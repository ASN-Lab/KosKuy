<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ThriftProductResource;
use App\Models\Category;
use App\Models\ThriftImage;
use App\Models\ThriftProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ThriftProductController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->role !== 'seller' && $request->user()->role !== 'owner') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'condition_status' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'is_sold' => 'boolean',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $product = ThriftProduct::create([
            'seller_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'condition_status' => $request->condition_status,
            'is_sold' => $request->get('is_sold', false),
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('thrift', 'public');
                ThriftImage::create([
                    'thrift_id' => $product->id,
                    'image_path' => $path,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk thrift berhasil dibuat',
            'data' => new ThriftProductResource($product->load(['images', 'category', 'seller'])),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = ThriftProduct::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        if ($request->user()->id !== $product->seller_id && $request->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'condition_status' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'is_sold' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $product->update($request->only([
            'title', 'description', 'price', 'condition_status', 'category_id', 'is_sold'
        ]));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('thrift', 'public');
                ThriftImage::create([
                    'thrift_id' => $product->id,
                    'image_path' => $path,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk thrift berhasil diupdate',
            'data' => new ThriftProductResource($product->load(['images', 'category', 'seller'])),
        ]);
    }

    public function purchase(Request $request, $id)
    {
        $product = ThriftProduct::where('is_sold', false)->findOrFail($id);

        $product->update([
            'is_sold' => true,
            'buyer_id' => $request->user()->id,
        ]);

        $product->seller->notify(
            'purchase',
            'Produk Terjual',
            "Produk {$product->title} Anda telah dibeli."
        );

        return response()->json(['success' => true, 'message' => 'Pembelian berhasil']);
    }

    public function myPurchases(Request $request)
    {
        $purchases = ThriftProduct::where('buyer_id', $request->user()->id)
            ->with(['images', 'category', 'seller'])
            ->latest()
            ->get();

        return response()->json(['success' => true, 'data' => $purchases]);
    }

    public function destroy(Request $request, $id)
    {
        $product = ThriftProduct::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        if ($request->user()->id !== $product->seller_id && $request->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk thrift berhasil dihapus',
        ]);
    }
}
