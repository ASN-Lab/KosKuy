<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Category;
use App\Models\KosFacility;
use App\Models\KosListing;
use App\Models\KosRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $kosListings = $user->kosListings()->with(['rooms', 'facilities', 'images'])->get();
        $kosIds = $kosListings->pluck('id');
        $bookings = Booking::whereIn('kos_id', $kosIds)->with(['user', 'kos'])->latest()->get();

        return view('owner.dashboard', compact('kosListings', 'bookings'));
    }

    public function createKos()
    {
        return view('owner.create-kos');
    }

    public function storeKos(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_month' => 'required|numeric|min:0',
            'address' => 'required|string',
            'gender_type' => 'required|in:putra,putri,campur',
            'is_available' => 'sometimes',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data['owner_id'] = auth()->id();
        $data['is_available'] = $request->has('is_available');

        DB::transaction(function () use ($data, $request) {
            $kos = KosListing::create($data);

            $facilities = array_filter(array_map('trim', explode(',', $request->input('facilities', ''))));
            foreach ($facilities as $facilityName) {
                if ($facilityName !== '') {
                    $kos->facilities()->create([
                        'facility_name' => $facilityName,
                        'condition_status' => 'normal',
                    ]);
                }
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('kos', 'public');
                    $kos->images()->create(['image_path' => $path]);
                }
            }
        });

        return redirect()->route('owner.dashboard')->with('success', 'Listing kos berhasil ditambahkan.');
    }

    public function editKos(KosListing $kos)
    {
        $this->authorizeOwner($kos);
        return view('owner.edit-kos', compact('kos'));
    }

    public function updateKos(Request $request, KosListing $kos)
    {
        $this->authorizeOwner($kos);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_month' => 'required|numeric|min:0',
            'address' => 'required|string',
            'gender_type' => 'required|in:putra,putri,campur',
            'is_available' => 'sometimes',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data['is_available'] = $request->has('is_available');

        DB::transaction(function () use ($data, $request, $kos) {
            $kos->update($data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('kos', 'public');
                    $kos->images()->create(['image_path' => $path]);
                }
            }
        });

        return redirect()->route('owner.dashboard')->with('success', 'Listing kos berhasil diperbarui.');
    }

    public function storeRoom(Request $request)
    {
        $data = $request->validate([
            'kos_id' => 'required|exists:kos_listings,id',
            'room_name' => 'required|string|max:255',
            'tenant_name' => 'nullable|string|max:255',
        ]);

        $kos = auth()->user()->kosListings()->findOrFail($data['kos_id']);

        DB::transaction(function () use ($kos, $data) {
            $room = $kos->rooms()->create($data);
            $room->update(['is_occupied' => filled($data['tenant_name'] ?? null)]);
        });

        return back()->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function updateRoom(Request $request, KosRoom $room)
    {
        $this->authorizeOwner($room->kos);

        $data = $request->validate([
            'room_name' => 'required|string|max:255',
            'tenant_name' => 'nullable|string|max:255',
            'is_occupied' => 'sometimes',
        ]);

        $data['is_occupied'] = $request->has('is_occupied');
        $room->update($data);

        return back()->with('success', 'Kamar diperbarui.');
    }

    public function deleteRoom(KosRoom $room)
    {
        $this->authorizeOwner($room->kos);
        $room->delete();

        return back()->with('success', 'Kamar dihapus.');
    }

    public function updateFacility(Request $request, KosFacility $facility)
    {
        $this->authorizeOwner($facility->kos);

        $data = $request->validate([
            'condition_status' => 'required|in:normal,under_repair',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($facility, $data) {
            $facility->update([
                'condition_status' => $data['condition_status'],
                'last_updated_by' => auth()->id(),
            ]);
            $facility->kos->conditionUpdates()->create([
                'subject_type' => get_class($facility),
                'subject_id' => $facility->id,
                'status' => $data['condition_status'],
                'notes' => $data['notes'] ?? null,
                'updated_by' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Kondisi fasilitas diperbarui.');
    }

    public function updateBookingStatus(Request $request, Booking $booking)
    {
        $this->authorizeOwner($booking->kos);

        $data = $request->validate([
            'status' => 'required|in:pending,confirmed,canceled',
        ]);
        $booking->update(['status' => $data['status']]);

        $booking->user->notify(
            'booking_status',
            'Status Booking Diperbarui',
            "Booking Anda untuk {$booking->kos->title} kini berstatus {$data['status']}."
        );

        return back()->with('success', 'Status booking diperbarui.');
    }

    private function authorizeOwner($kos): void
    {
        if ($kos->owner_id !== auth()->id()) {
            abort(403);
        }
    }
}
