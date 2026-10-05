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

        $kos = KosListing::find($request->kos_id);
        if (!$kos->is_available) {
            return response()->json(['success' => false, 'message' => 'Kos tidak tersedia'], 400);
        }

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'kos_id' => $request->kos_id,
            'booking_date' => $request->booking_date,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        $kos->owner->notify(
            'booking',
            'Booking Baru',
            "Mahasiswa {$request->user()->name} memesan {$kos->title} untuk tanggal {$request->booking_date}."
        );

        $ownerPhone = $kos->owner->phone;
        if ($ownerPhone) {
            $message = "Halo, ada booking baru untuk {$kos->title} pada {$request->booking_date}.\n\nKlik link untuk konfirmasi: http://127.0.0.1:8000/admin/bookings";
            $whatsappUrl = "https://wa.me/$ownerPhone?text=" . urlencode($message);
            // Catat notifikasi untuk dikirim nanti via job (sederhana: direct request)
            // Untuk MVP, kita catat log saja
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibuat',
            'data' => $booking->load(['user', 'kos']),
        ], 201);
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
