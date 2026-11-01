@extends('layouts.app')

@section('title', 'Pesan | KosKuy')

@section('content')
<div class="container-page py-12">
    <div class="flex items-center justify-between">
        <h1 class="heading-xl">Pesan</h1>
    </div>

    <div class="mt-8 grid gap-6">
        <div class="card p-6">
            @forelse ($conversations as $conversation)
                @php
                    $other = $conversation->student_id === auth()->id() ? $conversation->owner : $conversation->student;
                    $listing = $conversation->listing;
                @endphp
                <a href="{{ route('messages.show', $conversation) }}" class="flex items-center gap-4 border-b border-ink-100 py-4 last:border-0 hover:bg-ink-50/60 transition">
                    <span class="grid size-12 shrink-0 place-items-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">
                        {{ $other ? initials($other->name) : '?' }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-bold text-ink-900">{{ $other?->name ?? 'Pengguna' }}</p>
                        <p class="truncate text-sm text-ink-500">
                            @if ($listing)
                                {{ $conversation->listing_type === 'kos' ? 'Kos' : 'Thrift' }}: {{ $listing->title }}
                            @endif
                        </p>
                    </div>
                    <span class="text-xs text-ink-400">
                        {{ $conversation->last_message_at?->diffForHumans() ?? 'Baru' }}
                    </span>
                    <x-ui.icon name="chevron-right" class="size-5 text-ink-300" />
                </a>
            @empty
                <p class="py-8 text-center text-ink-400">Belum ada percakapan. Mulai chat dari halaman Kos atau Thrift.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
