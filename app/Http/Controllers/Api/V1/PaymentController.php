<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\ThriftProduct;
use App\Services\PaymentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService)
    {
    }

    /**
     * Pilih metode pembayaran untuk booking atau thrust.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:booking,thrift',
            'id' => 'required|integer',
            'method' => 'required|in:' . Payment::METHOD_TRANSFER . ',' . Payment::METHOD_CASH,
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $payable = $this->findPayable($request->type, $request->user()->id, $request->id);
        if (!$payable) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
        }

        $owner = $this->payableOwner($payable);
        $amount = $request->type === 'booking'
            ? $payable->kos->price_per_month
            : $payable->price;

        $payment = $this->paymentService->choose($payable, $request->method, $amount);

        return response()->json([
            'success' => true,
            'data' => $payment,
            'owner_bank' => $payment->method === Payment::METHOD_TRANSFER ? $this->bankOf($owner) : null,
            'total_transfer' => $payment->amount + $payment->unique_code,
        ]);
    }

    /**
     * Upload bukti transfer.
     */
    public function uploadProof(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:booking,thrift',
            'proof' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $payable = $this->findPayable($request->type, $request->user()->id, $id);
        if (!$payable) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
        }

        $payment = $payable->payment;
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Pembayaran belum dimulai'], 404);
        }

        if ($payment->method !== Payment::METHOD_TRANSFER) {
            return response()->json(['success' => false, 'message' => 'Metode COD tidak menerima bukti transfer'], 422);
        }

        $path = $request->file('proof')->store('payment-proofs', 'public');
        $payment->update([
            'proof_path' => $path,
            'status' => Payment::STATUS_PENDING_VERIFICATION,
        ]);

        $this->payableOwner($payable)?->notify(
            'payment_proof',
            'Bukti Transfer Baru',
            "Bukti transfer pembayaran #{$payment->id} menunggu verifikasi Anda.",
            ['payment_id' => $payment->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran diunggah, menunggu verifikasi.',
            'data' => $payment,
        ]);
    }

    /**
     * Detail status pembayaran berdasarkan payable.
     */
    public function show(Request $request, string $type, int $id)
    {
        if (!in_array($type, ['booking', 'thrift'])) {
            return response()->json(['success' => false, 'message' => 'Tipe transaksi tidak valid'], 422);
        }

        $payable = $this->findPayable($type, $request->user()->id, $id);
        if (!$payable) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
        }

        $payment = $payable->payment;
        if (!$payment) {
            return response()->json(['success' => true, 'data' => null]);
        }

        return response()->json([
            'success' => true,
            'data' => $payment,
            'owner_bank' => $payment->method === Payment::METHOD_TRANSFER
                ? $this->bankOf($this->payableOwner($payable))
                : null,
            'total_transfer' => $payment->amount + $payment->unique_code,
        ]);
    }

    /**
     * Cari transaksi milik user saat ini.
     */
    private function findPayable(string $type, int $userId, int $id): ?Model
    {
        return $type === 'thrift'
            ? ThriftProduct::with('seller')->where('buyer_id', $userId)->find($id)
            : Booking::with('kos.owner')->where('user_id', $userId)->find($id);
    }

    private function payableOwner(Model $payable): ?\App\Models\User
    {
        return $payable instanceof Booking
            ? $payable->kos?->owner
            : $payable->seller;
    }

    /**
     * @return array{bank_name: ?string, bank_number: ?string, bank_holder: ?string}|null
     */
    private function bankOf(?\App\Models\User $owner): ?array
    {
        if (!$owner) {
            return null;
        }

        return [
            'bank_name' => $owner->bank_name,
            'bank_number' => $owner->bank_number,
            'bank_holder' => $owner->bank_holder,
        ];
    }
}