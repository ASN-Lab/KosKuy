@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div>
        @if($kos->images->isNotEmpty())
        <div class="grid grid-cols-2 gap-4">
            @foreach($kos->images as $image)
            <img src="{{ asset('storage/' . $image->image_path) }}" class="rounded-xl w-full h-64 object-cover">
            @endforeach
        </div>
        @else
        <div class="bg-gray-200 h-64 flex items-center justify-center rounded-xl text-gray-400">Tidak ada foto</div>
        @endif
    </div>

    <div class="bg-white p-8 rounded-xl shadow-md">
        <h1 class="text-3xl font-bold mb-4">{{ $kos->title }}</h1>
        <p class="text-2xl text-indigo-600 font-bold mb-4">Rp {{ number_format($kos->price_per_month, 0, ',', '.') }} / bulan</p>
        <span class="inline-block bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm capitalize mb-6">{{ $kos->gender_type }}</span>

        <h3 class="font-semibold text-lg mb-2">Deskripsi</h3>
        <p class="text-gray-600 mb-6">{{ $kos->description }}</p>

        <h3 class="font-semibold text-lg mb-2">Alamat</h3>
        <p class="text-gray-600 mb-6">{{ $kos->address }}</p>

        @if($kos->facilities->isNotEmpty())
        <h3 class="font-semibold text-lg mb-2">Fasilitas</h3>
        <ul class="list-disc list-inside text-gray-600 mb-6">
            @foreach($kos->facilities as $facility)
            <li>{{ $facility->facility_name }}</li>
            @endforeach
        </ul>
        @endif

        @if($kos->latitude && $kos->longitude)
        <h3 class="font-semibold text-lg mb-2">Lokasi</h3>
        <a href="https://www.google.com/maps?q={{ $kos->latitude }},{{ $kos->longitude }}" target="_blank" class="text-indigo-600 underline mb-6 inline-block">Lihat di Google Maps</a>
        @endif

        @if($kos->owner && $kos->owner->phone)
        <a href="https://wa.me/{{ $kos->owner->phone }}?text=Halo, saya tertarik dengan kos {{ urlencode($kos->title) }}" target="_blank"
           class="block w-full bg-green-500 text-white text-center py-3 rounded-lg font-semibold hover:bg-green-600 mt-4">
            Hubungi Pemilik via WhatsApp
        </a>
        @endif

        @auth
            @if(auth()->user()->role === 'student')
                @if($kos->is_available)
                    <form method="POST" action="{{ route('web.bookings.store') }}">
                        @csrf
                        <input type="hidden" name="kos_id" value="{{ $kos->id }}">
                        <button class="block w-full bg-indigo-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-indigo-700 mt-4">
                            Booking Kos Ini
                        </button>
                    </form>
                @else
                    <p class="text-center text-gray-400 mt-4">Kos ini sedang tidak tersedia untuk booking.</p>
                @endif
            @endif
        @else
            <a href="{{ route('login') }}" class="block w-full bg-gray-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-gray-700 mt-4">
                Login untuk Booking
            </a>
        @endauth
    </div>
</div>
@endsection
