<?php

namespace App\Http\Controllers;

use App\Models\KosListing;
use App\Models\ThriftProduct;

class HomeController extends Controller
{
    public function index()
    {
        $kosListings = KosListing::where('is_available', true)
            ->with(['images', 'owner'])
            ->latest()
            ->take(6)
            ->get();

        $thriftProducts = ThriftProduct::where('is_sold', false)
            ->with(['images', 'category', 'seller'])
            ->latest()
            ->take(8)
            ->get();

        return view('home.index', compact('kosListings', 'thriftProducts'));
    }
}
