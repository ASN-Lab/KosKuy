<?php

namespace App\Http\Controllers;

use App\Models\KosListing;
use Illuminate\Http\Request;

class KosController extends Controller
{
    public function index(Request $request)
    {
        $query = KosListing::where('is_available', true)->with(['images', 'owner']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('gender_type')) {
            $query->where('gender_type', $request->gender_type);
        }

        if ($request->filled('min_price')) {
            $query->where('price_per_month', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_month', '<=', $request->max_price);
        }

        $kosListings = $query->latest()->paginate(9);

        return view('kos.index', compact('kosListings'));
    }

    public function show($id)
    {
        $kos = KosListing::with(['images', 'facilities', 'owner'])
            ->where('is_available', true)
            ->findOrFail($id);

        return view('kos.show', compact('kos'));
    }
}
