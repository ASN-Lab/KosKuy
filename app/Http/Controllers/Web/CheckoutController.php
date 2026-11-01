<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ThriftProduct;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(protected PaymentService $paymentService)
    {
    }

    /**
     * Halaman checkout: form pilih metode, atau detail bayar bila sudah ada.
     */
    public function show($id)
    {
        $product = ThriftProduct::with(['images', 'seller'])->findOrFail($id);

        $payment = $product->payment()->first();

        // Barang sudah dibeli orang lain.
        if (! $payment && $product->is_sold && (int) $product->buyer_id !== (int) auth()->id()) {
            abort(410, 'Barang sudah terjual.');
        }

        return view('thrift.checkout', compact('product', 'payment'));
    }

    /**
     * Konfirmasi pesanan: reserve barang + buat record pembayaran.
     */
    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'method' => 'required|in:' . Payment::METHOD_TRANSFER . ',' . Payment::METHOD_CASH,
        ]);

        $product = ThriftProduct::with('seller')->findOrFail($id);

        if ($product->is_sold && (int) $product->buyer_id !== (int) auth()->id()) {
            abort(410, 'Barang sudah terjual.');
        }

        if ($product->is_sold && $product->payment) {
            // Sudah pernah konfirmasi -> kembalikan ke halaman bayar yang sama.
            return redirect()->route('thrift.checkout', $product->id);
        }

        $product->update([
            'is_sold' => true,
            'buyer_id' => auth()->id(),
        ]);

        $this->paymentService->choose($product, $validated['method'], $product->price);

        $product->seller?->notify(
            'purchase',
            'Pesanan Baru',
            "Barang {$product->title} dipesan. Menunggu pembayaran dari pembeli."
        );

        return redirect()
            ->route('thrift.checkout', $product->id)
            ->with('success', 'Pesanan dikonfirmasi. Silakan lakukan pembayaran.');
    }

    /**
     * Upload bukti transfer -> pending_verification.
     */
    public function storeProof(Request $request, $id)
    {
        $request->validate([
            'proof' => 'required|image|max:2048',
        ]);

        $product = ThriftProduct::with('seller')->findOrFail($id);
        $payment = $product->payment()->first();

        abort_if(! $payment, 404, 'Pembayaran belum dimulai.');

        if ($payment->method !== Payment::METHOD_TRANSFER) {
            return back()->with('error', 'Metode COD tidak menerima bukti transfer.');
        }

        if ($payment->proof_path) {
            return back()->with('error', 'Bukti sudah dikirim, menunggu verifikasi admin.');
        }

        $payment->update([
            'proof_path' => $request->file('proof')->store('payment-proofs', 'public'),
            'status' => Payment::STATUS_PENDING_VERIFICATION,
        ]);

        $product->seller?->notify(
            'payment_proof',
            'Bukti Transfer Baru',
            "Bukti transfer pembayaran #{$payment->id} menunggu verifikasi.",
            ['payment_id' => $payment->id]
        );

        return back()->with('success', 'Bukti pembayaran terkirim, menunggu verifikasi admin.');
    }
}