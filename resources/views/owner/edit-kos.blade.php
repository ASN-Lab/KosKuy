@extends('layouts.app')

@section('title', 'Edit Listing Kos | KosKuy')

@section('content')
<div class="container-page py-12 max-w-2xl">
    <a href="{{ route('owner.dashboard') }}" class="text-brand-600 font-semibold hover:underline">&larr; Kembali ke Dashboard</a>
    <h1 class="heading-xl mt-4">Edit Listing Kos</h1>

    <form action="{{ route('owner.kos.update', $kos->id) }}" method="POST" enctype="multipart/form-data" class="mt-8 card p-6 space-y-5">
        @csrf
        @method('PUT')
        <div>
            <label for="title" class="field-label">Nama Kos</label>
            <input id="title" type="text" name="title" value="{{ $kos->title }}" class="field w-full" required>
        </div>
        <div>
            <label for="description" class="field-label">Deskripsi</label>
            <textarea id="description" name="description" rows="4" class="field w-full" required>{{ $kos->description }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="price_per_month" class="field-label">Harga / Bulan</label>
                <input id="price_per_month" type="number" name="price_per_month" value="{{ $kos->price_per_month }}" class="field w-full" required>
            </div>
            <div>
                <label for="gender_type" class="field-label">Jenis Kos</label>
                <select id="gender_type" name="gender_type" class="field w-full">
                    <option value="putra" @selected($kos->gender_type === 'putra')>Putra</option>
                    <option value="putri" @selected($kos->gender_type === 'putri')>Putri</option>
                    <option value="campur" @selected($kos->gender_type === 'campur')>Campur</option>
                </select>
            </div>
        </div>
        <div>
            <label for="address" class="field-label">Alamat</label>
            <input id="address" type="text" name="address" value="{{ $kos->address }}" class="field w-full" required>
        </div>
        <div>
            <label for="images" class="field-label">Tambah Foto Kos (opsional)</label>
            <input id="images" type="file" name="images[]" multiple class="field w-full">
        </div>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_available" value="1" @checked($kos->is_available) class="checkbox">
            <span class="text-sm font-medium">Tersedia untuk disewa</span>
        </label>
        <button type="submit" class="btn btn-primary w-full">Simpan Perubahan</button>
    </form>
</div>
@endsection
