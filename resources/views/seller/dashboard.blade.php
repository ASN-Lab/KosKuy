@extends('layouts.app')

@section('title', 'Dashboard Penjual | KosKuy')

@section('content')
<div class="container-page py-12">
    <div class="flex items-center justify-between">
        <h1 class="heading-xl">Dashboard Penjual</h1>
        <button type="button" class="btn btn-brand" onclick="document.getElementById('add-product-modal').classList.remove('hidden')">Tambah Barang</button>
    </div>

    <div id="add-product-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
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
    
    <div class="mt-8 grid gap-6">
        <div class="card p-6">
            <h2 class="font-bold text-lg">Produk Saya</h2>
            <table class="w-full mt-4">
                <tbody>
                    @foreach($products as $product)
                        <tr class="border-b">
                            <td class="p-2 font-medium">{{ $product->title }}</td>
                            <td class="p-2">{{ rupiah($product->price) }}</td>
                            <td class="p-2">
                                <span class="badge {{ $product->is_sold ? 'bg-ink-100 text-ink-500' : 'bg-accent-50 text-accent-700' }}">
                                    {{ $product->is_sold ? 'Terjual' : 'Aktif' }}
                                </span>
                            </td>
                            <td class="p-2">
                                <form action="{{ route('seller.products.toggle', $product->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="text-brand-600 hover:underline text-xs">{{ $product->is_sold ? 'Aktifkan Kembali' : 'Tandai Terjual' }}</button>
                                </form>
                            </td>
                            <td class="p-2">
                                <a href="{{ route('seller.products.edit', $product->id) }}" class="text-ink-600 hover:underline text-xs">Edit</a>
                            </td>
                            <td class="p-2">
                                <form action="{{ route('seller.products.destroy', $product->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-600 hover:underline text-xs">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
