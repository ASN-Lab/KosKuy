@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-12 mb-10">
    <h1 class="text-4xl font-bold mb-4">Selamat Datang di KosKuy</h1>
    <p class="text-lg">Temukan kos impian dan barang bekas berkualitas untuk mahasiswa.</p>
</div>

<h2 class="text-2xl font-bold mb-6">Kos Terbaru</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
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

<h2 class="text-2xl font-bold mb-6">Produk Thrift Terbaru</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    @foreach($thriftProducts as $product)
    <a href="/thrift/{{ $product->id }}" class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
        @if($product->images->isNotEmpty())
        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" class="h-40 w-full object-cover">
        @else
        <div class="h-40 bg-gray-200 flex items-center justify-center text-gray-400">Tidak ada foto</div>
        @endif
        <div class="p-4">
            <h3 class="font-semibold">{{ $product->title }}</h3>
            <p class="text-indigo-600 font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
        </div>
    </a>
    @endforeach
</div>
@endsection
