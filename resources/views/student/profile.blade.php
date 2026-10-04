@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Profil Saya</h1>
    
    @if (session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded-lg mb-6">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" class="bg-white rounded-xl shadow-md p-6 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Email (tidak bisa diubah)</label>
            <p class="mt-1 p-2 bg-gray-100 rounded-lg">{{ $user->email }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Password Baru (kosongkan jika tidak ubah)</label>
            <input type="password" name="password" class="w-full border rounded-lg px-3 py-2 mt-1">
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">Simpan Perubahan</button>
    </form>
</div>
@endsection