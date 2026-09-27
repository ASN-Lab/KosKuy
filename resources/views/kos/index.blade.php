@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold mb-6">Cari Kos</h1>

<form method="GET" class="bg-white p-6 rounded-xl shadow-md mb-8 grid grid-cols-1 md:grid-cols-4 gap-4">
    <input type="text" name="search" placeholder="Nama kos..." value="{{ request('search') }}" class="border rounded-lg px-4 py-2">
    <select name="gender_type" class="border rounded-lg px-4 py-2">
        <option value="">Semua Gender</option>
        <option value="putra" {{ request('gender_type') == 'putra' ? 'selected' : '' }}>Putra</option>
        <option value="putri" {{ request('gender_type') == 'putri' ? 'selected' : '' }}>Putri</option>
        <option value="campur" {{ request('gender_type') == 'campur' ? 'selected' : '' }}>Campur</option>
    </select>
    <input type="number" name="min_price" placeholder="Harga min" value="{{ request('min_price') }}" class="border rounded-lg px-4 py-2">
    <input type="number" name="max_price" placeholder="Harga max" value="{{ request('max_price') }}" class="border rounded-lg px-4 py-2">
    <button type="submit" class="md:col-span-4 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700">Filter</button>
</form>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($kosListings as $kos)
    <a href="/kos/{{ $kos->id }}" class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
        @if($kos->images->isNotEmpty())
        <img src="{{ asset('storage/' . $kos->images->first()->image_path) }}" class="h-48 w-full object-cover">
        @else
        <div class="h-48 bg-gray-200 flex items-center justify-center text-gray-400">Tidak ada foto</div>
        @endif
        <div class="p-4">
            <h3 class="font-semibold text-lg">{{ $kos->title }}</h3>
            <p class="text-indigo-600 font-bold">Rp {{ number_format($kos->price_per_month, 0, ',', '.') }} / bulan</p>
            <span class="inline-block bg-indigo-100 text-indigo-700 px-2 py-1 rounded text-sm mt-2 capitalize">{{ $kos->gender_type }}</span>
        </div>
    </a>
    @endforeach
</div>

<div class="mt-8">{{ $kosListings->links() }}</div>
@endsection
