@extends('layouts.app')

@section('title', 'KosKuy — Kos Impian, Thrift Pilihan | Platform Kos & Barang Bekas Mahasiswa')
@section('meta_description', 'KosKuy adalah platform marketplace kos dan barang bekas untuk mahasiswa. Cari kos sesuai budget, bandingkan fasilitas, dan chat pemilik langsung via WhatsApp. 100% gratis, tanpa perantara.')

@section('content')

    {{-- ========================= HERO ========================= --}}
    <section class="relative overflow-hidden bg-white" id="hero">
        {{-- Ambient background --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="dot-grid absolute inset-0 opacity-70"></div>
            <div class="blob -top-40 -left-32 size-[34rem] bg-brand-200/50 animate-blob"></div>
            <div class="blob top-24 -right-24 size-[30rem] bg-accent-200/45 animate-blob" style="animation-delay:-6s"></div>
            <div class="blob bottom-0 left-1/3 size-[22rem] bg-sun-200/35 animate-float-slow"></div>
            <div class="absolute inset-x-0 bottom-0 h-40 bg-linear-to-b from-transparent to-white"></div>
        </div>

        <div class="container-page relative pt-14 pb-20 sm:pt-20 lg:pt-24 lg:pb-28">
            <div class="grid items-center gap-14 lg:grid-cols-12 lg:gap-10">
                {{-- Copy --}}
                <div class="lg:col-span-6">
                    <div class="reveal inline-flex items-center gap-2 rounded-full border border-ink-100 bg-white/80 py-1.5 pr-4 pl-1.5 shadow-soft backdrop-blur">
                        <span class="badge bg-brand-600 px-2.5 py-1 text-white">
                            <x-ui.icon name="sparkles" class="size-3" />
                            Gratis
                        </span>
                        <span class="text-[13px] font-semibold text-ink-600">Sekarang: {{ $stats['kos'] }} kos siap huni</span>
                    </div>

                    <h1 class="reveal mt-6 text-4xl leading-[1.08] font-extrabold tracking-tight text-ink-900 sm:text-5xl lg:text-6xl">
                        Kos<span class="gradient-text">impian</span>,<br>
                        thrift <span class="gradient-text">pilihan</span>.
                    </h1>

                    <p class="reveal lede mt-6 max-w-xl text-base sm:text-lg">
                        Satu tempat untuk cari kos dan barang bekas berkualitas —
                        <span class="font-semibold text-ink-700">gratis</span>, tanpa perantara,
                        langsung chat pemiliknya. Dibuat oleh mahasiswa, untuk mahasiswa.
                    </p>

                    {{-- Search card with Kos / Thrift tabs --}}
                    <div
                        x-data="{ tab: 'kos' }"
                        class="reveal mt-9 max-w-2xl rounded-4xl border border-ink-100 bg-white/90 p-2 shadow-lift backdrop-blur-xl"
                    >
                        <div class="flex gap-1.5 rounded-3xl bg-ink-100/80 p-1.5">
                            <template x-for="item in [{ id: 'kos', label: 'Cari Kos', icon: 'building-office-2' }, { id: 'thrift', label: 'Cari Thrift', icon: 'tag' }]">
                                <button
                                    type="button"
                                    @click="tab = item.id"
                                    :aria-pressed="tab === item.id ? 'true' : 'false'"
                                    class="flex flex-1 items-center justify-center gap-2 rounded-2xl px-4 py-2.5 text-sm font-bold transition-all duration-200"
                                    :class="tab === item.id ? 'bg-white text-brand-700 shadow-soft' : 'text-ink-500 hover:text-ink-800'"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="size-4.5">
                                        <template x-if="item.icon === 'building-office-2'">
                                            <path d="M6 21.75h9.75a.75.75 0 001.5 0V3a.75.75 0 00-.75-.75H6a.75.75 0 00-.75.75v18.75zm0 0v-7.5A.75.75 0 016 13.5h3a.75.75 0 01.75.75v7.5m-3.75 0H3.375m17.25 0h-2.625m0 0h1.5m-1.5 0V3.75m1.5 0V3a.75.75 0 00-.75-.75H14.25a.75.75 0 00-.75.75v18.75M16.5 9h1.5m-1.5 3H18m-3 3h1.5m-1.5 3H18"/>
                                        </template>
                                        <template x-if="item.icon === 'tag'">
                                            <path d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3Z"/>
                                        </template>
                                    </svg>
                                    <span x-text="item.label"></span>
                                </button>
                            </template>
                        </div>

                        {{-- Kos search --}}
                        <form x-show="tab === 'kos'" x-cloak method="GET" action="{{ route('kos.index') }}" class="space-y-3 p-3 sm:p-4">
                            <div class="grid gap-3 sm:grid-cols-[1.6fr_1fr]">
                                <div class="relative">
                                    <x-ui.icon name="magnifying-glass" class="field-icon" />
                                    <input
                                        type="search"
                                        name="search"
                                        value="{{ request('search') }}"
                                        placeholder="Nama kos, daerah, atau alamat…"
                                        class="field field-with-icon"
                                        aria-label="Kata kunci pencarian kos"
                                    >
                                </div>
                                <select name="gender_type" class="field" aria-label="Jenis kos">
                                    <option value="">Semua jenis</option>
                                    <option value="putra" @selected(request('gender_type') === 'putra')>Putra</option>
                                    <option value="putri" @selected(request('gender_type') === 'putri')>Putri</option>
                                    <option value="campur" @selected(request('gender_type') === 'campur')>Campur</option>
                                </select>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-[1fr_auto]">
                                <div class="relative">
                                    <x-ui.icon name="banknotes" class="field-icon" />
                                    <input
                                        type="number"
                                        name="max_price"
                                        min="0"
                                        step="50000"
                                        value="{{ request('max_price') }}"
                                        placeholder="Budget maksimal (Rp)"
                                        class="field field-with-icon"
                                        aria-label="Budget maksimal"
                                    >
                                </div>
                                <button type="submit" class="btn btn-md btn-gradient sm:px-7">
                                    Cari Kos
                                    <x-ui.icon name="arrow-right" class="size-4" />
                                </button>
                            </div>

                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1.5 pt-0.5 text-xs text-ink-400">
                                <span class="font-semibold text-ink-500">Populer:</span>
                                @foreach (['Kos Putra', 'Kos Putri', 'Di bawah Rp 1 juta'] as $i => $term)
                                    @php
                                        $preset = match ($i) {
                                            0 => ['gender_type' => 'putra'],
                                            1 => ['gender_type' => 'putri'],
                                            default => ['max_price' => 1000000],
                                        };
                                    @endphp
                                    <a href="{{ route('kos.index', $preset) }}" class="rounded-full bg-ink-100 px-2.5 py-1 font-semibold text-ink-600 transition hover:bg-brand-100 hover:text-brand-700">
                                        {{ $term }}
                                    </a>
                                @endforeach
                            </div>
                        </form>

                        {{-- Thrift search --}}
                        <form x-show="tab === 'thrift'" x-cloak method="GET" action="{{ route('thrift.index') }}" class="space-y-3 p-3 sm:p-4">
                            <div class="grid gap-3 sm:grid-cols-[1.6fr_1fr]">
                                <div class="relative">
                                    <x-ui.icon name="magnifying-glass" class="field-icon" />
                                    <input
                                        type="search"
                                        name="search"
                                        value="{{ request('search') }}"
                                        placeholder="Buku, kipas, kursi, elektronik…"
                                        class="field field-with-icon"
                                        aria-label="Kata kunci pencarian barang"
                                    >
                                </div>
                                <select name="category_id" class="field" aria-label="Kategori barang">
                                    <option value="">Semua kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected((int) request('category_id') === $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-md btn-accent w-full">
                                <x-ui.icon name="tag" class="size-4" />
                                Lihat Barang Thrift
                            </button>

                            <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                                @foreach ($categories->take(5) as $category)
                                    <a href="{{ route('thrift.index', ['category_id' => $category->id]) }}" class="badge-ink transition hover:bg-accent-50 hover:text-accent-700 hover:ring-accent-100">
                                        <x-ui.icon :name="categoryIcon($category)" class="size-3" />
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        </form>
                    </div>

                    {{-- Trust row --}}
                    <div class="reveal mt-7 flex flex-wrap items-center gap-x-7 gap-y-3">
                        @foreach ([
                            ['icon' => 'banknotes', 'label' => 'Gratis daftar'],
                            ['icon' => 'shield-check', 'label' => 'Tanpa biaya perantara'],
                            ['icon' => 'chat-bubble-oval-left', 'label' => 'Chat langsung ke pemilik'],
                        ] as $point)
                            <span class="inline-flex items-center gap-2 text-[13px] font-semibold text-ink-500">
                                <span class="grid size-7 place-items-center rounded-lg bg-accent-50 text-accent-600">
                                    <x-ui.icon :name="$point['icon']" class="size-4" />
                                </span>
                                {{ $point['label'] }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Phone mock-up --}}
                <div class="reveal lg:col-span-6">
                    <div class="relative mx-auto max-w-sm lg:max-w-none">
                        {{-- Floating stat card --}}
                        <div class="absolute -top-4 -left-4 z-20 hidden animate-float rounded-2xl border border-white/70 bg-white/90 p-3.5 shadow-lift backdrop-blur-xl sm:block">
                            <div class="flex items-center gap-3">
                                <span class="grid size-10 place-items-center rounded-xl bg-accent-50 text-accent-600">
                                    <x-ui.icon name="arrow-trending-up" class="size-5" />
                                </span>
                                <div>
                                    <p class="text-lg font-extrabold leading-none text-ink-900">{{ $stats['thrift'] }}</p>
                                    <p class="mt-1 text-[11px] font-semibold text-ink-400">barang thrift</p>
                                </div>
                            </div>
                        </div>

                        {{-- Device --}}
                        <div class="relative mx-auto w-[290px] rounded-[2.75rem] bg-ink-950 p-2.5 shadow-[0_40px_90px_-30px_rgb(23_28_43/0.55)] sm:w-[320px]">
                            <div class="absolute inset-x-0 top-2.5 z-10 mx-auto h-5 w-24 rounded-full bg-ink-950"></div>

                            <div class="relative overflow-hidden rounded-[2.25rem] bg-white">
                                {{-- App bar --}}
                                <div class="flex items-center justify-between bg-brand-gradient px-5 pt-9 pb-5 text-white">
                                    <div class="flex items-center gap-2">
                                        <span class="grid size-8 place-items-center rounded-xl bg-white/20">
                                            <img src="{{ asset('images/logo.svg') }}" alt="" class="size-5" aria-hidden="true">
                                        </span>
                                        <span class="text-sm font-extrabold">KosKuy</span>
                                    </div>
                                    <span class="rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-bold">Magelang</span>
                                </div>

                                {{-- Content --}}
                                <div class="space-y-3 bg-ink-50 p-4 pb-6">
                                    <div class="flex items-center gap-2 rounded-xl border border-ink-100 bg-white px-3 py-2">
                                        <x-ui.icon name="magnifying-glass" class="size-4 text-ink-400" />
                                        <span class="text-xs text-ink-400">Cari kos di sekitar…</span>
                                    </div>

                                    @foreach ($kosListings->take(3) as $item)
                                        @php $img = listingImage($item); @endphp
                                        <div class="overflow-hidden rounded-2xl border border-ink-100 bg-white shadow-xs">
                                            <div class="media h-24 {{ $img ? '' : 'bg-brand-soft' }}">
                                                @if ($img)
                                                    <img src="{{ $img }}" alt="" class="media-img" loading="lazy">
                                                @else
                                                    <span class="absolute inset-0 grid place-items-center text-brand-300">
                                                        <x-ui.icon name="building-office-2" class="size-7" />
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="space-y-1.5 p-3">
                                                <p class="line-clamp-1 text-[13px] font-bold text-ink-900">{{ $item->title }}</p>
                                                <div class="flex items-center justify-between">
                                                    <p class="text-[13px] font-extrabold text-brand-700">{{ rupiah($item->price_per_month) }}</p>
                                                    <span class="rounded-full bg-accent-50 px-2 py-0.5 text-[10px] font-bold text-accent-700">{{ genderLabel($item->gender_type) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Floating WhatsApp card --}}
                        <div class="absolute -right-2 bottom-16 z-20 hidden animate-float-slow rounded-2xl border border-white/70 bg-white/95 p-3.5 shadow-lift backdrop-blur-xl sm:block">
                            <div class="flex items-center gap-3">
                                <span class="relative grid size-10 place-items-center rounded-xl bg-accent-500 text-white">
                                    <x-ui.icon name="chat-bubble-oval-left" class="size-5" />
                                    <span class="absolute -top-0.5 -right-0.5 size-3 rounded-full border-2 border-white bg-accent-400"></span>
                                </span>
                                <div>
                                    <p class="text-[13px] font-bold text-ink-900">Pemilik membalas</p>
                                    <p class="text-[11px] text-ink-400">biasanya &lt; 5 menit</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================= MARQUEE ========================= --}}
    <section class="border-y border-ink-100 bg-ink-50/70 py-6">
        <div class="marquee overflow-hidden">
            <div class="marquee-track">
                {{-- NB: the counter must not be called $loop — Blade owns that variable and
     rebinding it inside a nested @foreach turns this loop into an infinite one. --}}
                @for ($copy = 0; $copy < 2; $copy++)
                    @foreach ([
                        'Wi-Fi Cepat', 'Kamar mandi dalam', 'AC', 'Dapur Bersama', 'Laundry', 'Area belajar',
                        'Buku Bekas', 'Meja Belajar', 'Kipas Angin', 'Lampu Tidur', 'Kursi Lipat', 'Elektronik Murah',
                    ] as $item)
                        <span class="inline-flex items-center gap-2 rounded-full border border-ink-100 bg-white px-4 py-2 text-sm font-semibold whitespace-nowrap text-ink-600 shadow-xs">
                            <x-ui.icon name="check-badge" class="size-4 text-accent-500" />
                            {{ $item }}
                        </span>
                    @endforeach
                @endfor
            </div>
        </div>
    </section>

    {{-- ========================= STATS ========================= --}}
    <section class="bg-white py-14 sm:py-16">
        <div class="container-page">
            <div class="reveal grid grid-cols-2 gap-4 rounded-4xl border border-ink-100 bg-ink-50/60 p-6 sm:gap-6 lg:grid-cols-4 lg:p-8">
                @foreach ([
                    ['value' => $stats['kos'], 'label' => 'Unit kos terdaftar', 'icon' => 'building-office-2', 'tone' => 'text-brand-600 bg-brand-50'],
                    ['value' => $stats['thrift'], 'label' => 'Barang thrift aktif', 'icon' => 'tag', 'tone' => 'text-accent-600 bg-accent-50'],
                    ['value' => $stats['categories'], 'label' => 'Kategori barang', 'icon' => 'view-columns', 'tone' => 'text-sun-600 bg-sun-50'],
                    ['value' => 0, 'label' => 'Biaya iklan', 'icon' => 'shield-check', 'tone' => 'text-violet-600 bg-violet-50', 'suffix' => 'Rp'],
                ] as $stat)
                    <div class="text-center">
                        <span class="mx-auto grid size-11 place-items-center rounded-2xl {{ $stat['tone'] }}">
                            <x-ui.icon :name="$stat['icon']" class="size-5" />
                        </span>
                        <p class="mt-3 font-display text-3xl font-extrabold text-ink-900 sm:text-4xl">
                            @if ($stat['value'] === 0 && isset($stat['suffix']))
                                <span class="text-brand-600">{{ $stat['suffix'] }}</span>0
                            @else
                                <span x-data="countUp({{ (int) $stat['value'] }})" x-text="shown">0</span>
                            @endif
                        </p>
                        <p class="mt-1 text-[13px] font-semibold text-ink-500">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================= TWO PILLARS ========================= --}}
    <section class="section bg-white" id="tentang">
        <div class="container-page">
            <x-ui.section-heading
                eyebrow="Dua layanan, satu platform"
                title="Kos untuk <span class='gradient-text'>tinggal</span>, thrift untuk <span class='gradient-text'>hemat</span>"
                description="KosKuy menyatukan dua kebutuhan mahasiswa yang biasanya dicari di tempat berbeda — supaya hemat waktu, hemat tenaga, dan hemat uang."
            />

            <div class="mt-14 grid gap-6 lg:grid-cols-2">
                @foreach ([
                    [
                        'icon' => 'home',
                        'title' => 'Cari Kos',
                        'desc' => 'Filter berdasarkan jenis, harga, dan kata kunci. Lihat foto, fasilitas, serta lokasi REAL sebelum menghubungi pemilik. Tidak ada biaya apa pun.',
                        'href' => route('kos.index'),
                        'cta' => 'Mulai cari kos',
                        'points' => ['Foto & fasilitas lengkap', 'Filter harga & gender', 'Lokasi peta interaktif'],
                        'accent' => 'from-brand-600 to-violet-600',
                        'chip' => 'bg-brand-50 text-brand-700 ring-brand-100',
                        'chipIcon' => 'building-office-2',
                        'stat' => $stats['kos'] . ' unit tersedia',
                    ],
                    [
                        'icon' => 'tag',
                        'title' => 'Belanja Thrift',
                        'desc' => 'Buku, elektronik, furnitur, hingga pakaian — semua dari mahasiswa dan jubel di sekitarmu. Kategori rapi, kondisi jelas, harga jujur.',
                        'href' => route('thrift.index'),
                        'cta' => 'Lihat katalog',
                        'points' => ['6+ kategori lengkap', 'Kondisi barang tertera', 'Chat penjual langsung'],
                        'accent' => 'from-accent-600 to-emerald-500',
                        'chip' => 'bg-accent-50 text-accent-700 ring-accent-100',
                        'chipIcon' => 'tag',
                        'stat' => $stats['thrift'] . ' barang aktif',
                    ],
                ] as $i => $pillar)
                    <article class="reveal group relative flex flex-col overflow-hidden rounded-4xl border border-ink-100 bg-white p-8 shadow-soft transition-all duration-300 hover:-translate-y-1 hover:shadow-lift sm:p-10">
                        <div class="pointer-events-none absolute inset-x-0 top-0 h-1.5 bg-linear-to-r {{ $pillar['accent'] }}"></div>

                        <div class="flex items-start justify-between gap-4">
                            <span class="grid size-14 place-items-center rounded-3xl bg-linear-to-br {{ $pillar['accent'] }} text-white shadow-card">
                                <x-ui.icon :name="$pillar['icon']" class="size-7" />
                            </span>
                            <span class="badge {{ $pillar['chip'] }} ring-1">
                                <x-ui.icon :name="$pillar['chipIcon']" class="size-3.5" />
                                {{ $pillar['stat'] }}
                            </span>
                        </div>

                        <h3 class="mt-6 text-2xl font-extrabold sm:text-3xl">{{ $pillar['title'] }}</h3>
                        <p class="mt-3 text-[15px] leading-relaxed text-ink-500">{{ $pillar['desc'] }}</p>

                        <ul class="mt-6 space-y-2.5">
                            @foreach ($pillar['points'] as $point)
                                <li class="flex items-center gap-2.5 text-sm font-semibold text-ink-600">
                                    <span class="grid size-5 place-items-center rounded-full bg-accent-50 text-accent-600">
                                        <x-ui.icon name="check" class="size-3" />
                                    </span>
                                    {{ $point }}
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ $pillar['href'] }}" class="btn btn-md btn-primary mt-8 self-start">
                            {{ $pillar['cta'] }}
                            <x-ui.icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-1" />
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================= FEATURED KOS ========================= --}}
    @if ($kosListings->isNotEmpty())
        <section class="section bg-ink-50/60" id="kos-terbaru">
            <div class="container-page">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                    <x-ui.section-heading
                        align="left"
                        eyebrow="Baru masuk"
                        title="Kos <span class='gradient-text'>terbaru</span>"
                        description="Listing yang baru ditambahkan pemilik kos minggu ini."
                        class="sm:max-w-xl"
                    />

                    <a href="{{ route('kos.index') }}" class="btn btn-md btn-outline shrink-0 self-start sm:self-auto">
                        Lihat semua kos
                        <x-ui.icon name="arrow-right" class="size-4" />
                    </a>
                </div>

                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($kosListings as $kos)
                        @include('partials.kos-card', ['kos' => $kos])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ========================= FEATURED THRIFT ========================= --}}
    @if ($thriftProducts->isNotEmpty())
        <section class="section bg-white" id="thrift-terbaru">
            <div class="container-page">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                    <x-ui.section-heading
                        align="left"
                        eyebrow="Belanja hemat"
                        title="Thrift <span class='gradient-text'>fresh</span>"
                        description="Barang bekas berkualitas dari mahasiswa dan jubel di sekitarmu, dengan kondisi yang ditulis jujur oleh penjualnya."
                        class="sm:max-w-xl"
                    />

                    <a href="{{ route('thrift.index') }}" class="btn btn-md btn-outline shrink-0 self-start sm:self-auto">
                        Lihat katalog
                        <x-ui.icon name="arrow-right" class="size-4" />
                    </a>
                </div>

                <div class="mt-12 grid gap-5 grid-cols-2 lg:grid-cols-4">
                    @foreach ($thriftProducts as $product)
                        @include('partials.thrift-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ========================= HOW IT WORKS ========================= --}}
    <section class="section relative overflow-hidden bg-ink-950" id="cara-kerja">
        <div class="grid-lines pointer-events-none absolute inset-0" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -top-20 left-1/4 size-96 rounded-full bg-brand-600/25 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute right-0 -bottom-24 size-96 rounded-full bg-accent-500/20 blur-3xl" aria-hidden="true"></div>

        <div class="container-page relative">
            <div class="reveal mx-auto max-w-2xl text-center">
                <span class="eyebrow border border-white/10 bg-white/10 text-brand-200 ring-0">
                    <span class="size-1.5 rounded-full bg-accent-400"></span>
                    Proses sederhana
                </span>
                <h2 class="heading-lg mt-5 text-white">
                    Dari cari sampai tinggal, cuma <span class="text-brand-300">3 langkah</span>
                </h2>
                <p class="lede mt-4 text-ink-400">
                    Tidak ada formulir panjang, tidak ada biaya tersembunyi.
                </p>
            </div>

            <div class="mt-16 grid gap-6 md:grid-cols-3">
                @foreach ([
                    ['n' => '01', 'icon' => 'magnifying-glass', 'title' => 'Cari & bandingkan', 'desc' => 'Gunakan filter jenis kos, rentang harga, dan kata kunci. Bandingkan beberapa listing sekaligus.'],
                    ['n' => '02', 'icon' => 'chat-bubble-oval-left', 'title' => 'Chat pemilik', 'desc' => 'Klik tombol Chat — pesan otomatis terisi. Tanyakan availability, biaya tambahan, atau alamat lengkap.'],
                    ['n' => '03', 'icon' => 'check-badge', 'title' => 'Sepakat & beres', 'desc' => 'Sepakati, lalu bayar langsung ke pemilik. KosKuy tidak mengambil komisi apa pun.'],
                ] as $step)
                    <div class="reveal group relative rounded-4xl border border-white/10 bg-white/[0.04] p-7 transition-colors duration-300 hover:border-brand-400/40 hover:bg-white/[0.07]">
                        <span class="absolute top-6 right-7 font-display text-4xl font-extrabold text-white/10 transition-colors group-hover:text-brand-400/30">
                            {{ $step['n'] }}
                        </span>
                        <span class="grid size-12 place-items-center rounded-2xl bg-brand-500/20 text-brand-200 ring-1 ring-brand-400/30">
                            <x-ui.icon :name="$step['icon']" class="size-6" />
                        </span>
                        <h3 class="mt-5 text-lg font-bold text-white">{{ $step['title'] }}</h3>
                        <p class="mt-2.5 text-sm leading-relaxed text-ink-400">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================= BENTO FEATURES ========================= --}}
    <section class="section bg-white" id="keunggulan">
        <div class="container-page">
            <x-ui.section-heading
                eyebrow="Kenapa KosKuy"
                title="Dibuat <span class='gradient-text'>sederhana</span>, bukan <span class='gradient-text'>rumit</span>"
                description="Kami buang semua yang tidak perlu, supaya kamu fokus pada satu hal: mendapat tempat tinggal dan barang yang layak."
            />

            <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['icon' => 'banknotes', 'title' => 'Selalu gratis', 'desc' => 'Tidak ada biaya pendaftaran, biaya langganan, atau komisi. MAU Pun untuk pemilik kos.', 'tone' => 'bg-brand-50 text-brand-600', 'span' => 'sm:col-span-2 lg:col-span-1'],
                    ['icon' => 'map-pin', 'title' => 'Peta tanpa API key', 'desc' => 'Lokasi kos ditampilkan langsung di peta interaktif, plus tautan rute ke Google Maps.', 'tone' => 'bg-accent-50 text-accent-600', 'span' => ''],
                    ['icon' => 'shield-check', 'title' => 'Kontak langsung', 'desc' => 'Nomor WA pemilik hanya ditampilkan sebagai tautan wa.me — data tidak dibocorkan.', 'tone' => 'bg-sun-50 text-sun-600', 'span' => ''],
                    ['icon' => 'device-phone-mobile', 'title' => 'Siap untuk mobile', 'desc' => 'REST API v1 sudah tersedia untuk aplikasi Flutter, dan tampilan web ini responsif penuh.', 'tone' => 'bg-violet-50 text-violet-600', 'span' => ''],
                    ['icon' => 'bolt', 'title' => 'Muat cepat', 'desc' => 'Aset lokal, tanpa CDN berat. Halaman tampil-optimal di jaringan kampus yang lambat.', 'tone' => 'bg-ink-100 text-ink-700', 'span' => ''],
                    ['icon' => 'fire', 'title' => 'Listing selalu segar', 'desc' => 'Kos baru dan barang thrift ditampilkan berdasarkan waktu unggah, jadi yang paling baru selalu muncul duluan.', 'tone' => 'bg-rose-50 text-rose-600', 'span' => 'sm:col-span-2 lg:col-span-1'],
                ] as $feature)
                    <article class="reveal card card-hover {{ $feature['span'] }} p-7">
                        <span class="grid size-12 place-items-center rounded-2xl {{ $feature['tone'] }}">
                            <x-ui.icon :name="$feature['icon']" class="size-6" />
                        </span>
                        <h3 class="mt-5 text-lg font-bold">{{ $feature['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-500">{{ $feature['desc'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================= TESTIMONI ========================= --}}
    <section class="section bg-ink-50/60" id="testimoni">
        <div class="container-page">
            <x-ui.section-heading
                eyebrow="Suara mahasiswa"
                title="Cerita dari <span class='gradient-text'>pengguna</span>"
                description="Tiga momen kecil yang membuat kami terus mengembangkan KosKuy."
            />

            <div class="mt-10 flex justify-center">
                <span class="badge-sun">
                    <x-ui.icon name="information-circle" class="size-3.5" />
                    Contoh tampilan — ganti dengan testimoni asli sebelum kampanye resmi
                </span>
            </div>

            <div class="mt-8 grid gap-6 md:grid-cols-3">
                @foreach ([
                    [
                        'name' => 'Rangga P.',
                        'meta' => 'Mahasiswa Teknik, Universitas Tidar',
                        'text' => 'Dulu saya keliling komplek selama dua minggu hanya untuk cari kos. Di KosKuy saya bisa membandingkan tiga listing dalam satu malam, lalu chat pemilik lewat WhatsApp.',
                        'tone' => 'bg-brand-600',
                    ],
                    [
                        'name' => 'Salsabila N.',
                        'meta' => 'Mahasiswa Sistem Informasi, Universitas Amikom Magelang',
                        'text' => 'Buku dan kipas bekas yang saya cari akhirnya ketemu di sini. Kondisinya ditulis jujur oleh penjualnya, jadi tidak wasting time untuk nego asal-nego.',
                        'tone' => 'bg-accent-600',
                    ],
                    [
                        'name' => 'Pak Rudi',
                        'meta' => 'Pemilik kos, 6 unit',
                        'text' => 'Saya memasang enam kamar dalam seminggu tanpa biaya iklan. Mahasiswa baru bisa langsung chat saya kalau ada kamar kosong.',
                        'tone' => 'bg-violet-600',
                    ],
                ] as $item)
                    <figure class="reveal card card-hover relative flex flex-col p-7">
                        <x-ui.icon name="sparkles" class="size-7 text-brand-200" />

                        <blockquote class="mt-4 flex-1 text-[15px] leading-relaxed text-ink-600">
                            {{ $item['text'] }}
                        </blockquote>

                        <figcaption class="mt-6 flex items-center gap-3 border-t border-ink-100 pt-5">
                            <span class="grid size-11 shrink-0 place-items-center rounded-2xl text-sm font-bold text-white {{ $item['tone'] }}">
                                {{ initials($item['name']) }}
                            </span>
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-bold text-ink-900">{{ $item['name'] }}</span>
                                <span class="block truncate text-xs text-ink-400">{{ $item['meta'] }}</span>
                            </span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================= FAQ ========================= --}}
    <section class="section bg-white" id="faq">
        <div class="container-narrow">
            <x-ui.section-heading
                eyebrow="Pertanyaan umum"
                title="Yang <span class='gradient-text'>sering</span> ditanya"
                description="Kalau pertanyaanmu belum ada di sini, chat saja langsung ke tim kami."
            />

            <div class="reveal mt-12 divide-y divide-ink-100 overflow-hidden rounded-4xl border border-ink-100 bg-white shadow-soft">
                @foreach ([
                    ['q' => 'Apakah KosKuy benar-benar gratis?', 'a' => 'Ya, sepenuhnya. Pendaftaran, penempatan listing, dan chatting tidak dipungut biaya. Kami ingin jumlah penggunanya tumbuh, bukan dari potongan transaksi.'],
                    ['q' => 'Saya pemilik kos, bagaimana cara mendaftarkannya?', 'a' => 'Buat akun lewat halaman Daftar, lalu masuk ke Dashboard. Setelah itu tambahkan unit kos beserta foto, harga, jenis kamar, dan fasilitasnya.'],
                    ['q' => 'Apakah saya bisa melihat lokasi kos di peta?', 'a' => 'Bisa. Listing yang punya koordinat menampilkan peta interaktif di halaman detail, plus tombol rute yang membuka Google Maps.'],
                    ['q' => 'Bagaimana cara menghubungi pemilik?', 'a' => 'Setiap listing punya tombol Chat yang membuka WhatsApp dengan pesan otomatis terisi. Kamu tinggal kirim dan melanjutkan obrolan di sana.'],
                    ['q' => 'Apakah barang thrift bergaransi?', 'a' => 'Tidak ada garansi resmi dari platform karena transaksi dilakukan langsung antar pembeli dan penjual. Karena itu kondisi setiap barang selalu dituliskan sedetail mungkin oleh penjualnya.'],
                    ['q' => 'Sudah ada aplikasi mobile-nya?', 'a' => 'Versi web ini sudah responsif dan bisa dipasang ke layar utama. Aplikasi native untuk Android dan iOS sedang dikembangkan, dan API v1-nya sudah siap dipakai.'],
                ] as $faq)
                    <div x-data="{ open: false }">
                        <button
                            type="button"
                            @click="open = !open"
                            class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left transition hover:bg-ink-50 sm:px-7"
                            :aria-expanded="open ? 'true' : 'false'"
                        >
                            <span class="text-[15px] font-bold text-ink-800">{{ $faq['q'] }}</span>
                            <span
                                class="grid size-8 shrink-0 place-items-center rounded-full transition-colors duration-200"
                                :class="open ? 'bg-brand-600 text-white' : 'bg-ink-100 text-ink-500'"
                            >
                                <x-ui.icon name="chevron-down" class="size-4 transition-transform duration-300" ::class="open && 'rotate-180'" />
                            </span>
                        </button>

                        <div x-show="open" x-collapse.duration.300ms>
                            <p class="px-6 pb-6 text-sm leading-relaxed text-ink-500 sm:px-7">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================= TERMS & PRIVACY ========================= --}}
    <section class="bg-white pb-20" id="legal">
        <div class="container-page">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-4xl border border-ink-100 bg-ink-50/50 p-7 sm:p-8">
                    <span class="grid size-11 place-items-center rounded-2xl bg-brand-50 text-brand-600">
                        <x-ui.icon name="lock-closed" class="size-5" />
                    </span>
                    <h3 class="text-lg font-extrabold">Kebijakan Privasi</h3>
                    <p class="mt-1 text-[13px] text-ink-400">Berlaku sejak {{ now()->translatedFormat('F Y') }}</p>
                    <ul class="mt-4 space-y-3">
                        @foreach ([
                            'Kami hanya menyimpan data yang kamu isi saat mendaftar: nama, email, dan nomor WhatsApp.',
                            'Nomormu hanya dipakai untuk komunikasi listing dan tidak dibagikan ke pihak ketiga.',
                            'Kamu bisa meminta data dihapus kapan saja dengan menghubungi email kami.',
                        ] as $point)
                            <li class="flex gap-2.5 text-sm leading-relaxed text-ink-600">
                                <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-brand-400"></span>
                                {{ $point }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-4xl border border-ink-100 bg-ink-50/50 p-7 sm:p-8">
                    <span class="grid size-11 place-items-center rounded-2xl bg-accent-50 text-accent-600">
                        <x-ui.icon name="shield-check" class="size-5" />
                    </span>
                    <h3 class="mt-4 text-lg font-extrabold">Syarat &amp; Ketentuan</h3>
                    <ul class="mt-4 space-y-3">
                        @foreach ([
                            'KosKuy adalah platform informasi, bukan agen atau makelar.',
                            'Harga, ketersediaan, dan kondisi listing menjadi tanggung jawab pemilik listing.',
                            'Transaksi dan komunikasi dilakukan langsung antar pengguna.',
                            'Listing yang menipu, fiktif, atau melanggar aturan akan dihapus.',
                        ] as $term)
                            <li class="flex gap-2.5 text-sm leading-relaxed text-ink-600">
                                <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-accent-400"></span>
                                {{ $term }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            </div>
    </section>

    {{-- ========================= FINAL CTA ========================= --}}
    <section class="relative overflow-hidden bg-white pb-24">
        <div class="container-page">
            <div class="reveal relative overflow-hidden rounded-5xl bg-ink-gradient px-6 py-14 text-center sm:px-12 lg:py-20">
                <div class="grid-lines pointer-events-none absolute inset-0 opacity-70" aria-hidden="true"></div>
                <div class="blob -top-24 -left-16 size-80 bg-brand-500/40 blur-3xl" aria-hidden="true"></div>
                <div class="blob -right-16 -bottom-24 size-80 bg-accent-400/30 blur-3xl" aria-hidden="true"></div>

                <div class="relative">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-xs font-bold tracking-wide text-white uppercase backdrop-blur">
                        <x-ui.icon name="rocket-launch" class="size-3.5" />
                        Ayo mulai
                    </span>

                    <h2 class="mx-auto mt-6 max-w-3xl text-3xl leading-tight font-extrabold text-white sm:text-4xl lg:text-5xl">
                        Kos bulan depan jangan sampai<br class="hidden sm:block"> terlewat
                    </h2>

                    <p class="mx-auto mt-5 max-w-xl text-[15px] leading-relaxed text-ink-300 sm:text-base">
                        Ribuan mahasiswa sudah mulai mencari lewat KosKuy. Kamu tinggal buka browser — gratis, tanpa daftar, tanpa biaya.
                    </p>

                    <div class="mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row">
                        <a href="{{ route('kos.index') }}" class="btn btn-lg btn-white w-full sm:w-auto">
                            <x-ui.icon name="magnifying-glass" class="size-5" />
                            Cari Kos Sekarang
                        </a>
                        <a href="{{ route('thrift.index') }}" class="btn btn-lg w-full border border-white/20 bg-white/10 text-white backdrop-blur transition hover:bg-white/20 sm:w-auto">
                            <x-ui.icon name="tag" class="size-5" />
                            Belanja Thrift
                        </a>
                    </div>

                    <p class="mt-6 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-xs font-semibold text-ink-400">
                        <span class="inline-flex items-center gap-1.5"><x-ui.icon name="check" class="size-3.5 text-accent-400" /> Tanpa biaya</span>
                        <span class="inline-flex items-center gap-1.5"><x-ui.icon name="check" class="size-3.5 text-accent-400" /> Tanpa perantara</span>
                        <span class="inline-flex items-center gap-1.5"><x-ui.icon name="check" class="size-3.5 text-accent-400" /> Chat langsung</span>
                    </p>
                </div>
            </div>
        </div>
    </section>

@endsection
