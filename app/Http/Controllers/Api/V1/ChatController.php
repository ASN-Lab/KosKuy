<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        return Conversation::with(['student', 'owner', 'listing'])
            ->where('student_id', $userId)
            ->orWhere('owner_id', $userId)
            ->latest('last_message_at')
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'listing_type' => 'required|in:kos,thrift',
            'listing_id' => 'required|integer',
            'owner_id' => 'required|exists:users,id',
        ]);

        $conversation = Conversation::firstOrCreate([
            'student_id' => $request->user()->id,
            'owner_id' => $request->owner_id,
            'listing_type' => $request->listing_type,
            'listing_id' => $request->listing_id,
        ]);

        return $conversation;
    }

    public function messages(Request $request, Conversation $conversation)
    {
        $this->authorizeAccess($request->user(), $conversation);

        return $conversation->messages()->with('sender')->get();
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $this->authorizeAccess($request->user(), $conversation);

        $request->validate(['body' => 'required|string']);

        $message = $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => $request->body,
        ]);

        $conversation->update(['last_message_at' => now()]);

        broadcast(new MessageSent($message))->toOthers();

        return $message;
    }

    private function authorizeAccess($user, Conversation $conversation)
    {
        if ($user->id !== $conversation->student_id && $user->id !== $conversation->owner_id) {
            abort(403);
        }
    }
}