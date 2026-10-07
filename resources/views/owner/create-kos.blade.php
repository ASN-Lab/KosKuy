@extends('layouts.app')

@section('title', 'Tambah Listing Kos | KosKuy')

@section('content')
<div class="container-page py-12 max-w-2xl">
    <a href="{{ route('owner.dashboard') }}" class="text-brand-600 font-semibold hover:underline">&larr; Kembali ke Dashboard</a>
    <h1 class="heading-xl mt-4">Tambah Listing Kos</h1>

    <form action="{{ route('owner.kos.store') }}" method="POST" enctype="multipart/form-data" class="mt-8 card p-6 space-y-5">
        @csrf
        <div>
            <label for="title" class="field-label">Nama Kos</label>
            <input id="title" type="text" name="title" class="field w-full" required>
        </div>
        <div>
            <label for="description" class="field-label">Deskripsi</label>
            <textarea id="description" name="description" rows="4" class="field w-full" required></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="price_per_month" class="field-label">Harga / Bulan</label>
                <input id="price_per_month" type="number" name="price_per_month" class="field w-full" required>
            </div>
            <div>
                <label for="gender_type" class="field-label">Jenis Kos</label>
                <select id="gender_type" name="gender_type" class="field w-full">
                    <option value="putra">Putra</option>
                    <option value="putri">Putri</option>
                    <option value="campur">Campur</option>
                </select>
            </div>
        </div>
        <div>
            <label for="facilities" class="field-label">Fasilitas (pisahkan dengan koma)</label>
            <input id="facilities" type="text" name="facilities" placeholder="AC, Kamar Mandi Dalam, WiFi, Parkir Motor, dll" class="field w-full">
            <p class="mt-1 text-xs text-ink-500">Contoh: AC, Kamar Mandi Dalam, WiFi, Parkir Motor</p>
        </div>
        <div>
            <label for="address" class="field-label">Alamat</label>
            <input id="address" type="text" name="address" class="field w-full" required>
        </div>
        <div>
            <label for="images" class="field-label">Foto Kos</label>
            <input id="images" type="file" name="images[]" multiple class="field w-full">
        </div>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_available" value="1" checked class="checkbox">
            <span class="text-sm font-medium">Tersedia untuk disewa</span>
        </label>
        <button type="submit" class="btn btn-primary w-full">Simpan Listing</button>
    </form>
</div>
@endsection
