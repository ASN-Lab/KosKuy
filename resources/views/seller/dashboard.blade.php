@extends('layouts.dashboard', ['dashboardUrl' => route('seller.dashboard')])

@section('title', 'Dashboard Seller')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold">Dashboard Seller (Thrift)</h1>
    <span class="text-gray-500">Selamat datang, {{ auth()->user()->name }}</span>
</div>

@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded-lg mb-6">{{ session('success') }}</div>
@endif

<!-- Analisis Penjualan -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="bg-white rounded-xl shadow-md p-6">
        <p class="text-sm text-gray-500">Total Produk</p>
        <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $stats['total_products'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-md p-6">
        <p class="text-sm text-gray-500">Produk Terjual</p>
        <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['total_sold'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-md p-6">
        <p class="text-sm text-gray-500">Total Pendapatan</p>
        <p class="text-3xl font-bold text-indigo-600 mt-1">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
    </div>
</div>

<h2 class="text-xl font-bold mb-4">Tambah Produk Thrift Baru</h2>
<form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-md p-6 mb-12 grid grid-cols-1 md:grid-cols-2 gap-4">
    @csrf
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
        <select name="category_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Produk</label>
        <input type="text" name="title" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
        <input type="number" name="price" min="0" step="0.01" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi</label>
        <select name="condition_status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            <option value="baru">Baru</option>
            <option value="like_new">Seperti Baru</option>
            <option value="bekas">Bekas</option>
            <option value="rusak">Rusak / Part</option>
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
        <textarea name="description" rows="3" required class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
        <input type="file" name="images[]" multiple accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2">
    </div>
    <div class="md:col-span-2">
        <button class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">Simpan Produk</button>
    </div>
</form>

<h2 class="text-xl font-bold mb-4">Manajemen Inventaris Produk</h2>
<div class="bg-white rounded-xl shadow-md overflow-hidden mb-12">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
            <tr>
                <th class="px-4 py-3">Judul</th>
                <th class="px-4 py-3">Kategori</th>
                <th class="px-4 py-3">Harga</th>
                <th class="px-4 py-3">Kondisi</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr class="border-t">
                    <td class="px-4 py-3 font-medium">{{ $product->title }}</td>
                    <td class="px-4 py-3">{{ $product->category->name }}</td>
                    <td class="px-4 py-3">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 capitalize">{{ $product->condition_status }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs {{ $product->is_sold ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $product->is_sold ? 'Terjual' : 'Aktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 flex gap-3">
                        <form method="POST" action="{{ route('seller.products.toggle', $product) }}">
                            @csrf
                            @method('PATCH')
                            <button class="text-indigo-600 text-sm hover:underline">{{ $product->is_sold ? 'Aktifkan' : 'Tandai Terjual' }}</button>
                        </form>
                        <form method="POST" action="{{ route('seller.products.destroy', $product) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 text-sm hover:underline" onclick="return confirm('Hapus produk ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada produk.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<h2 class="text-xl font-bold mb-4">Pelacakan Transaksi Masuk (Terjual)</h2>
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
            <tr>
                <th class="px-4 py-3">Waktu Terjual</th>
                <th class="px-4 py-3">Produk</th>
                <th class="px-4 py-3">Harga</th>
                <th class="px-4 py-3">Pembeli</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products->where('is_sold', true) as $soldProduct)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ $soldProduct->updated_at->format('d M Y, H:i') }}</td>
                    <td class="px-4 py-3 font-medium">{{ $soldProduct->title }}</td>
                    <td class="px-4 py-3">Rp {{ number_format($soldProduct->price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">{{ $soldProduct->buyer->name ?? 'Mahasiswa (Umum)' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada transaksi penjualan masuk.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection