@extends('layouts.dashboard', ['dashboardUrl' => route('owner.dashboard')])

@section('title', 'Dashboard Owner')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold">Dashboard Owner</h1>
    <span class="text-gray-500">Selamat datang, {{ auth()->user()->name }}</span>
</div>

@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded-lg mb-6">{{ session('success') }}</div>
@endif

<h2 class="text-xl font-bold mb-4">Kos Saya</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
    @foreach ($kosListings as $kos)
        @php
            $occupied = $kos->rooms->where('is_occupied', true)->count();
            $repairing = $kos->facilities->where('condition_status', 'under_repair')->count();
        @endphp
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-bold text-lg">{{ $kos->title }}</h3>
                <span class="inline-block px-2 py-1 rounded text-sm {{ $kos->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $kos->is_available ? 'Tersedia' : 'Tidak tersedia' }}
                </span>
            </div>
            <p class="text-indigo-600 font-bold mb-3">Rp {{ number_format($kos->price_per_month, 0, ',', '.') }} / bulan</p>
            <div class="space-y-1 text-sm text-gray-600 mb-4">
                <p>Kamar: <span class="font-semibold text-gray-800">{{ $occupied }} terisi / {{ $kos->rooms->count() }} total</span></p>
                <p>Fasilitas: <span class="font-semibold text-gray-800">{{ $kos->facilities->count() }}</span>
                    @if ($repairing > 0)
                        <span class="text-orange-600 font-semibold">( {{ $repairing }} dalam perbaikan )</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('owner.kos.monitoring', $kos) }}"
                class="block text-center bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                Kelola Monitoring &amp; Kamar
            </a>
        </div>
    @endforeach
</div>

<h2 class="text-xl font-bold mb-4">Booking Masuk</h2>
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
            <tr>
                <th class="px-4 py-3">Kos</th>
                <th class="px-4 py-3">Mahasiswa</th>
                <th class="px-4 py-3">Tanggal</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bookings as $booking)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ $booking->kos->title }}</td>
                    <td class="px-4 py-3">{{ $booking->user->name }}</td>
                    <td class="px-4 py-3">{{ $booking->booking_date }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs
                            {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $booking->status === 'canceled' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if ($booking->status === 'pending')
                        <form method="POST" action="{{ route('owner.bookings.status', $booking) }}" class="flex gap-2">
                            @csrf
                            @method('PATCH')
                            <button name="status" value="confirmed" class="text-green-600 text-sm hover:underline">Konfirmasi</button>
                            <button name="status" value="canceled" class="text-red-600 text-sm hover:underline">Tolak</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada booking.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection