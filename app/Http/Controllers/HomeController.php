<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\KosListing;
use App\Models\ThriftProduct;
use App\Support\StorefrontCache;

class HomeController extends Controller
{
    public function index()
    {
        $data = StorefrontCache::remember('home', 'index', function () {
            $kosListings = KosListing::where('is_available', true)
                ->with(['images', 'facilities', 'owner'])
                ->latest()
                ->take(6)
                ->get();

            $thriftProducts = ThriftProduct::where('is_sold', false)
                ->with(['images', 'category', 'seller'])
                ->latest()
                ->take(8)
                ->get();

            $categories = Category::orderBy('name')->get();

            $stats = [
                'kos' => KosListing::where('is_available', true)->count(),
                'thrift' => ThriftProduct::where('is_sold', false)->count(),
                'categories' => $categories->count(),
            ];

            return compact('kosListings', 'thriftProducts', 'categories', 'stats');
        });

        return view('home.index', $data);
    }
}