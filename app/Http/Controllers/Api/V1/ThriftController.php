<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ThriftProductResource;
use App\Models\ThriftImage;
use App\Models\ThriftProduct;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

        match ($request->input('sort', 'terbaru')) {
            'termurah' => $query->orderBy('price'),
            'termahal' => $query->orderByDesc('price'),
            'nama' => $query->orderBy('title'),
            default => $query->latest(),
        };

        $products = $query->paginate(10);

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

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'price' => ['required', 'numeric', 'min:0'],
                'category_id' => ['required', 'integer', 'exists:categories,id'],
                'condition_status' => ['required', 'string', 'max:100'],
                'images' => ['nullable', 'array', 'max:5'],
                'images.*' => ['image', 'max:2048'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah barang. Periksa kembali data yang diisi.',
                'data' => $e->errors(),
            ], 422);
        }

        $product = ThriftProduct::create([
            'seller_id' => $request->user()->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'price' => $validated['price'],
            'condition_status' => $validated['condition_status'],
            'is_sold' => false,
        ]);

        foreach ($request->file('images', []) as $image) {
            $path = $image->store('thrift', 'public');
            ThriftImage::create([
                'thrift_id' => $product->id,
                'image_path' => $path,
            ]);
        }

        $product->load(['images', 'category', 'seller']);

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil diunggah',
            'data' => new ThriftProductResource($product),
        ], 201);
    }
}
