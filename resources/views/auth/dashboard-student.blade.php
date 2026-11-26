@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa | KosKuy')

@section('content')
<div class="container-page py-12">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <span class="badge badge-brand mb-2">Mahasiswa / Pencari Kos</span>
            <h1 class="heading-xl">Dashboard Mahasiswa</h1>
            <p class="text-ink-500 mt-1">Kelola pengajuan booking kos dan riwayat pembelian barang thrift kamu.</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-md btn-white">Keluar</button>
        </form>
    </div>

    {{-- Stats Row --}}
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-2xl bg-brand-50 text-brand-600">
                    <x-ui.icon name="building-office-2" class="size-5" />
                </span>
                <div>
                    <p class="text-xs font-semibold text-ink-400">Total Booking</p>
                    <p class="font-display text-2xl font-extrabold text-ink-900">{{ auth()->user()->bookings()->count() }}</p>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-2xl bg-accent-50 text-accent-600">
                    <x-ui.icon name="tag" class="size-5" />
                </span>
                <div>
                    <p class="text-xs font-semibold text-ink-400">Pembelian Thrift</p>
                    <p class="font-display text-2xl font-extrabold text-ink-900">{{ auth()->user()->purchases()->count() }}</p>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-2xl bg-violet-50 text-violet-600">
                    <x-ui.icon name="chat-bubble-oval-left" class="size-5" />
                </span>
                <div>
                    <p class="text-xs font-semibold text-ink-400">Pesan Aktif</p>
                    <p class="font-display text-2xl font-extrabold text-ink-900">Chat</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 grid gap-6 md:grid-cols-2">
        <div class="card p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <span class="grid size-12 place-items-center rounded-2xl bg-brand-50 text-brand-600">
                        <x-ui.icon name="building-office-2" class="size-6" />
                    </span>
                    <div>
                        <h2 class="font-bold text-lg text-ink-900">Pengajuan Booking Kos</h2>
                        <p class="text-xs text-ink-500">Cek status pengajuan sewa kamar kosmu</p>
                    </div>
                </div>
                <p class="mt-4 text-sm text-ink-600">Kamu dapat melihat apakah booking kosmu sedang diproses, dikonfirmasi, atau dibatalkan oleh pemilik kos.</p>
            </div>
            <div class="mt-6">
                <a href="{{ route('my-bookings') }}" class="btn btn-brand w-full">Lihat Riwayat Booking</a>
            </div>
        </div>

        <div class="card p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <span class="grid size-12 place-items-center rounded-2xl bg-accent-50 text-accent-600">
                        <x-ui.icon name="tag" class="size-6" />
                    </span>
                    <div>
                        <h2 class="font-bold text-lg text-ink-900">Belanja Barang Thrift</h2>
                        <p class="text-xs text-ink-500">Riwayat barang thrift yang pernah dibeli</p>
                    </div>
                </div>
                <p class="mt-4 text-sm text-ink-600">Lihat daftar barang bekas atau thrift berkualitas yang sudah berhasil kamu pesan dari penjual.</p>
            </div>
            <div class="mt-6">
                <a href="{{ route('my-purchases') }}" class="btn btn-white w-full">Lihat Riwayat Belanja</a>
            </div>
        </div>
    </div>
</div>
@endsection
