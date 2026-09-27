@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold mb-6">Katalog Thrift</h1>

<form method="GET" class="bg-white p-6 rounded-xl shadow-md mb-8 grid grid-cols-1 md:grid-cols-3 gap-4">
    <input type="text" name="search" placeholder="Cari barang..." value="{{ request('search') }}" class="border rounded-lg px-4 py-2">
    <select name="category_id" class="border rounded-lg px-4 py-2">
        <option value="">Semua Kategori</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700">Filter</button>
</form>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    @foreach($products as $product)
    <a href="/thrift/{{ $product->id }}" class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
        @if($product->images->isNotEmpty())
        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" class="h-48 w-full object-cover">
        @else
        <div class="h-48 bg-gray-200 flex items-center justify-center text-gray-400">Tidak ada foto</div>
        @endif
        <div class="p-4">
            <h3 class="font-semibold text-lg">{{ $product->title }}</h3>
            <p class="text-indigo-600 font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            <p class="text-gray-500 text-sm mt-1">{{ $product->condition_status }}</p>
        </div>
    </a>
    @endforeach
</div>

<div class="mt-8">{{ $products->links() }}</div>
@endsection
