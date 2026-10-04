@extends('layouts.app')

@section('content')
<a href="{{ route('owner.dashboard') }}" class="text-indigo-600 hover:underline text-sm inline-block mb-4">&larr; Kembali ke Dashboard</a>

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold">{{ $kos->title }}</h1>
        <p class="text-gray-500">{{ $kos->address }}</p>
    </div>
    <span class="inline-block px-3 py-1 rounded text-sm {{ $kos->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
        {{ $kos->is_available ? 'Tersedia' : 'Tidak tersedia' }}
    </span>
</div>

@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded-lg mb-6">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
    <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-lg font-bold mb-4">Kamar ({{ $kos->rooms->count() }})</h2>
        <form method="POST" action="{{ route('owner.rooms.store') }}" class="flex gap-2 mb-4">
            @csrf
            <input type="hidden" name="kos_id" value="{{ $kos->id }}">
            <input type="text" name="room_name" placeholder="Nama kamar" required
                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <input type="text" name="tenant_name" placeholder="Penghuni (opsional)"
                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">Tambah</button>
        </form>

        <div class="space-y-3">
            @forelse ($kos->rooms as $room)
                <form method="POST" action="{{ route('owner.rooms.update', $room) }}" class="border border-gray-200 rounded-lg p-3">
                    @csrf
                    @method('PATCH')
                    <div class="flex gap-2 items-center mb-2">
                        <input type="text" name="room_name" value="{{ $room->room_name }}"
                            class="border border-gray-300 rounded-lg px-2 py-1 text-sm w-32">
                        <input type="text" name="tenant_name" value="{{ $room->tenant_name ?? '' }}"
                            placeholder="Penghuni" class="border border-gray-300 rounded-lg px-2 py-1 text-sm flex-1">
                        <span class="text-xs px-2 py-1 rounded {{ $room->is_occupied ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700' }}">
                            {{ $room->is_occupied ? 'Terisi' : 'Kosong' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="is_occupied" {{ $room->is_occupied ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600">
                            Tandai terisi manual
                        </label>
                        <div class="flex gap-3 items-center">
                            <button class="text-indigo-600 text-sm hover:underline">Simpan</button>
                            <button type="button" class="text-red-500 text-sm hover:underline"
                                onclick="document.getElementById('delete-room-{{ $room->id }}').submit()">Hapus</button>
                        </div>
                    </div>
                </form>
                <form id="delete-room-{{ $room->id }}" method="POST" action="{{ route('owner.rooms.delete', $room) }}" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @empty
                <p class="text-sm text-gray-400">Belum ada kamar. Tambahkan kamar pertama di form atas.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-lg font-bold mb-4">Kondisi Fasilitas</h2>
        <div class="space-y-3">
            @forelse ($kos->facilities as $facility)
                <form method="POST" action="{{ route('owner.facilities.update', $facility) }}" class="border border-gray-200 rounded-lg p-3">
                    @csrf
                    @method('PATCH')
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-medium text-sm">{{ $facility->facility_name }}</span>
                        <span class="text-xs px-2 py-1 rounded {{ $facility->condition_status === 'normal' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                            {{ $facility->condition_status === 'normal' ? 'Normal' : 'Dalam Perbaikan' }}
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <select name="condition_status" class="border border-gray-300 rounded-lg px-2 py-1 text-sm">
                            <option value="normal" {{ $facility->condition_status === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="under_repair" {{ $facility->condition_status === 'under_repair' ? 'selected' : '' }}>Dalam Perbaikan</option>
                        </select>
                        <input type="text" name="notes" placeholder="Catatan (opsional)"
                            class="flex-1 border border-gray-300 rounded-lg px-2 py-1 text-sm">
                        <button class="text-indigo-600 text-sm hover:underline">Update</button>
                    </div>
                    @if ($facility->lastUpdater)
                        <p class="text-xs text-gray-400 mt-2">Terakhir diperbarui oleh {{ $facility->lastUpdater->name }}</p>
                    @endif
                </form>
            @empty
                <p class="text-sm text-gray-400">Belum ada fasilitas terdaftar untuk kos ini.</p>
            @endforelse
        </div>
    </div>
</div>

<h2 class="text-xl font-bold mb-4">Riwayat Pemantauan</h2>
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
            <tr>
                <th class="px-4 py-3">Waktu</th>
                <th class="px-4 py-3">Subjek</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Catatan</th>
                <th class="px-4 py-3">Diupdate oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kos->conditionUpdates as $update)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ $update->created_at->format('d M Y, H:i') }}</td>
                    <td class="px-4 py-3">{{ $update->subject instanceof \App\Models\KosFacility ? $update->subject->facility_name : $update->subject_type }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs {{ $update->status === 'normal' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                            {{ $update->status === 'normal' ? 'Normal' : 'Dalam Perbaikan' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $update->notes ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $update->updater->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada riwayat pemantauan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection