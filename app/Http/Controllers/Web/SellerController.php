<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ThriftProduct;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $products = $user->thriftProducts()->with(['category', 'images', 'buyer'])->latest()->get();
        $categories = Category::all();

        $stats = [
            'total_products' => $products->count(),
            'total_sold' => $products->where('is_sold', true)->count(),
            'total_revenue' => $products->where('is_sold', true)->sum('price'),
        ];

        return view('seller.dashboard', compact('products', 'categories', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'condition_status' => 'required|string|max:50',
        ]);

        auth()->user()->thriftProducts()->create($data);

        return back()->with('success', 'Produk berhasil ditambahkan.');
    }

    public function toggleSold(Request $request, ThriftProduct $product)
    {
        $this->authorizeSeller($product);

        $product->update(['is_sold' => !$product->is_sold]);

        return back()->with('success', $product->is_sold ? 'Produk ditandai terjual.' : 'Produk dikembalikan ke daftar aktif.');
    }

    public function destroy(ThriftProduct $product)
    {
        $this->authorizeSeller($product);
        $product->delete();

        return back()->with('success', 'Produk dihapus.');
    }

    private function authorizeSeller(ThriftProduct $product): void
    {
        if ($product->seller_id !== auth()->id()) {
            abort(403);
        }
    }
}