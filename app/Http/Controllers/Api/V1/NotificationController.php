<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->limit(50)->get();

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    public function markRead(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true, 'message' => 'Notifikasi dibaca']);
    }
}