<?php

namespace App\Http\Controllers;

use App\Models\Category;
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

        $products = $query->latest()->paginate(9);
        $categories = Category::orderBy('name')->get();

        return view('thrift.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = ThriftProduct::with(['images', 'category', 'seller'])
            ->where('is_sold', false)
            ->findOrFail($id);

        return view('thrift.show', compact('product'));
    }
}
