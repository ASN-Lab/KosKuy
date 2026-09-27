<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\KosResource;
use App\Models\KosListing;
use Illuminate\Http\Request;

class KosController extends Controller
{
    public function index(Request $request)
    {
        $query = KosListing::where('is_available', true)->with(['images', 'facilities', 'owner']);

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

        $listings = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar kos berhasil diambil',
            'data' => KosResource::collection($listings)->response()->getData(true),
        ]);
    }

    public function show($id)
    {
        $kos = KosListing::with(['images', 'facilities', 'owner'])
            ->where('is_available', true)
            ->find($id);

        if (!$kos) {
            return response()->json([
                'success' => false,
                'message' => 'Kos tidak ditemukan',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kos berhasil diambil',
            'data' => new KosResource($kos),
        ]);
    }
}
