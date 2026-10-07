@extends('layouts.app')

@section('title', 'Edit Barang Thrift | KosKuy')

@section('content')
<div class="container-page py-12 max-w-2xl">
    <a href="{{ route('seller.dashboard') }}" class="text-brand-600 font-semibold hover:underline">&larr; Kembali ke Dashboard</a>
    <h1 class="heading-xl mt-4">Edit Barang Thrift</h1>

    <form action="{{ route('seller.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="mt-8 card p-6 space-y-5">
        @csrf
        @method('PUT')
        <div>
            <label for="title" class="field-label">Nama Barang</label>
            <input id="title" type="text" name="title" value="{{ $product->title }}" class="field w-full" required>
        </div>
        <div>
            <label for="description" class="field-label">Deskripsi</label>
            <textarea id="description" name="description" rows="4" class="field w-full" required>{{ $product->description }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="price" class="field-label">Harga</label>
                <input id="price" type="number" name="price" value="{{ $product->price }}" class="field w-full" required>
            </div>
            <div>
                <label for="category_id" class="field-label">Kategori</label>
                <select id="category_id" name="category_id" class="field w-full">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected($product->category_id === $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label for="condition_status" class="field-label">Kondisi</label>
            <input id="condition_status" type="text" name="condition_status" value="{{ $product->condition_status }}" class="field w-full" required>
        </div>
        <div>
            <label for="images" class="field-label">Tambah Foto (opsional)</label>
            <input id="images" type="file" name="images[]" multiple class="field w-full">
        </div>
        <button type="submit" class="btn btn-primary w-full">Simpan Perubahan</button>
    </form>
</div>
@endsection
