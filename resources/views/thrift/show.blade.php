@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div>
        @if($product->images->isNotEmpty())
        <div class="space-y-4">
            @foreach($product->images as $image)
            <img src="{{ asset('storage/' . $image->image_path) }}" class="rounded-xl w-full">
            @endforeach
        </div>
        @else
        <div class="bg-gray-200 h-64 flex items-center justify-center rounded-xl text-gray-400">Tidak ada foto</div>
        @endif
    </div>

    <div class="bg-white p-8 rounded-xl shadow-md">
        <h1 class="text-3xl font-bold mb-4">{{ $product->title }}</h1>
        <p class="text-2xl text-indigo-600 font-bold mb-2">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
        <p class="text-gray-600 mb-6">Kondisi: {{ $product->condition_status }}</p>

        <h3 class="font-semibold text-lg mb-2">Deskripsi</h3>
        <p class="text-gray-600 mb-6">{{ $product->description }}</p>

        <h3 class="font-semibold text-lg mb-2">Info Penjual</h3>
        <p class="text-gray-600 mb-6">{{ $product->seller->name }}</p>

        @if($product->seller && $product->seller->phone)
        <a href="https://wa.me/{{ $product->seller->phone }}?text=Halo, saya tertarik dengan barang {{ urlencode($product->title) }}" target="_blank"
           class="block w-full bg-green-500 text-white text-center py-3 rounded-lg font-semibold hover:bg-green-600">
            Chat Penjual via WhatsApp
        </a>
        @endif
    </div>
</div>
@endsection
