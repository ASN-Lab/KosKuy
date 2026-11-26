@extends('layouts.app')

@section('title', 'Dashboard Penjual | KosKuy')

@section('content')
<div class="container-page py-12">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <span class="badge badge-brand mb-2">Dashboard Penjual</span>
            <h1 class="heading-xl">Katalog Thrift</h1>
            <p class="text-ink-500 mt-1">Kelola barang bekas dan pantau penjualanmu.</p>
        </div>
        <button type="button" class="btn btn-brand" onclick="document.getElementById('add-product-modal').classList.remove('hidden')">
            <x-ui.icon name="plus-circle" class="size-4" />
            Tambah Barang
        </button>
    </div>

    {{-- Stats Row --}}
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-2xl bg-brand-50 text-brand-600">
                    <x.ui.icon name="tag" class="size-5" />
                </span>
                <div>
                    <p class="text-xs font-semibold text-ink-400">Total Barang</p>
                    <p class="font-display text-2xl font-extrabold text-ink-900">{{ $stats['total_products'] }}</p>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-2xl bg-accent-50 text-accent-600">
                    <x.ui.icon name="check-badge" class="size-5" />
                </span>
                <div>
                    <p class="text-xs font-semibold text-ink-400">Terjual</p>
                    <p class="font-display text-2xl font-extrabold text-ink-900">{{ $stats['total_sold'] }}</p>
                </div>
            </div>
        </div>
        <div class="card p-5 sm:col-span-2 lg:col-span-1">
            <div class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-2xl bg-sun-50 text-sun-600">
                    <x.ui.icon name="banknotes" class="size-5" />
                </span>
                <div>
                    <p class="text-xs font-semibold text-ink-400">Total Pendapatan</p>
                    <p class="font-display text-2xl font-extrabold text-ink-900">{{ rupiah($stats['total_revenue']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div id="add-product-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
        <div class="card w-full max-w-lg p-6">
            <h2 class="font-bold text-lg">Tambah Barang Thrift</h2>
            <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <input type="text" name="title" placeholder="Nama Barang" class="field w-full" required>
                <textarea name="description" placeholder="Deskripsi" class="field w-full"></textarea>
                <input type="number" name="price" placeholder="Harga" class="field w-full" required>
                <select name="category_id" class="field w-full">
                    @foreach($categories as $cat) <option value="{{ $cat->id }}">{{ $cat->name }}</option> @endforeach
                </select>
                <input type="text" name="condition_status" placeholder="Kondisi (misal: Bekas Layak Pakai)" class="field w-full">
                <input type="file" name="images[]" multiple class="field w-full">
                <div class="flex gap-2 justify-end">
                    <button type="button" class="btn btn-white" onclick="document.getElementById('add-product-modal').classList.add('hidden')">Batal</button>
                    <button type="submit" class="btn btn-brand">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-8">
        <div class="card p-6">
            <div class="flex items-center justify-between gap-4">
                <h2 class="font-bold text-lg text-ink-900">Produk Saya</h2>
                <span class="badge-ink">{{ $products->count() }} barang</span>
            </div>

            @if ($products->isEmpty())
                @include('partials.empty-state', [
                    'icon' => 'tag',
                    'title' => 'Belum ada barang thrift',
                    'description' => 'Upload barang bekasmu dan jangkau pembeli terdekat dengan gratis.',
                ])
            @else
            <div class="overflow-x-auto mt-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-ink-400 text-xs uppercase tracking-wider border-b border-ink-100">
                            <th class="p-3">Produk</th>
                            <th class="p-3">Harga</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr class="border-b border-ink-50 hover:bg-ink-50/50 transition">
                                <td class="p-3 font-semibold text-ink-900">{{ \Illuminate\Support\Str::limit($product->title, 40) }}</td>
                                <td class="p-3 text-ink-600">{{ rupiah($product->price) }}</td>
                                <td class="p-3">
                                    <span class="badge {{ $product->is_sold ? 'bg-ink-100 text-ink-500 ring-1 ring-ink-200' : 'bg-accent-50 text-accent-700 ring-1 ring-accent-100' }}">
                                        {{ $product->is_sold ? 'Terjual' : 'Aktif' }}
                                    </span>
                                </td>
                                <td class="p-3 text-right whitespace-nowrap">
                                    <form action="{{ route('seller.products.toggle', $product->id) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-brand-600 hover:underline text-xs font-semibold">{{ $product->is_sold ? 'Aktifkan' : 'Tandai Terjual' }}</button>
                                    </form>
                                    <span class="text-ink-200 px-1">·</span>
                                    <a href="{{ route('seller.products.edit', $product->id) }}" class="text-ink-600 hover:underline text-xs font-semibold">Edit</a>
                                    <span class="text-ink-200 px-1">·</span>
                                    <form action="{{ route('seller.products.destroy', $product->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-rose-600 hover:underline text-xs font-semibold">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection