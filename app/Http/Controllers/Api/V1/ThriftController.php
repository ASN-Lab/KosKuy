<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ThriftProductResource;
use App\Models\ThriftProduct;
use Illuminate\Http\Request;

class ThriftController extends Controller
{
    public function index(Request $request)
    {
        $query = ThriftProduct::where('is_sold', false)->with(['images', 'category', 'seller']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar produk thrift berhasil diambil',
            'data' => ThriftProductResource::collection($products)->response()->getData(true),
        ]);
    }

    public function show($id)
    {
        $product = ThriftProduct::with(['images', 'category', 'seller'])
            ->where('is_sold', false)
            ->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk thrift tidak ditemukan',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail produk thrift berhasil diambil',
            'data' => new ThriftProductResource($product),
        ]);
    }
}
