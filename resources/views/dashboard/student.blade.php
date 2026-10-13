@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa | KosKuy')
@section('meta_description', 'Rekomendasi kos dan barang thrift pilihan untuk mahasiswa, langsung dari dashboardmu.')

@section('content')

    @php
        $user = auth()->user();
        $roleLabel = 'Mahasiswa / Pencari Kos';
    @endphp

    {{-- ========================= HEADER ========================= --}}
    <section class="relative overflow-hidden border-b border-ink-100 bg-ink-950">
        <div class="grid-lines pointer-events-none absolute inset-0" aria-hidden="true"></div>
        <div class="blob -top-24 -right-16 size-96 bg-brand-500/35 blur-3xl" aria-hidden="true"></div>
        <div class="blob -bottom-32 left-10 size-96 bg-accent-400/25 blur-3xl" aria-hidden="true"></div>

        <div class="container-page relative py-12 sm:py-14">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <x-ui.avatar :user="$user" size="size-16" text="text-2xl" class="font-display font-extrabold text-white ring-white/15" />
                    <div>
                        <span class="badge-glass inline-flex">
                            <x-ui.icon name="academic-cap" class="size-3.5 text-accent-300" />
                            {{ $roleLabel }}
                        </span>
                        <h1 class="mt-2.5 text-2xl font-extrabold text-white sm:text-3xl">
                            Halo, {{ \Illuminate\Support\Str::before($user->name, ' ') }}!
                        </h1>
                        <p class="mt-1 text-sm text-ink-400">
                            Temukan kos dan barang thrift yang pas untuk kebutuhanmu.
                        </p>
                    </div>
                </div>

                <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:flex-wrap">
                    <a href="{{ route('kos.index') }}" class="btn btn-md w-full border border-white/15 bg-white/10 text-white backdrop-blur transition hover:bg-white/20 sm:w-auto">
                        <x-ui.icon name="magnifying-glass" class="size-4" />
                        Cari Kos
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Browse state (Kos / Thrift) shared by the search hero and the recommendations below --}}
    <div x-data="{ type: 'kos' }">

    {{-- ========================= SEARCH ========================= --}}
    <section class="container-page relative z-10 -mt-8">
        <div class="card p-3 sm:p-4">
            <form
                method="GET"
                :action="type === 'kos' ? '{{ route('kos.index') }}' : '{{ route('thrift.index') }}'"
                action="{{ route('kos.index') }}"
                class="flex flex-col gap-3 lg:flex-row lg:items-center"
            >
                <div class="flex shrink-0 rounded-2xl bg-ink-100 p-1">
                    <button
                        type="button"
                        @click="type = 'kos'"
                        :class="type === 'kos' ? 'bg-white text-brand-700 shadow-soft' : 'text-ink-500 hover:text-ink-700'"
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition"
                    >
                        <x-ui.icon name="building-office-2" class="size-4" />
                        Kos
                    </button>
                    <button
                        type="button"
                        @click="type = 'thrift'"
                        :class="type === 'thrift' ? 'bg-white text-violet-700 shadow-soft' : 'text-ink-500 hover:text-ink-700'"
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition"
                    >
                        <x-ui.icon name="tag" class="size-4" />
                        Thrift
                    </button>
                </div>

                <div class="relative flex-1">
                    <x-ui.icon name="magnifying-glass" class="field-icon" />
                    <input
                        type="search"
                        name="search"
                        :placeholder="type === 'kos' ? 'Cari nama kos, lokasi, atau kampus...' : 'Cari barang thrift...'"
                        placeholder="Cari nama kos, lokasi, atau kampus..."
                        class="field field-with-icon"
                    >
                </div>

                <button type="submit" class="btn btn-md btn-primary w-full lg:w-auto">
                    <x-ui.icon name="magnifying-glass" class="size-4" />
                    Cari
                </button>
            </form>
        </div>
    </section>

    {{-- ========================= HIGHLIGHTS ========================= --}}
    <section class="container-page mt-8">
        <div class="grid gap-4 sm:grid-cols-3">
            @foreach ([
                ['label' => 'Kos terbaru', 'value' => $kosListings->count(), 'icon' => 'building-office-2', 'tone' => 'text-brand-600 bg-brand-50'],
                ['label' => 'Barang thrift aktif', 'value' => $thriftProducts->count(), 'icon' => 'tag', 'tone' => 'text-violet-600 bg-violet-50'],
                ['label' => 'Chat pemilik', 'value' => 'Gratis', 'icon' => 'chat-bubble-oval-left', 'tone' => 'text-accent-600 bg-accent-50'],
            ] as $stat)
                <div class="card flex items-center gap-4 p-5">
                    <span class="grid size-11 shrink-0 place-items-center rounded-2xl {{ $stat['tone'] }}">
                        <x-ui.icon :name="$stat['icon']" class="size-5" />
                    </span>
                    <div>
                        <p class="font-display text-2xl font-extrabold text-ink-900">{{ $stat['value'] }}</p>
                        <p class="text-[13px] font-semibold text-ink-500">{{ $stat['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ========================= REKOMENDASI (Kos / Thrift) ========================= --}}
    <section class="section-tight bg-ink-50/60">
        <div class="container-page">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <x-ui.section-heading
                    align="left"
                    eyebrow="Rekomendasi"
                    title="Rekomendasi untukmu"
                    description="Pilih Kos atau Thrift — yang terbaru langsung tampil di bawah."
                />
                <div class="flex shrink-0 rounded-2xl bg-white p-1 ring-1 ring-ink-200">
                    <button
                        type="button"
                        @click="type = 'kos'"
                        :class="type === 'kos' ? 'bg-brand-50 text-brand-700' : 'text-ink-500 hover:text-ink-700'"
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition"
                    >
                        <x-ui.icon name="building-office-2" class="size-4" />
                        Kos
                    </button>
                    <button
                        type="button"
                        @click="type = 'thrift'"
                        :class="type === 'thrift' ? 'bg-violet-50 text-violet-700' : 'text-ink-500 hover:text-ink-700'"
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition"
                    >
                        <x-ui.icon name="tag" class="size-4" />
                        Thrift
                    </button>
                </div>
            </div>

            {{-- Kos --}}
            <div x-show="type === 'kos'">
                <div class="mt-8 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm font-semibold text-ink-500">{{ $kosListings->count() }} listing kos terbaru</p>
                    <a href="{{ route('kos.index') }}" class="btn btn-sm btn-outline">
                        Lihat semua kos
                        <x-ui.icon name="arrow-right" class="size-4" />
                    </a>
                </div>

                @if ($kosListings->isEmpty())
                    <div class="mt-6">
                        @include('partials.empty-state', [
                            'icon' => 'building-office-2',
                            'title' => 'Belum ada kos terbaru',
                            'description' => 'Listing kos akan muncul di sini begitu pemilik memasangnya.',
                            'actionLabel' => 'Jelajahi Katalog Kos',
                            'actionHref' => route('kos.index'),
                        ])
                    </div>
                @else
                    <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($kosListings as $kos)
                            @include('partials.kos-card', ['kos' => $kos])
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Thrift --}}
            <div x-show="type === 'thrift'" x-cloak>
                <div class="mt-8 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm font-semibold text-ink-500">{{ $thriftProducts->count() }} barang thrift aktif</p>
                    <a href="{{ route('thrift.index') }}" class="btn btn-sm btn-outline">
                        Lihat semua thrift
                        <x-ui.icon name="arrow-right" class="size-4" />
                    </a>
                </div>

                @if ($thriftProducts->isEmpty())
                    <div class="mt-6">
                        @include('partials.empty-state', [
                            'icon' => 'tag',
                            'title' => 'Belum ada barang thrift',
                            'description' => 'Barang bekas mahasiswa akan muncul di sini.',
                            'actionLabel' => 'Jelajahi Katalog Thrift',
                            'actionHref' => route('thrift.index'),
                        ])
                    </div>
                @else
                    <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($thriftProducts as $product)
                            @include('partials.thrift-card', ['product' => $product])
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    </div>{{-- /browse state --}}

    {{-- ========================= ACCOUNT ========================= --}}
    <section class="section-tight">
        <div class="container-page">
            <div class="card p-6 sm:p-8">
                <h2 class="text-lg font-extrabold">Data akun</h2>

                <dl class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['label' => 'Nama', 'value' => $user->name, 'icon' => 'users'],
                        ['label' => 'Email', 'value' => $user->email, 'icon' => 'envelope'],
                        ['label' => 'WhatsApp', 'value' => $user->phone ?: 'Belum diisi', 'icon' => 'chat-bubble-oval-left'],
                        ['label' => 'Peran', 'value' => $roleLabel, 'icon' => 'shield-check'],
                    ] as $row)
                        <div class="min-w-0">
                            <dt class="flex items-center gap-2 text-[13px] font-semibold text-ink-400">
                                <x-ui.icon :name="$row['icon']" class="size-3.5" />
                                {{ $row['label'] }}
                            </dt>
                            <dd class="mt-1.5 text-sm font-bold break-words text-ink-900">{{ $row['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>

                <div class="mt-6 flex flex-col gap-4 rounded-2xl border border-brand-100 bg-brand-50/60 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-3">
                        <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-700">
                            <x-ui.icon name="building-office-2" class="size-5" />
                        </span>
                        <div>
                            <p class="text-sm font-bold text-ink-900">Punya kos atau barang untuk ditawarkan?</p>
                            <p class="mt-0.5 text-[13px] leading-relaxed text-ink-500">
                                Pelajari cara jadi mitra KosKuy — sewakan kosmu atau jual barang bekas.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('partner.index') }}" class="btn btn-md btn-primary shrink-0">
                        Jadi mitra
                        <x-ui.icon name="arrow-right" class="size-4" />
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection
