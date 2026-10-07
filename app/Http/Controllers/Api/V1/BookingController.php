<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\KosListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kos_id' => 'required|exists:kos_listings,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login untuk melakukan booking.');
        }

        $kos = KosListing::find($request->kos_id);
        if (!$kos) {
            return response()->json(['success' => false, 'message' => 'Kos tidak ditemukan'], 404);
        }

        if (!$kos->is_available) {
            return response()->json(['success' => false, 'message' => 'Kos tidak tersedia'], 400);
        }

        // Cek apakah user sudah punya booking active/pending untuk kos yang sama
        $existing = Booking::where('user_id', $user->id)
            ->where('kos_id', $request->kos_id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existing) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah memiliki booking aktif atau pending untuk kos ini.',
                ], 409);
            }
            return redirect()->back()->with('error', 'Anda sudah memiliki booking aktif atau pending untuk kos ini.');
        }

        $booking = Booking::create([
            'user_id' => $user->id,
            'kos_id' => $request->kos_id,
            'booking_date' => $request->booking_date,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        $owner = $kos->owner;
        $owner->notify(
            'booking',
            'Booking Baru',
            "Mahasiswa {$user->name} memesan {$kos->title} untuk tanggal {$request->booking_date}."
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dibuat. Tunggu konfirmasi dari pemilik kos.',
                'data' => $booking->load(['user', 'kos']),
            ], 201);
        }

        return redirect()->back()->with('success', 'Booking berhasil dibuat! Tunggu konfirmasi dari pemilik kos.');
    }

    public function myBookings(Request $request)
    {
        $bookings = Booking::with(['user', 'kos'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking tidak ditemukan'], 404);
        }

        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $booking->update(['status' => 'canceled']);

        return response()->json([
            'success' => true,
            'message' => 'Booking dibatalkan',
        ]);
    }

    public function ownerBookings(Request $request)
    {
        $bookings = Booking::with(['user', 'kos.owner'])
            ->whereHas('kos', function ($query) use ($request) {
                $query->where('owner_id', $request->user()->id);
            })
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking tidak ditemukan'], 404);
        }

        $kos = $booking->kos;
        if ($kos->owner_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:confirmed,canceled',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $booking->update(['status' => $request->status]);

        $booking->user->notify(
            'booking_status',
            'Status Booking Diperbarui',
            "Booking Anda untuk {$booking->kos->title} kini berstatus {$request->status}."
        );

        return response()->json([
            'success' => true,
            'message' => 'Status booking diupdate',
            'data' => $booking->load(['user', 'kos']),
        ]);
    }
}
