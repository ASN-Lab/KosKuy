<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\ThriftProduct;
use App\Models\ThriftPurchase;
use Illuminate\Http\Request;

class ThriftPurchaseController extends Controller
{
    public function myPurchases(Request $request)
    {
        $purchases = ThriftPurchase::with('product:id,title,price')
            ->where('buyer_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn (ThriftPurchase $p) => [
                'id' => $p->id,
                'status' => $p->status,
                'amount' => (float) $p->amount,
                'product' => $p->product ? [
                    'id' => $p->product->id,
                    'title' => $p->product->title,
                    'price' => (float) $p->product->price,
                ] : null,
                'created_at' => $p->created_at?->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pembelian berhasil diambil',
            'data' => $purchases,
        ]);
    }

    public function purchase(Request $request, int $id)
    {
        $product = ThriftProduct::find($id);

        if (! $product || $product->is_sold) {
            return response()->json([
                'success' => false,
                'message' => 'Produk thrift tidak ditemukan',
                'data' => null,
            ], 404);
        }

        if ((int) $product->seller_id === (int) $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa membeli barang sendiri',
                'data' => null,
            ], 422);
        }

        $purchase = ThriftPurchase::create([
            'buyer_id' => $request->user()->id,
            'thrift_product_id' => $product->id,
            'amount' => $product->price,
            'status' => 'completed',
        ]);

        $product->update(['is_sold' => true]);

        AppNotification::create([
            'user_id' => $product->seller_id,
            'title' => 'Barang thrift terjual',
            'body' => $request->user()->name . ' membeli ' . $product->title,
            'type' => 'purchase',
        ]);

        AppNotification::create([
            'user_id' => $request->user()->id,
            'title' => 'Pembelian berhasil',
            'body' => 'Kamu membeli ' . $product->title,
            'type' => 'purchase',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembelian berhasil',
            'data' => [
                'id' => $purchase->id,
                'status' => $purchase->status,
            ],
        ]);
    }
}
