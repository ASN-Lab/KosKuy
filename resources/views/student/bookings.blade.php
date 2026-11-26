@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6">Riwayat Booking Kos</h1>

<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
            <tr>
                <th class="px-4 py-3">Kos</th>
                <th class="px-4 py-3">Tanggal Booking</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bookings as $booking)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ $booking->kos->title }}</td>
                    <td class="px-4 py-3">{{ $booking->booking_date }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs
                            {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $booking->status === 'canceled' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $booking->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="p-0"><x.partials.empty-state
                    icon="calendar-days"
                    title="Belum ada booking kos"
                    description="Booking kos yang kamu lakukan akan muncul di sini."
                    actionLabel="Cari Kos"
                    :actionHref="route('kos.index')" /></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection