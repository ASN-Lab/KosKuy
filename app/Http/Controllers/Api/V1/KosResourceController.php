<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\KosResource;
use App\Models\KosImage;
use App\Models\KosListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KosResourceController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->role !== 'owner') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_month' => 'required|numeric',
            'gender_type' => 'required|in:putra,putri,campur',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $kos = KosListing::create([
            'owner_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'price_per_month' => $request->price_per_month,
            'gender_type' => $request->gender_type,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_available' => true,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('kos', 'public');
                KosImage::create([
                    'kos_id' => $kos->id,
                    'image_path' => $path,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Listing kos berhasil dibuat',
            'data' => new KosResource($kos->load(['images', 'facilities', 'owner'])),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $kos = KosListing::find($id);

        if (!$kos) {
            return response()->json(['success' => false, 'message' => 'Kos tidak ditemukan'], 404);
        }

        if ($request->user()->id !== $kos->owner_id && $request->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_month' => 'required|numeric',
            'gender_type' => 'required|in:putra,putri,campur',
            'address' => 'required|string',
            'is_available' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $kos->update($request->only([
            'title', 'description', 'price_per_month', 'gender_type', 'address', 'latitude', 'longitude', 'is_available'
        ]));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('kos', 'public');
                KosImage::create([
                    'kos_id' => $kos->id,
                    'image_path' => $path,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Listing kos berhasil diupdate',
            'data' => new KosResource($kos->load(['images', 'facilities', 'owner'])),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $kos = KosListing::find($id);

        if (!$kos) {
            return response()->json(['success' => false, 'message' => 'Kos tidak ditemukan'], 404);
        }

        if ($request->user()->id !== $kos->owner_id && $request->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        foreach ($kos->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        $kos->delete();

        return response()->json([
            'success' => true,
            'message' => 'Listing kos berhasil dihapus',
        ]);
    }
}
