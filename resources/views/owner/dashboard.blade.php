@extends('layouts.app')

@section('title', 'Dashboard Pemilik Kos | KosKuy')

@section('content')
<div class="container-page py-12">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <span class="badge badge-brand mb-2">Dashboard Pemilik</span>
            <h1 class="heading-xl">Kelola Kos</h1>
            <p class="text-ink-500 mt-1">Pantau okupansi dan status booking kamar kosmu.</p>
        </div>
        <a href="{{ route('owner.kos.create') }}" class="btn btn-brand">
            <x-ui.icon name="plus-circle" class="size-4" />
            Tambah Kos
        </a>
    </div>

    {{-- Stats Row --}}
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-2xl bg-brand-50 text-brand-600">
                    <x-ui.icon name="building-office-2" class="size-5" />
                </span>
                <div>
                    <p class="text-xs font-semibold text-ink-400">Total Unit</p>
                    <p class="font-display text-2xl font-extrabold text-ink-900">{{ $kosListings->count() }}</p>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-2xl bg-accent-50 text-accent-600">
                    <x-ui.icon name="check-badge" class="size-5" />
                </span>
                <div>
                    <p class="text-xs font-semibold text-ink-400">Total Booking</p>
                    <p class="font-display text-2xl font-extrabold text-ink-900">{{ $bookings->count() }}</p>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-2xl bg-sun-50 text-sun-600">
                    <x-ui.icon name="clock" class="size-5" />
                </span>
                <div>
                    <p class="text-xs font-semibold text-ink-400">Booking Pending</p>
                    <p class="font-display text-2xl font-extrabold text-ink-900">{{ $bookings->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="card p-6">
            <h2 class="font-bold text-lg text-ink-900">Listing Kos Saya</h2>
            <ul class="mt-4 space-y-3">
                @foreach($kosListings as $kos)
                    <li class="flex justify-between items-center p-4 border border-ink-100 rounded-2xl hover:bg-ink-50 transition">
                        <span class="font-semibold text-sm">{{ $kos->title }}</span>
                        <div class="flex gap-3">
                            <a href="{{ route('owner.kos.monitoring', $kos->id) }}" class="text-xs font-bold text-brand-600 hover:underline">Monitoring</a>
                            <a href="{{ route('owner.kos.edit', $kos->id) }}" class="text-xs text-ink-500 hover:underline">Edit</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        
        <div class="card p-6">
            <h2 class="font-bold text-lg text-ink-900">Booking Masuk</h2>
            <div class="overflow-x-auto mt-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-ink-400 text-xs uppercase tracking-wider">
                            <th class="p-2">Penyewa</th>
                            <th class="p-2">Kos</th>
                            <th class="p-2">Status</th>
                            <th class="p-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr class="border-t border-ink-50">
                                <td class="p-2 font-medium">{{ $booking->user->name }}</td>
                                <td class="p-2 text-ink-600">{{ $booking->kos->title }}</td>
                                <td class="p-2">
                                    <span class="badge {{ $booking->status === 'confirmed' ? 'bg-accent-50 text-accent-700' : ($booking->status === 'canceled' ? 'bg-rose-50 text-rose-700' : 'bg-sun-50 text-sun-700') }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="p-2 text-right">
                                    <form action="{{ route('owner.bookings.status', $booking->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <select name="status" class="field text-xs" onchange="this.form.submit()">
                                            <option value="pending" @selected($booking->status === 'pending')>Pending</option>
                                            <option value="confirmed" @selected($booking->status === 'confirmed')>Confirmed</option>
                                            <option value="canceled" @selected($booking->status === 'canceled')>Canceled</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-center text-ink-400">Belum ada booking masuk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
