<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Booking;
use App\Models\KosListing;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function myBookings(Request $request)
    {
        $bookings = Booking::with('kosListing:id,title')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn (Booking $b) => [
                'id' => $b->id,
                'status' => $b->status,
                'booking_date' => $b->booking_date instanceof \DateTimeInterface
                    ? $b->booking_date->format('Y-m-d')
                    : (string) $b->booking_date,
                'notes' => $b->notes,
                'kos' => $b->kosListing ? [
                    'id' => $b->kosListing->id,
                    'title' => $b->kosListing->title,
                ] : null,
                'created_at' => $b->created_at?->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat booking berhasil diambil',
            'data' => $bookings,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'kos_id' => ['required', 'integer', 'exists:kos_listings,id'],
                'booking_date' => ['required', 'date'],
                'notes' => ['nullable', 'string'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan booking. Periksa kembali data yang diisi.',
                'data' => $e->errors(),
            ], 422);
        }

        $kos = KosListing::find($validated['kos_id']);

        if (! $kos || ! $kos->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'Kos tidak tersedia untuk booking',
                'data' => null,
            ], 422);
        }

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'kos_listing_id' => $kos->id,
            'booking_date' => $validated['booking_date'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        AppNotification::create([
            'user_id' => $kos->owner_id,
            'title' => 'Booking baru masuk',
            'body' => $request->user()->name . ' mengajukan booking untuk ' . $kos->title,
            'type' => 'booking',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dikirim',
            'data' => [
                'id' => $booking->id,
                'status' => $booking->status,
            ],
        ], 201);
    }

    public function cancel(Request $request, int $id)
    {
        $booking = Booking::with('kosListing')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan',
                'data' => null,
            ], 404);
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya booking pending yang bisa dibatalkan',
                'data' => null,
            ], 422);
        }

        $booking->update(['status' => 'canceled']);

        if ($booking->kosListing) {
            AppNotification::create([
                'user_id' => $booking->kosListing->owner_id,
                'title' => 'Booking dibatalkan',
                'body' => $request->user()->name . ' membatalkan booking untuk ' . $booking->kosListing->title,
                'type' => 'booking_status',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking dibatalkan',
            'data' => null,
        ]);
    }
}
