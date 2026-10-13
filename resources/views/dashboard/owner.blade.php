@extends('layouts.app')

@section('title', 'Dashboard Pemilik | KosKuy')
@section('meta_description', 'Kelola listing kos dan barang thrift kamu dari satu dashboard pemilik.')

@section('content')

    @php
        $user = auth()->user();
        $roleLabel = 'Pemilik / Pengelola Kos';
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
                            <x-ui.icon name="building-office-2" class="size-3.5 text-accent-300" />
                            {{ $roleLabel }}
                        </span>
                        <h1 class="mt-2.5 text-2xl font-extrabold text-white sm:text-3xl">
                            Halo, {{ \Illuminate\Support\Str::before($user->name, ' ') }}!
                        </h1>
                        <p class="mt-1 text-sm text-ink-400">
                            Pantau hunian, pembayaran, dan listing kosmu di sini.
                        </p>
                    </div>
                </div>

                <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:flex-wrap">
                    <a href="{{ url('/admin') }}" class="btn btn-md w-full border border-white/15 bg-white/10 text-white backdrop-blur transition hover:bg-white/20 sm:w-auto">
                        <x-ui.icon name="computer-desktop" class="size-4" />
                        Kelola di Panel
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================= RENTAL STATS ========================= --}}
    <section class="container-page -mt-8 relative z-10">
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ([
                ['label' => 'Total kamar', 'value' => $rental['rooms_total'], 'icon' => 'home-modern', 'tone' => 'text-brand-600 bg-brand-50'],
                ['label' => 'Terisi', 'value' => $rental['rooms_occupied'], 'icon' => 'check-badge', 'tone' => 'text-accent-600 bg-accent-50'],
                ['label' => 'Kosong', 'value' => $rental['rooms_vacant'], 'icon' => 'key', 'tone' => 'text-sun-600 bg-sun-50'],
                ['label' => 'Penghuni aktif', 'value' => $rental['tenants_active'], 'icon' => 'users', 'tone' => 'text-violet-600 bg-violet-50'],
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

    {{-- ========================= OCCUPANCY + FINANCE ========================= --}}
    <section class="section-tight">
        <div class="container-page">
            <div class="grid gap-6 lg:grid-cols-12">
                {{-- Occupancy --}}
                <div class="card p-6 sm:p-8 lg:col-span-5">
                    <div class="flex items-center gap-3">
                        <span class="grid size-10 place-items-center rounded-2xl bg-brand-50 text-brand-600">
                            <x-ui.icon name="chart-pie" class="size-5" />
                        </span>
                        <h2 class="text-lg font-extrabold">Tingkat hunian</h2>
                    </div>

                    <p class="mt-6 font-display text-4xl font-extrabold text-ink-900">{{ $rental['occupancy_rate'] }}%</p>
                    <p class="mt-1 text-[13px] font-semibold text-ink-500">
                        {{ $rental['rooms_occupied'] }} dari {{ $rental['rooms_total'] }} kamar terisi
                    </p>

                    <div class="mt-4 h-2.5 w-full overflow-hidden rounded-full bg-ink-100">
                        <div class="h-full rounded-full bg-brand-500" style="width: {{ $rental['occupancy_rate'] }}%"></div>
                    </div>
                </div>

                {{-- Finance --}}
                <div class="card p-6 sm:p-8 lg:col-span-7">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="grid size-10 place-items-center rounded-2xl bg-accent-50 text-accent-600">
                                <x-ui.icon name="receipt-percent" class="size-5" />
                            </span>
                            <h2 class="text-lg font-extrabold">Keuangan {{ $rental['period_label'] }}</h2>
                        </div>
                        <a href="{{ url('/admin/payments') }}" class="hidden text-[13px] font-semibold text-brand-700 hover:underline sm:inline">Kelola pembayaran</a>
                    </div>

                    <dl class="mt-6 grid gap-5 sm:grid-cols-3">
                        @foreach ([
                            ['label' => 'Target iuran', 'value' => rupiah($rental['expected_monthly']), 'tone' => 'text-ink-900'],
                            ['label' => 'Terkumpul', 'value' => rupiah($rental['collected_monthly']), 'tone' => 'text-accent-700'],
                            ['label' => 'Tunggakan', 'value' => rupiah($rental['outstanding_amount']), 'tone' => 'text-rose-600'],
                        ] as $finance)
                            <div class="rounded-2xl bg-ink-50 p-4">
                                <dt class="text-[13px] font-semibold text-ink-400">{{ $finance['label'] }}</dt>
                                <dd class="mt-1.5 font-display text-xl font-extrabold {{ $finance['tone'] }}">{{ $finance['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>

            {{-- ========================= REPORTS + ACTIVITY ========================= --}}
            <div class="mt-6 grid gap-6 lg:grid-cols-12">
                {{-- Outstanding tenants --}}
                <div class="card p-6 sm:p-8 lg:col-span-7">
                    <div class="flex items-center gap-3">
                        <span class="grid size-10 place-items-center rounded-2xl bg-rose-50 text-rose-600">
                            <x-ui.icon name="clipboard-document-list" class="size-5" />
                        </span>
                        <h2 class="text-lg font-extrabold">Belum bayar bulan ini</h2>
                    </div>

                    @if ($rental['outstanding_tenants']->isEmpty())
                        <p class="mt-5 flex items-center gap-2.5 rounded-2xl bg-accent-50 p-4 text-[13px] font-semibold text-accent-700">
                            <x-ui.icon name="check-circle" class="size-4 shrink-0" />
                            Semua penghuni aktif sudah lunas untuk periode ini.
                        </p>
                    @else
                        <ul class="mt-5 divide-y divide-ink-100">
                            @foreach ($rental['outstanding_tenants'] as $tenant)
                                <li class="flex items-center justify-between gap-4 py-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-ink-900">{{ $tenant->name }}</p>
                                        <p class="mt-0.5 truncate text-xs text-ink-400">
                                            {{ $tenant->kosListing?->title ?? '—' }}@if ($tenant->room) &middot; Kamar {{ $tenant->room->room_number }}@endif
                                        </p>
                                    </div>
                                    <span class="shrink-0 text-sm font-bold text-rose-600">{{ rupiah($tenant->monthly_price) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- Recent payments --}}
                <div class="card p-6 sm:p-8 lg:col-span-5">
                    <div class="flex items-center gap-3">
                        <span class="grid size-10 place-items-center rounded-2xl bg-brand-50 text-brand-600">
                            <x-ui.icon name="credit-card" class="size-5" />
                        </span>
                        <h2 class="text-lg font-extrabold">Aktivitas pembayaran</h2>
                    </div>

                    @if ($rental['recent_payments']->isEmpty())
                        <p class="mt-5 text-[13px] text-ink-500">Belum ada pembayaran tercatat.</p>
                    @else
                        <ul class="mt-5 space-y-3">
                            @foreach ($rental['recent_payments'] as $payment)
                                <li class="flex items-center gap-3">
                                    <span class="grid size-9 shrink-0 place-items-center rounded-xl {{ $payment->status === 'paid' ? 'bg-accent-50 text-accent-600' : 'bg-sun-50 text-sun-600' }}">
                                        <x-ui.icon :name="$payment->status === 'paid' ? 'check-circle' : 'bell-alert'" class="size-4" />
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-[13px] font-bold text-ink-900">{{ $payment->tenant?->name ?? '—' }}</p>
                                        <p class="truncate text-xs text-ink-400">{{ $payment->period }} &middot; {{ $payment->kosListing?->title ?? '—' }}</p>
                                    </div>
                                    <span class="shrink-0 text-[13px] font-bold {{ $payment->status === 'paid' ? 'text-accent-700' : 'text-sun-600' }}">
                                        {{ rupiah($payment->amount) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- ========================= LISTINGS ========================= --}}
            <div class="mt-12 grid gap-8 lg:grid-cols-12 lg:gap-10">

                {{-- Kos --}}
                <div class="lg:col-span-12">
                    <div class="flex items-end justify-between gap-4">
                        <h2 class="heading-md">Listing kos saya</h2>
                        <span class="badge-ink">{{ $kosListings->count() }} unit</span>
                    </div>

                    @if ($kosListings->isEmpty())
                        @include('partials.empty-state', [
                            'icon' => 'building-office-2',
                            'title' => 'Belum ada kos dipasang',
                            'description' => 'Pasang listing kosmu lewat panel pengelola, lalu listingnya langsung tampil di sini.',
                            'actionLabel' => 'Buka Panel Pengelola',
                            'actionHref' => url('/admin'),
                        ])
                    @else
                        <ul class="mt-6 space-y-3">
                            @foreach ($kosListings as $kos)
                                @php
                                    $image = listingImage($kos);
                                    $occupancy = $rental['room_summaries']->get($kos->id);
                                @endphp
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
                                            @if ($occupancy)
                                                {{ $occupancy['occupied'] }}/{{ $occupancy['total'] }} kamar terisi
                                            @else
                                                {{ $kos->facilities->count() }} fasilitas
                                            @endif
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
                        Kelola kamar, penghuni, dan pembayaran lewat
                        <a href="{{ url('/admin') }}" class="font-semibold text-brand-700 hover:underline">panel pengelola</a>,
                        atau ubah data akunmu di
                        <a href="{{ route('settings.edit') }}" class="font-semibold text-brand-700 hover:underline">pengaturan</a>.
                    </span>
                </p>
            </div>
        </div>
    </section>

@endsection
