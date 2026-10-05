@extends('layouts.app')

@section('title', 'Dashboard Saya | KosKuy')
@section('meta_description', 'Kelola listing kos dan barang thrift kamu dari satu dashboard.')

@section('content')

    @php
        $user = auth()->user();
        $roleLabel = $user->role === 'owner' ? 'Pemilik Listing' : 'Mahasiswa';
    @endphp

    {{-- ========================= HEADER ========================= --}}
    <section class="relative overflow-hidden border-b border-ink-100 bg-ink-950">
        <div class="grid-lines pointer-events-none absolute inset-0" aria-hidden="true"></div>
        <div class="blob -top-24 -right-16 size-96 bg-brand-500/35 blur-3xl" aria-hidden="true"></div>
        <div class="blob -bottom-32 left-10 size-96 bg-accent-400/25 blur-3xl" aria-hidden="true"></div>

        <div class="container-page relative py-12 sm:py-14">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <span class="grid size-16 shrink-0 place-items-center rounded-3xl bg-white/10 font-display text-2xl font-extrabold text-white ring-1 ring-white/15">
                        {{ initials($user->name) }}
                    </span>
                    <div>
                        <span class="badge-glass inline-flex">
                            <x-ui.icon name="check-badge" class="size-3.5 text-accent-300" />
                            {{ $roleLabel }}
                        </span>
                        <h1 class="mt-2.5 text-2xl font-extrabold text-white sm:text-3xl">
                            Halo, {{ \Illuminate\Support\Str::before($user->name, ' ') }}!
                        </h1>
                        <p class="mt-1 text-sm text-ink-400">
                            Ringkasan listing dan aktivitas akunmu.
                        </p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('kos.index') }}" class="btn btn-md w-full border border-white/15 bg-white/10 text-white backdrop-blur transition hover:bg-white/20 sm:w-auto">
                        <x-ui.icon name="magnifying-glass" class="size-4" />
                        Jelajahi Kos
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-md btn-white w-full sm:w-auto">
                            <x-ui.icon name="arrow-path" class="size-4" />
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================= STATS ========================= --}}
    <section class="container-page -mt-8 relative z-10">
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ([
                ['label' => 'Total kos', 'value' => $stats['kos'], 'icon' => 'building-office-2', 'tone' => 'text-brand-600 bg-brand-50'],
                ['label' => 'Kos terisi', 'value' => $stats['kos_available'], 'icon' => 'check-badge', 'tone' => 'text-accent-600 bg-accent-50'],
                ['label' => 'Total barang', 'value' => $stats['thrift'], 'icon' => 'tag', 'tone' => 'text-violet-600 bg-violet-50'],
                ['label' => 'Barang aktif', 'value' => $stats['thrift_active'], 'icon' => 'fire', 'tone' => 'text-sun-600 bg-sun-50'],
            ] as $stat)
                <div class="card p-5 sm:p-6">
                    <span class="grid size-10 place-items-center rounded-2xl {{ $stat['tone'] }}">
                        <x-ui.icon :name="$stat['icon']" class="size-5" />
                    </span>
                    <p class="mt-4 font-display text-3xl font-extrabold text-ink-900">{{ $stat['value'] }}</p>
                    <p class="mt-0.5 text-[13px] font-semibold text-ink-500">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ========================= LISTINGS ========================= --}}
    <section class="section-tight">
        <div class="container-page">
            <div class="grid gap-8 lg:grid-cols-12 lg:gap-10">

                {{-- Kos --}}
                <div class="lg:col-span-7">
                    <div class="flex items-end justify-between gap-4">
                        <h2 class="heading-md">Listing kos saya</h2>
                        <span class="badge-ink">{{ $kosListings->count() }} unit</span>
                    </div>

                    @if ($kosListings->isEmpty())
                        @include('partials.empty-state', [
                            'icon' => 'building-office-2',
                            'title' => 'Belum ada kos dipasang',
                            'description' => 'Pasang listing kosmu lewat panel admin, lalu listingnya langsung tampil di sini.',
                            'actionLabel' => 'Buka Panel Admin',
                            'actionHref' => url('/admin'),
                        ])
                    @else
                        <ul class="mt-6 space-y-3">
                            @foreach ($kosListings as $kos)
                                @php $image = listingImage($kos); @endphp
                                <li class="card card-hover flex items-center gap-4 p-4">
                                    <span class="media h-20 w-24 shrink-0 rounded-2xl">
                                        @if ($image)
                                            <img src="{{ $image }}" alt="" class="media-img" loading="lazy">
                                        @else
                                            <span class="grid place-items-center bg-brand-soft text-brand-400">
                                                <x-ui.icon name="building-office-2" class="size-6" />
                                            </span>
                                        @endif
                                    </span>

                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('kos.show', $kos->id) }}" class="line-clamp-1 text-sm font-bold transition hover:text-brand-700">
                                            {{ $kos->title }}
                                        </a>
                                        <p class="mt-1 text-[13px] font-semibold text-brand-700">
                                            {{ rupiah($kos->price_per_month) }}<span class="text-ink-400">/bln</span>
                                        </p>
                                        <p class="mt-1 line-clamp-1 text-xs text-ink-400">
                                            {{ $kos->facilities->count() }} fasilitas
                                        </p>
                                    </div>

                                    <span class="badge {{ $kos->is_available ? 'bg-accent-50 text-accent-700 ring-1 ring-accent-100' : 'bg-ink-100 text-ink-500 ring-1 ring-ink-200' }} shrink-0">
                                        {{ $kos->is_available ? 'Tersedia' : 'Penuh' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- Thrift --}}
                <div class="lg:col-span-5">
                    <div class="flex items-end justify-between gap-4">
                        <h2 class="heading-md">Barang thrift saya</h2>
                        <span class="badge-ink">{{ $thriftProducts->count() }} barang</span>
                    </div>

                    @if ($thriftProducts->isEmpty())
                        @include('partials.empty-state', [
                            'icon' => 'tag',
                            'title' => 'Belum ada barang thrift',
                            'description' => 'Upload barang bekasmu dan jangkau pembeli terdekat dengan gratis.',
                            'actionLabel' => 'Lihat Katalog Thrift',
                            'actionHref' => route('thrift.index'),
                        ])
                    @else
                        <ul class="mt-6 space-y-3">
                            @foreach ($thriftProducts as $product)
                                @php $image = listingImage($product); @endphp
                                <li class="card card-hover flex items-center gap-4 p-4">
                                    <span class="media h-20 w-24 shrink-0 rounded-2xl">
                                        @if ($image)
                                            <img src="{{ $image }}" alt="" class="media-img" loading="lazy">
                                        @else
                                            <span class="grid place-items-center bg-accent-50 text-accent-400">
                                                <x-ui.icon :name="categoryIcon($product->category)" class="size-6" />
                                            </span>
                                        @endif
                                    </span>

                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('thrift.show', $product->id) }}" class="line-clamp-1 text-sm font-bold transition hover:text-accent-700">
                                            {{ $product->title }}
                                        </a>
                                        <p class="mt-1 text-[13px] font-semibold text-accent-700">
                                            {{ rupiah($product->price) }}
                                        </p>
                                        <p class="mt-1 truncate text-xs text-ink-400">
                                            {{ $product->category?->name ?? 'Tanpa kategori' }}
                                        </p>
                                    </div>

                                    <span class="badge {{ $product->is_sold ? 'bg-ink-100 text-ink-500 ring-1 ring-ink-200' : 'bg-accent-50 text-accent-700 ring-1 ring-accent-100' }} shrink-0">
                                        {{ $product->is_sold ? 'Terjual' : 'Aktif' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Account --}}
            <div class="mt-12 card p-6 sm:p-8">
                <h2 class="text-lg font-extrabold">Data akun</h2>

                <dl class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['label' => 'Nama', 'value' => $user->name, 'icon' => 'users'],
                        ['label' => 'Email', 'value' => $user->email, 'icon' => 'envelope'],
                        ['label' => 'WhatsApp', 'value' => $user->phone ?: 'Belum diisi', 'icon' => 'chat-bubble-oval-left'],
                        ['label' => 'Peran', 'value' => $roleLabel, 'icon' => 'shield-check'],
                    ] as $row)
                        <div>
                            <dt class="flex items-center gap-2 text-[13px] font-semibold text-ink-400">
                                <x-ui.icon :name="$row['icon']" class="size-3.5" />
                                {{ $row['label'] }}
                            </dt>
                            <dd class="mt-1.5 text-sm font-bold text-ink-900">{{ $row['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>

                <p class="mt-6 flex items-start gap-2.5 rounded-2xl bg-ink-50 p-4 text-[13px] leading-relaxed text-ink-600">
                    <x-ui.icon name="information-circle" class="mt-0.5 size-4 shrink-0" />
                    <span>
                        Pengubahan data akun dilakukan lewat
                        <a href="{{ url('/admin') }}" class="font-semibold text-brand-700 hover:underline">panel admin</a>.
                    </span>
                </p>
            </div>
        </div>
    </section>

@endsection