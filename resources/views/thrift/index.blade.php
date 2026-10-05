@extends('layouts.app')

@section('title', 'Katalog Thrift Barang BekasMurah | KosKuy')
@section('meta_description', 'Belanja barang bekas berkualitas: buku, elektronik, furnitur, pakaian, dan olahraga. Kategori rapi, kondisi jelas, harga jujur. Langsung chat penjualnya.')

@section('content')

    {{-- ========================= PAGE HEADER ========================= --}}
    <section class="relative overflow-hidden border-b border-ink-100 bg-ink-950">
        <div class="grid-lines pointer-events-none absolute inset-0" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -top-24 right-10 size-96 rounded-full bg-accent-500/25 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-24 left-10 size-96 rounded-full bg-brand-500/25 blur-3xl" aria-hidden="true"></div>

        <div class="container-page relative py-12 sm:py-16">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-sm text-ink-400">
                <a href="{{ route('home') }}" class="font-semibold transition hover:text-white">Beranda</a>
                <x-ui.icon name="chevron-right" class="size-3.5 text-ink-600" />
                <span class="font-semibold text-white">Thrift</span>
            </nav>

            <div class="mt-5 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="badge border border-white/10 bg-white/10 text-accent-300">
                        <x-ui.icon name="globe-alt" class="size-3.5" />
                        <span class="sr-only">Ramah lingkungan</span>
                        Belanja barang bekas
                    </span>
                    <h1 class="mt-4 text-4xl leading-[1.08] font-extrabold text-white sm:text-5xl">
                        Katalog <span class="text-accent-300">Thrift</span> Terlengkap
                    </h1>
                    <p class="mt-4 max-w-xl text-base leading-relaxed text-ink-400">
                        <span class="font-semibold text-white">{{ $products->total() }} barang</span>
                        dari mahasiswa dan jubel sekitar. Kondisi ditulis jujur, harga masuk akal,
                        dan <span class="font-semibold text-accent-300">tanpa komisi</span>.
                    </p>
                </div>

                <form method="GET" action="{{ route('thrift.index') }}" class="w-full lg:max-w-md">
                    <div class="relative">
                        <x-ui.icon name="magnifying-glass" class="field-icon" />
                        <input
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari buku, kipas, kursi, elektronik…"
                            aria-label="Cari barang thrift"
                            class="h-13 w-full rounded-2xl border border-white/10 bg-white/10 pr-28 pl-12 text-sm text-white placeholder:text-ink-500 focus:border-accent-400 focus:ring-4 focus:ring-accent-500/20 focus:outline-none"
                        >
                        <button type="submit" class="absolute top-1.5 right-1.5 btn btn-md btn-accent">
                            Cari
                        </button>
                    </div>
                </form>
            </div>

            {{-- Category chips --}}
            @if ($categories->isNotEmpty())
                <div class="mt-8 flex flex-wrap items-center gap-2">
                    <a
                        href="{{ route('thrift.index', array_filter(request()->except(['category_id', 'page']))) }}"
                        class="badge {{ request('category_id') ? 'border border-white/10 bg-white/5 text-ink-300' : 'bg-white text-ink-900' }} transition hover:bg-white/90"
                    >
                        Semua
                    </a>
                    @foreach ($categories as $category)
                        @php $isActive = (int) request('category_id') === $category->id; @endphp
                        <a
                            href="{{ route('thrift.index', array_filter(array_merge(request()->except(['category_id', 'page']), ['category_id' => $category->id]))) }}"
                            class="badge {{ $isActive ? 'bg-white text-ink-900' : 'border border-white/10 bg-white/5 text-ink-300' }} transition hover:bg-white/90"
                        >
                            <x-ui.icon :name="categoryIcon($category)" class="size-3.5" />
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ========================= CONTENT ========================= --}}
    <section class="section-tight">
        <div class="container-page">
            <div class="grid gap-8 lg:grid-cols-[260px_1fr] lg:gap-10">

                {{-- Sidebar --}}
                <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
                    <div x-data="filterPanel()" class="card overflow-hidden">
                        <button
                            type="button"
                            @click="toggle()"
                            class="flex w-full items-center justify-between gap-3 px-5 py-4 lg:hidden"
                            :aria-expanded="open ? 'true' : 'false'"
                        >
                            <span class="flex items-center gap-2 text-sm font-bold">
                                <x-ui.icon name="funnel" class="size-4 text-accent-600" />
                                Filter
                            </span>
                            <x-ui.icon name="chevron-down" class="size-4 text-ink-400 transition-transform" ::class="open && 'rotate-180'" />
                        </button>

                        <div x-show="open || desktop" class="space-y-5 border-t border-ink-100 p-5 lg:border-t-0">
                            <div>
                                <label for="thrift-search" class="field-label">Kata kunci</label>
                                <div class="relative">
                                    <x-ui.icon name="magnifying-glass" class="field-icon" />
                                    <input
                                        id="thrift-search"
                                        type="search"
                                        name="search"
                                        value="{{ request('search') }}"
                                        placeholder="Nama barang"
                                        class="field field-with-icon"
                                    >
                                </div>
                            </div>

                            <fieldset>
                                <legend class="field-label">Kategori</legend>
                                <div class="space-y-1">
                                    <label class="flex cursor-pointer items-center gap-3 rounded-xl px-3 py-2 text-sm transition hover:bg-ink-50">
                                        <input
                                            type="radio"
                                            name="category_id"
                                            value=""
                                            @checked(! request('category_id'))
                                            class="size-4 border-ink-300 text-accent-600 focus:ring-2 focus:ring-accent-500/30"
                                        >
                                        <span class="font-medium text-ink-700">Semua kategori</span>
                                    </label>
                                    @foreach ($categories as $category)
                                        <label class="flex cursor-pointer items-center gap-3 rounded-xl px-3 py-2 text-sm transition hover:bg-ink-50">
                                            <input
                                                type="radio"
                                                name="category_id"
                                                value="{{ $category->id }}"
                                                @checked((int) request('category_id') === $category->id)
                                                class="size-4 border-ink-300 text-accent-600 focus:ring-2 focus:ring-accent-500/30"
                                            >
                                            <span class="min-w-0 flex-1 truncate font-medium text-ink-700">{{ $category->name }}</span>
                                            <span class="text-xs font-semibold text-ink-400">{{ $categoryCounts[$category->id] ?? 0 }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>

                            <fieldset>
                                <legend class="field-label">Rentang harga</legend>
                                <div class="flex items-center gap-2">
                                    <input
                                        type="number"
                                        name="min_price"
                                        min="0"
                                        step="10000"
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
                                        step="10000"
                                        value="{{ request('max_price') }}"
                                        placeholder="Maks"
                                        aria-label="Harga maksimum"
                                        class="field h-11 px-3 text-[13px]"
                                    >
                                </div>
                            </fieldset>

                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-ink-100 px-3 py-2.5 text-sm transition hover:bg-ink-50">
                                <input
                                    type="checkbox"
                                    name="available"
                                    value="1"
                                    @checked(request('available'))
                                    class="size-4 rounded border-ink-300 text-accent-600 focus:ring-2 focus:ring-accent-500/30"
                                >
                                <span class="font-medium text-ink-700">Hanya yang belum terjual</span>
                            </label>

                            <div class="flex gap-2">
                                <button type="submit" class="btn btn-md btn-accent flex-1">
                                    <x-ui.icon name="funnel" class="size-4" />
                                    Terapkan
                                </button>
                                @if ($activeFilterCount > 0)
                                    <a href="{{ route('thrift.index') }}" class="btn btn-md btn-outline" title="Reset filter">
                                        <x-ui.icon name="arrow-path" class="size-4" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Eco banner --}}
                    <div class="hidden overflow-hidden rounded-4xl bg-linear-to-br from-accent-600 to-emerald-500 p-6 text-white lg:block">
                        <span class="grid size-10 place-items-center rounded-2xl bg-white/15">
                            <x-ui.icon name="sparkles" class="size-5" />
                        </span>
                        <h3 class="mt-4 text-base font-bold">Punya barang bekas?</h3>
                        <p class="mt-1.5 text-[13px] leading-relaxed text-white/85">
                            Jual dalam hitungan menit. Gratis daftar, tanpa biaya komisi.
                        </p>
                        <a href="{{ route('register') }}" class="btn btn-sm mt-4 w-full bg-white text-accent-700 hover:bg-white/90">
                            Jual Sekarang
                        </a>
                    </div>
                </aside>

                {{-- Results --}}
                <div>
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-ink-500">
                            Menampilkan <span class="font-bold text-ink-900">{{ $products->firstItem() ?? 0 }}&ndash;{{ $products->lastItem() ?? 0 }}</span>
                            dari <span class="font-bold text-ink-900">{{ $products->total() }}</span> barang
                            @if ($activeCategory)
                                <span class="text-ink-400">di {{ $activeCategory->name }}</span>
                            @endif
                        </p>

                        <div class="flex items-center gap-2">
                            @if ($activeFilterCount > 0)
                                <a href="{{ route('thrift.index') }}" class="btn btn-sm btn-ghost">
                                    <x-ui.icon name="x-mark" class="size-3.5" />
                                    Reset
                                </a>
                            @endif

                            <form method="GET" class="flex items-center gap-2">
                                @foreach (request()->except(['sort', 'page']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ is_array($value) ? $value[0] : $value }}">
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

                    @if ($activeFilters->isNotEmpty())
                        <div class="mb-6 flex flex-wrap items-center gap-2">
                            @foreach ($activeFilters as $filter)
                                <a
                                    href="{{ $filter['url'] }}"
                                    class="inline-flex items-center gap-1.5 rounded-full bg-accent-50 py-1.5 pr-2.5 pl-3.5 text-[13px] font-semibold text-accent-700 ring-1 ring-accent-100 transition hover:bg-accent-100"
                                >
                                    {{ $filter['label'] }}
                                    <x-ui.icon name="x-mark" class="size-3.5" />
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if ($products->isEmpty())
                        @include('partials.empty-state', [
                            'icon' => 'tag',
                            'title' => 'Barang tidak ditemukan',
                            'description' => 'Belum ada barang yang cocok. Coba kata kunci lain atau pilih kategori berbeda.',
                            'actionLabel' => 'Lihat semua barang',
                            'actionHref' => route('thrift.index'),
                        ])
                    @else
                        <div class="grid grid-cols-2 gap-5 xl:grid-cols-3">
                            @foreach ($products as $product)
                                @include('partials.thrift-card', ['product' => $product])
                            @endforeach
                        </div>

                        <div class="mt-12">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

@endsection
