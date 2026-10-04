<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\KosConditionUpdate;
use App\Models\KosFacility;
use App\Models\KosListing;
use App\Models\KosRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KosMonitoringController extends Controller
{
    public function show($kosId)
    {
        $kos = KosListing::with(['rooms', 'facilities.lastUpdater', 'conditionUpdates.updater'])
            ->findOrFail($kosId);

        if ($kos->owner_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $kos,
        ]);
    }

    public function storeRoom(Request $request, $kosId)
    {
        $kos = KosListing::findOrFail($kosId);
        $this->authorizeOwner($kos);

        $validator = Validator::make($request->all(), [
            'room_name' => 'required|string|max:255',
            'tenant_name' => 'nullable|string|max:255',
            'is_occupied' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $room = $kos->rooms()->create([
            'room_name' => $request->room_name,
            'tenant_name' => $request->tenant_name,
            'is_occupied' => $request->has('is_occupied') ?: filled($request->tenant_name),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kamar berhasil ditambahkan',
            'data' => $room,
        ], 201);
    }

    public function updateRoom(Request $request, $kosId, $roomId)
    {
        $kos = KosListing::findOrFail($kosId);
        $this->authorizeOwner($kos);

        $room = $kos->rooms()->findOrFail($roomId);

        $validator = Validator::make($request->all(), [
            'room_name' => 'required|string|max:255',
            'tenant_name' => 'nullable|string|max:255',
            'is_occupied' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $room->update([
            'room_name' => $request->room_name,
            'tenant_name' => $request->tenant_name,
            'is_occupied' => $request->has('is_occupied'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kamar diperbarui',
            'data' => $room->fresh(),
        ]);
    }

    public function deleteRoom($kosId, $roomId)
    {
        $kos = KosListing::findOrFail($kosId);
        $this->authorizeOwner($kos);

        $room = $kos->rooms()->findOrFail($roomId);
        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kamar dihapus',
        ]);
    }

    public function updateFacility(Request $request, $kosId, $facilityId)
    {
        $kos = KosListing::findOrFail($kosId);
        $this->authorizeOwner($kos);

        $facility = $kos->facilities()->findOrFail($facilityId);

        $validator = Validator::make($request->all(), [
            'condition_status' => 'required|in:normal,under_repair',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::transaction(function () use ($kos, $facility, $request) {
            $facility->update([
                'condition_status' => $request->condition_status,
                'last_updated_by' => auth()->id(),
            ]);
            $kos->conditionUpdates()->create([
                'subject_type' => get_class($facility),
                'subject_id' => $facility->id,
                'status' => $request->condition_status,
                'notes' => $request->notes,
                'updated_by' => auth()->id(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Kondisi fasilitas diperbarui',
            'data' => $facility->fresh()->load('lastUpdater'),
        ]);
    }

    public function conditionUpdates($kosId)
    {
        $kos = KosListing::findOrFail($kosId);
        $this->authorizeOwner($kos);

        $updates = $kos->conditionUpdates()->with('updater')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $updates,
        ]);
    }

    private function authorizeOwner(KosListing $kos): void
    {
        if ($kos->owner_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
    }
}