<?php

namespace App\Http\Controllers\Web;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $conversations = Conversation::with(['student', 'owner', 'listing'])
            ->where('student_id', $userId)
            ->orWhere('owner_id', $userId)
            ->latest('last_message_at')
            ->get();

        return view('student.messages', compact('conversations'));
    }

    public function show(Request $request, Conversation $conversation)
    {
        $this->authorizeAccess($request->user(), $conversation);

        $conversation->load(['messages.sender', 'student', 'owner', 'listing']);
        $otherUser = $request->user()->id === $conversation->student_id ? $conversation->owner : $conversation->student;

        return view('student.conversation', compact('conversation', 'otherUser'));
    }

    /**
     * Mulai (atau ambil) percakapan antara user aktif dan pemilik listing.
     */
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

        return redirect()->route('messages.show', $conversation);
    }

    /**
     * Kirim pesan baru dari halaman web.
     */
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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($message->load('sender'));
        }

        return redirect()->route('messages.show', $conversation);
    }

    private function authorizeAccess($user, Conversation $conversation)
    {
        if ($user->id !== $conversation->student_id && $user->id !== $conversation->owner_id) {
            abort(403);
        }
    }
}
