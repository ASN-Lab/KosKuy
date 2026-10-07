@extends('layouts.app')

@section('title', 'Dashboard Pemilik Kos | KosKuy')

@section('content')
<div class="container-page py-12">
    <div class="flex items-center justify-between">
        <h1 class="heading-xl">Dashboard Pemilik</h1>
        <div class="flex gap-2">
            <a href="{{ route('owner.kos.create') }}" class="btn btn-brand">Tambah Kos</a>
        </div>
    </div>
    
    <div class="mt-8 grid gap-6">
        <div class="card p-6">
            <h2 class="font-bold text-lg">Listing Kos Saya</h2>
            <ul class="mt-4 space-y-2">
                @foreach($kosListings as $kos)
                    <li class="flex justify-between items-center p-3 border rounded">
                        <span class="font-semibold">{{ $kos->title }}</span>
                        <div class="flex gap-2">
                            <a href="{{ route('owner.kos.monitoring', $kos->id) }}" class="text-brand-600 font-semibold hover:underline">Monitoring</a>
                            <a href="{{ route('owner.kos.edit', $kos->id) }}" class="text-ink-500 hover:underline">Edit</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        
        <div class="card p-6">
            <h2 class="font-bold text-lg">Booking Masuk</h2>
            <div class="overflow-x-auto mt-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-ink-500">
                            <th class="p-2">Penyewa</th>
                            <th class="p-2">Kos</th>
                            <th class="p-2">Tanggal</th>
                            <th class="p-2">Status</th>
                            <th class="p-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr class="border-b">
                                <td class="p-2 font-medium">{{ $booking->user->name }}</td>
                                <td class="p-2">{{ $booking->kos->title }}</td>
                                <td class="p-2">{{ $booking->created_at->format('d M Y') }}</td>
                                <td class="p-2">
                                    <span class="badge {{ $booking->status === 'confirmed' ? 'bg-accent-50 text-accent-700' : ($booking->status === 'canceled' ? 'bg-rose-50 text-rose-700' : 'bg-sun-50 text-sun-700') }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="p-2">
                                    <form action="{{ route('owner.bookings.status', $booking->id) }}" method="POST" class="flex gap-2">
                                        @csrf @method('PATCH')
                                        <select name="status" class="field text-xs py-1" onchange="this.form.submit()">
                                            <option value="pending" @selected($booking->status === 'pending')>Pending</option>
                                            <option value="confirmed" @selected($booking->status === 'confirmed')>Confirmed</option>
                                            <option value="canceled" @selected($booking->status === 'canceled')>Canceled</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-ink-400">Belum ada booking masuk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
