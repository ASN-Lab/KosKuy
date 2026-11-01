@extends('layouts.app')

@section('title', 'Percakapan | KosKuy')

@section('content')
<div class="container-page py-10"
    x-data="{
        conversationId: {{ $conversation->id }},
        currentUserId: {{ auth()->id() }},
        messages: {{ Illuminate\Support\Js::from($conversation->messages) }},
        newMessage: '',
        init() {
            this.scrollToBottom();
            if (window.Echo) {
                window.Echo.private('conversation.' + this.conversationId)
                    .listen('MessageSent', (e) => {
                        this.messages.push(e.message);
                        this.scrollToBottom();
                    });
            }
        },
        async sendMessage() {
            if (!this.newMessage.trim()) return;
            const body = this.newMessage;
            this.newMessage = '';

            const res = await fetch('{{ route('messages.send', $conversation) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content
                },
                body: JSON.stringify({ body })
            });

            if (res.ok) {
                const msg = await res.json();
                this.messages.push(msg);
                this.scrollToBottom();
            }
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.chatContainer;
                if (el) el.scrollTop = el.scrollHeight;
            });
        }
    }"
>
    <div class="mb-6 flex items-center justify-between border-b border-ink-100 pb-4">
        <div>
            <a href="{{ route('messages.index') }}" class="text-sm font-semibold text-brand-600 hover:underline">&larr; Kembali ke Pesan</a>
            <h1 class="text-xl font-bold text-ink-900 mt-1">Percakapan dengan {{ $otherUser->name }}</h1>
        </div>
        @if($conversation->listing)
            <div class="text-right text-xs text-ink-500">
                <span class="font-semibold">{{ $conversation->listing_type === 'kos' ? 'Kos' : 'Thrift' }}:</span> 
                {{ $conversation->listing->title }}
            </div>
        @endif
    </div>

    <div class="card flex flex-col h-[60vh] p-4 bg-ink-50/50">
        <div class="flex-1 overflow-y-auto space-y-3 pr-2" x-ref="chatContainer">
            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.sender_id === currentUserId ? 'text-right' : 'text-left'">
                    <div :class="msg.sender_id === currentUserId ? 'bg-brand-600 text-white ml-auto' : 'bg-white text-ink-800 border border-ink-100'" 
                         class="inline-block rounded-2xl px-4 py-2.5 max-w-md text-sm shadow-sm">
                        <p x-text="msg.body"></p>
                        <span class="block text-[10px] opacity-75 mt-1 text-right" x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                    </div>
                </div>
            </template>
        </div>

        <form @submit.prevent="sendMessage" class="mt-4 flex gap-2">
            <input type="text" x-model="newMessage" placeholder="Ketik pesan..." class="field flex-1" required>
            <button type="submit" class="btn btn-brand">Kirim</button>
        </form>
    </div>
</div>
@endsection