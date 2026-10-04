@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold">Dashboard Owner</h1>
    <span class="text-gray-500">Selamat datang, {{ auth()->user()->name }}</span>
</div>

@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded-lg mb-6">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
    @foreach ($kosListings as $kos)
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex justify-between items-start mb-4">
                <h2 class="text-lg font-bold">{{ $kos->title }}</h2>
                <span class="inline-block px-2 py-1 rounded text-sm {{ $kos->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $kos->is_available ? 'Tersedia' : 'Tidak tersedia' }}
                </span>
            </div>

            <h3 class="font-semibold text-sm text-gray-600 mb-2">Kamar ({{ $kos->rooms->count() }})</h3>
            <form method="POST" action="{{ route('owner.rooms.store') }}" class="flex gap-2 mb-3">
                @csrf
                <input type="hidden" name="kos_id" value="{{ $kos->id }}">
                <input type="text" name="room_name" placeholder="Nama kamar" required
                    class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                <input type="text" name="tenant_name" placeholder="Penghuni (opsional)"
                    class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                <button class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg text-sm">Tambah</button>
            </form>

            <div class="space-y-2 mb-6">
                @forelse ($kos->rooms as $room)
                    <form method="POST" action="{{ route('owner.rooms.update', $room) }}" class="flex gap-2 items-center">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="room_name" value="{{ $room->room_name }}"
                            class="border border-gray-300 rounded-lg px-2 py-1 text-sm w-32">
                        <input type="text" name="tenant_name" value="{{ $room->tenant_name ?? '' }}"
                            placeholder="Penghuni" class="border border-gray-300 rounded-lg px-2 py-1 text-sm flex-1">
                        <span class="text-xs {{ $room->is_occupied ? 'text-orange-600' : 'text-green-600' }}">
                            {{ $room->is_occupied ? 'Terisi' : 'Kosong' }}
                        </span>
                        <button class="text-indigo-600 text-sm hover:underline">Simpan</button>
                    </form>
                    <form method="POST" action="{{ route('owner.rooms.delete', $room) }}" class="flex justify-end -mt-2">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-500 text-xs hover:underline">Hapus</button>
                    </form>
                @empty
                    <p class="text-sm text-gray-400">Belum ada kamar.</p>
                @endforelse
            </div>

            <h3 class="font-semibold text-sm text-gray-600 mb-2">Fasilitas</h3>
            <div class="space-y-2">
                @forelse ($kos->facilities as $facility)
                    <form method="POST" action="{{ route('owner.facilities.update', $facility) }}" class="flex gap-2 items-center">
                        @csrf
                        @method('PATCH')
                        <span class="flex-1 text-sm">{{ $facility->facility_name }}</span>
                        <select name="condition_status" class="border border-gray-300 rounded-lg px-2 py-1 text-sm">
                            <option value="normal" {{ $facility->condition_status === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="under_repair" {{ $facility->condition_status === 'under_repair' ? 'selected' : '' }}>Perbaikan</option>
                        </select>
                        <input type="text" name="notes" placeholder="Catatan"
                            class="border border-gray-300 rounded-lg px-2 py-1 text-sm w-40">
                        <button class="text-indigo-600 text-sm hover:underline">Update</button>
                    </form>
                @empty
                    <p class="text-sm text-gray-400">Belum ada fasilitas.</p>
                @endforelse
            </div>
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