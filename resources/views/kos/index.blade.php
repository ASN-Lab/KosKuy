@extends('layouts.app')

@section('title', 'Cari Kos Mahasiswa di ' . config('app.name') . ' — Gratis, Tanpa Perantara')
@section('meta_description', 'Cari kos putra, kos putri, dan kos campur sesuai budget. Filter harga, lihat foto dan fasilitas, lalu chat pemilik langsung via WhatsApp. 100% gratis di KosKuy.')

@section('content')

    {{-- ========================= PAGE HEADER ========================= --}}
    <section class="relative overflow-hidden border-b border-ink-100 bg-ink-50/60">
        <div class="dot-grid pointer-events-none absolute inset-0 opacity-60" aria-hidden="true"></div>
        <div class="blob -top-32 right-0 size-96 bg-brand-200/40 blur-3xl" aria-hidden="true"></div>

        <div class="container-page relative py-12 sm:py-16">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-sm text-ink-500">
                <a href="{{ route('home') }}" class="font-semibold transition hover:text-brand-700">Beranda</a>
                <x-ui.icon name="chevron-right" class="size-3.5 text-ink-300" />
                <span class="font-semibold text-ink-900">Cari Kos</span>
            </nav>

            <div class="mt-5 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="heading-xl text-ink-900">
                        Cari <span class="gradient-text">Kos</span> Mahasiswa
                    </h1>
                    <p class="lede mt-3 max-w-xl">
                        <span class="font-semibold text-ink-700">{{ $kosListings->total() }} listing</span>
                        ditemukan. Filter sesuai budget dan jenis kamar, lalu chat pemilik tanpa perantara.
                    </p>
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    <span class="badge-accent">
                        <span class="relative flex size-2">
                            <span class="absolute inline-flex size-full animate-ping rounded-full bg-accent-400 opacity-75"></span>
                            <span class="relative inline-flex size-2 rounded-full bg-accent-500"></span>
                        </span>
                        {{ $kosListings->total() }} tersedia
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================= CONTENT ========================= --}}
    <section class="section-tight">
        <div class="container-page">
            <div class="grid gap-8 lg:grid-cols-[300px_1fr] lg:gap-10">

                {{-- ---------- Filter sidebar ---------- --}}
                <aside class="lg:sticky lg:top-24 lg:self-start">
                    <div x-data="filterPanel()" class="card overflow-hidden">
                        {{-- Mobile toggle --}}
                        <button
                            type="button"
                            @click="toggle()"
                            class="flex w-full items-center justify-between gap-3 px-5 py-4 lg:hidden"
                            :aria-expanded="open ? 'true' : 'false'"
                        >
                            <span class="flex items-center gap-2 text-sm font-bold">
                                <x-ui.icon name="adjustments-horizontal" class="size-4 text-brand-600" />
                                Filter &amp; urutkan
                                @if ($activeFilterCount > 0)
                                    <span class="badge bg-brand-600 px-2 text-white">{{ $activeFilterCount }}</span>
                                @endif
                            </span>
                            <x-ui.icon name="chevron-down" class="size-4 text-ink-400 transition-transform" ::class="open && 'rotate-180'" />
                        </button>

                        <form
                            method="GET"
                            action="{{ route('kos.index') }}"
                            x-show="open || desktop"
                            class="space-y-6 border-t border-ink-100 p-5 lg:border-t-0"
                        >
                            {{-- Preserve sort when filtering --}}
                            @if (request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif

                            <div>
                                <label for="search" class="field-label">Kata kunci</label>
                                <div class="relative">
                                    <x-ui.icon name="magnifying-glass" class="field-icon" />
                                    <input
                                        id="search"
                                        type="search"
                                        name="search"
                                        value="{{ request('search') }}"
                                        placeholder="Nama kos atau alamat"
                                        class="field field-with-icon"
                                    >
                                </div>
                            </div>

                            <fieldset>
                                <legend class="field-label">Jenis kos</legend>
                                <div class="space-y-1">
                                    @foreach (['' => 'Semua jenis', 'putra' => 'Putra', 'putri' => 'Putri', 'campur' => 'Campur'] as $value => $label)
                                        <label class="flex cursor-pointer items-center gap-3 rounded-xl px-3 py-2 text-sm transition hover:bg-ink-50">
                                            <input
                                                type="radio"
                                                name="gender_type"
                                                value="{{ $value }}"
                                                @checked(request('gender_type') === $value)
                                                class="size-4 border-ink-300 text-brand-600 focus:ring-2 focus:ring-brand-500/30"
                                            >
                                            <span class="font-medium text-ink-700">{{ $label }}</span>
                                            <span class="ml-auto text-xs font-semibold text-ink-400">
                                                {{ $genderCounts[$value ?: 'all'] ?? 0 }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>

                            <fieldset>
                                <legend class="field-label">Rentang harga / bulan</legend>

                                <div class="flex items-center gap-2">
                                    <input
                                        type="number"
                                        name="min_price"
                                        min="0"
                                        step="50000"
                                        value="{{ request('min_price') }}"
                                        placeholder="Min"
                                        aria-label="Harga minimum"
                                        class="field h-11 px-3 text-[13px]"
                                    >
                                    <span class="text-ink-300">&ndash;</span>
                                    <input
                                        type="number"
                                        name="max_price"
                                        min="0"
                                        step="50000"
                                        value="{{ request('max_price') }}"
                                        placeholder="Maks"
                                        aria-label="Harga maksimum"
                                        class="field h-11 px-3 text-[13px]"
                                    >
                                </div>

                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach ([
                                        ['label' => '< 750rb', 'max' => 750000],
                                        ['label' => '750rb - 1jt', 'min' => 750000, 'max' => 1000000],
                                        ['label' => '> 1jt', 'min' => 1000000],
                                    ] as $chip)
                                        @php
                                            $params = array_filter([
                                                'min_price' => $chip['min'] ?? null,
                                                'max_price' => $chip['max'] ?? null,
                                            ], fn ($v) => $v !== null);
                                            $isActive = (int) request('min_price') === (int) ($chip['min'] ?? 0)
                                                && (int) request('max_price') === (int) ($chip['max'] ?? 0);
                                        @endphp
                                        <a
                                            href="{{ route('kos.index', $params) }}"
                                            class="badge {{ $isActive ? 'bg-brand-600 text-white' : 'bg-ink-100 text-ink-600 ring-1 ring-ink-200' }} transition hover:bg-brand-100"
                                        >
                                            {{ $chip['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </fieldset>

                            <fieldset>
                                <legend class="field-label">Fasilitas</legend>
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse ($facilities as $facility)
                                        @php $isActive = in_array($facility->facility_name, (array) request('facilities', []), true); @endphp
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="facilities[]" value="{{ $facility->facility_name }}" class="peer sr-only" @checked($isActive)>
                                            <span class="badge bg-ink-100 text-ink-600 ring-1 ring-ink-200 transition peer-checked:bg-brand-600 peer-checked:text-white peer-checked:ring-brand-600 hover:bg-ink-200 peer-checked:hover:bg-brand-700">
                                                <x-ui.icon :name="facilityIcon($facility->facility_name)" class="size-3" />
                                                {{ $facility->facility_name }}
                                            </span>
                                        </label>
                                    @empty
                                        <p class="text-xs text-ink-400">Belum ada data fasilitas.</p>
                                    @endforelse
                                </div>
                            </fieldset>

                            <div class="flex gap-2">
                                <button type="submit" class="btn btn-md btn-primary flex-1">
                                    <x-ui.icon name="funnel" class="size-4" />
                                    Terapkan
                                </button>
                                @if ($activeFilterCount > 0)
                                    <a href="{{ route('kos.index') }}" class="btn btn-md btn-outline" title="Reset filter">
                                        <x-ui.icon name="arrow-path" class="size-4" />
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    {{-- Sidebar promo --}}
                    <div class="mt-4 hidden overflow-hidden rounded-4xl bg-ink-gradient p-6 text-white lg:block">
                        <span class="grid size-10 place-items-center rounded-2xl bg-white/10 text-brand-200">
                            <x-ui.icon name="megaphone" class="size-5" />
                        </span>
                        <h3 class="mt-4 text-base font-bold">Pemiliknya di area kamu?</h3>
                        <p class="mt-1.5 text-[13px] leading-relaxed text-ink-400">
                            Pasang listing kos kamu secara gratis dan jangkau mahasiswa yang sedang cari.
                        </p>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-white mt-4 w-full">
                            Pasang Gratis
                        </a>
                    </div>
                </aside>

                {{-- ---------- Results ---------- --}}
                <div>
                    {{-- Result bar --}}
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-ink-500">
                            Menampilkan <span class="font-bold text-ink-900">{{ $kosListings->firstItem() ?? 0 }}&ndash;{{ $kosListings->lastItem() ?? 0 }}</span>
                            dari <span class="font-bold text-ink-900">{{ $kosListings->total() }}</span> kos
                            @if (request('search'))
                                <span class="text-ink-400">untuk &ldquo;{{ request('search') }}&rdquo;</span>
                            @endif
                        </p>

                        <div class="flex items-center gap-2">
                            @if ($activeFilterCount > 0)
                                <a href="{{ route('kos.index') }}" class="btn btn-sm btn-ghost">
                                    <x-ui.icon name="x-mark" class="size-3.5" />
                                    Reset
                                </a>
                            @endif

                            <form method="GET" class="flex items-center gap-2">
                                @foreach (request()->except(['sort', 'page']) as $key => $value)
                                    @if (is_array($value))
                                        @foreach ($value as $v)
                                            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                        @endforeach
                                    @else
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endif
                                @endforeach

                                <label for="sort" class="sr-only">Urutkan</label>
                                <select id="sort" name="sort" onchange="this.form.submit()" class="field h-10 w-auto py-0 pr-9 text-[13px] font-semibold">
                                    @foreach ([
                                        'terbaru' => 'Terbaru',
                                        'termurah' => 'Harga terendah',
                                        'termahal' => 'Harga tertinggi',
                                        'nama' => 'Nama A-Z',
                                    ] as $value => $label)
                                        <option value="{{ $value }}" @selected(request('sort', 'terbaru') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>

                    {{-- Active filter pills --}}
                    @if ($activeFilters->isNotEmpty())
                        <div class="mb-6 flex flex-wrap items-center gap-2">
                            @foreach ($activeFilters as $filter)
                                <a
                                    href="{{ $filter['url'] }}"
                                    class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 py-1.5 pr-2.5 pl-3.5 text-[13px] font-semibold text-brand-700 ring-1 ring-brand-100 transition hover:bg-brand-100"
                                >
                                    {{ $filter['label'] }}
                                    <x-ui.icon name="x-mark" class="size-3.5" />
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Grid --}}
                    @if ($kosListings->isEmpty())
                        @include('partials.empty-state', [
                            'icon' => 'building-office-2',
                            'title' => 'Kos tidak ditemukan',
                            'description' => 'Belum ada kos yang cocok dengan filter ini. Coba longgarkan budget atau ganti jenis kamar.',
                            'actionLabel' => 'Reset filter',
                            'actionHref' => route('kos.index'),
                        ])
                    @else
                        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($kosListings as $kos)
                                @include('partials.kos-card', ['kos' => $kos])
                            @endforeach
                        </div>

                        <div class="mt-12">
                            {{ $kosListings->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

@endsection
