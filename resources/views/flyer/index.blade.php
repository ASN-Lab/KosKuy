@extends('layouts.app')

@section('title', 'Flyer Promosi KosKuy — Siap Dibagikan')
@section('meta_description', 'Flyer promosi KosKuy dalam format A5: satu kos, satu barang thrift, dan ajakan mendaftar. Cetak atau simpan sebagai gambar.')

@section('content')

    {{-- ========================= TOOLBAR ========================= --}}
    <section class="no-print border-b border-ink-100 bg-ink-50/70">
        <div class="container-page py-10">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-sm text-ink-500">
                <a href="{{ route('home') }}" class="font-semibold transition hover:text-brand-700">Beranda</a>
                <x-ui.icon name="chevron-right" class="size-3.5 text-ink-300" />
                <span class="font-semibold text-ink-900">Flyer</span>
            </nav>

            <div class="mt-5 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="badge-brand">
                        <x-ui.icon name="gift" class="size-3.5" />
                        Materi promosi
                    </span>
                    <h1 class="heading-lg mt-4">Flyer Promosi KosKuy</h1>
                    <p class="lede mt-3 max-w-xl">
                        Tiga halaman siap cetak dalam ukuran A5. Dirancang untuk dibagikan di grup kelas,
                        kampus, atau melalui WhatsApp.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="window.print()" class="btn btn-md btn-primary">
                        <x-ui.icon name="printer" class="size-4" />
                        Cetak / Simpan PDF
                    </button>
                    <a href="{{ route('kos.index') }}" class="btn btn-md btn-outline">
                        <x-ui.icon name="magnifying-glass" class="size-4" />
                        Lihat Katalog
                    </a>
                </div>
            </div>

            <p class="mt-6 flex items-start gap-2.5 rounded-2xl bg-white p-4 text-[13px] leading-relaxed text-ink-600 ring-1 ring-ink-100">
                <x-ui.icon name="information-circle" class="mt-0.5 size-4 shrink-0 text-brand-600" />
                <span>
                    Tips cetak: pilih <strong>Save as PDF</strong> dan ukuran kertas <strong>A5</strong> dengan margin
                    <strong>None</strong>. Aktifkan <strong>Background graphics</strong> agar warna brand ikut tercetak.
                </span>
            </p>
        </div>
    </section>

    {{-- ========================= FLYER SHEETS ========================= --}}
    <section class="section-tight bg-ink-100/60">
        <div class="container-page">
            {{--
                Each sheet is locked to the A5 ratio (148:210). Content areas use
                min-h-0 so flex children shrink instead of pushing past the sheet
                edge, which is what previously clipped the last card on page 2.
            --}}
            <div class="grid gap-10 xl:grid-cols-3 xl:gap-8">

                {{-- ---------- Sheet 1: Hero ---------- --}}
                <article class="print-page">
                    <div class="flyer-sheet flex aspect-148/210 flex-col overflow-hidden bg-white shadow-lift">
                        <div class="relative min-h-0 flex-1 overflow-hidden bg-ink-gradient px-7 py-7">
                            <div class="grid-lines pointer-events-none absolute inset-0 opacity-70" aria-hidden="true"></div>
                            <div class="blob -top-20 -right-14 size-64 bg-brand-500/45 blur-3xl" aria-hidden="true"></div>
                            <div class="blob -bottom-24 -left-16 size-64 bg-accent-400/35 blur-3xl" aria-hidden="true"></div>

                            <div class="relative flex h-full flex-col">
                                <div class="flex items-center gap-2.5">
                                    <x-ui.logo variant="light" class="size-11 shrink-0" />
                                    <div class="leading-none">
                                        <p class="font-display text-2xl font-extrabold text-white">KosKuy</p>
                                        <p class="mt-1 text-[9px] font-semibold tracking-[0.2em] text-ink-400 uppercase">Kos &amp; Thrift</p>
                                    </div>
                                </div>

                                <h2 class="mt-7 text-[1.9rem] leading-[1.08] font-extrabold text-white">
                                    Kos<br>
                                    <span class="text-brand-300">impianmu</span><br>
                                    tanpa drama.
                                </h2>

                                <p class="mt-3.5 text-[12.5px] leading-relaxed text-ink-300">
                                    Cari kos sesuai budget, bandingkan fasilitas, lalu chat pemilik
                                    langsung lewat WhatsApp. Gratis, tanpa perantara.
                                </p>

                                <ul class="mt-5 space-y-2">
                                    @foreach ([
                                        'Kos putra, putri, dan campur',
                                        'Barang thrift kondisi jelas',
                                        'Tanpa biaya agen',
                                    ] as $point)
                                        <li class="flex items-center gap-2 text-[12px] font-semibold text-white">
                                            <span class="grid size-4 shrink-0 place-items-center rounded-full bg-accent-500 text-white">
                                                <x-ui.icon name="check" class="size-2.5" />
                                            </span>
                                            {{ $point }}
                                        </li>
                                    @endforeach
                                </ul>

                                {{-- Flexible spacer keeps the QR pinned to the sheet
                                     bottom without ever pushing content off-page. --}}
                                <div class="min-h-2 flex-1"></div>

                                <div class="mt-5 flex items-center gap-3 rounded-2xl border border-white/15 bg-white/10 p-3 backdrop-blur">
                                    @php $qr = qrImageUrl(); @endphp
                                    <span class="grid size-[4.5rem] shrink-0 place-items-center rounded-xl bg-white p-1.5">
                                        @if ($qr)
                                            <img src="{{ $qr }}" alt="Kode QR pendaftaran {{ siteHost() }}" class="size-full rounded-lg object-contain" />
                                        @else
                                            <span class="grid size-full place-items-center rounded-lg bg-ink-950 text-center text-white">
                                                <span class="px-0.5 text-[7px] leading-tight font-bold tracking-tight">
                                                    QR<br>belum<br>diisi
                                                </span>
                                            </span>
                                        @endif
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-[9px] font-bold tracking-[0.14em] text-ink-400 uppercase">Pindai untuk daftar</p>
                                        <p class="mt-1 font-display text-[1.05rem] leading-tight font-extrabold break-words text-white">
                                            {{ siteHost() }}/register
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 bg-white px-7 py-5">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold tracking-wide text-ink-400 uppercase">Gratis daftar</p>
                                    <p class="mt-0.5 truncate font-display text-xl font-extrabold text-brand-700">{{ siteHost() }}</p>
                                </div>
                                <span class="badge-glass shrink-0">
                                    <x-ui.icon name="sparkles" class="size-3.5 text-brand-300" />
                                    Tanpa biaya
                                </span>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 text-center text-xs font-semibold text-ink-400">Halaman 1 — Hero</p>
                </article>

                {{-- ---------- Sheet 2: Feature grid ---------- --}}
                <article class="print-page">
                    <div class="flyer-sheet flex aspect-148/210 flex-col overflow-hidden bg-white shadow-lift">
                        <div class="shrink-0 border-b border-ink-100 bg-ink-50/60 px-7 py-5">
                            <p class="text-[10px] font-bold tracking-[0.18em] text-brand-700 uppercase">Kenapa KosKuy</p>
                            <h2 class="mt-1.5 text-[1.45rem] leading-tight font-extrabold text-ink-900">
                                Semua yang kamu butuh, dalam satu tempat.
                            </h2>
                        </div>

                        {{-- min-h-0 + shrink lets the four cards compress to fit A5
                             rather than overflowing the sheet. --}}
                        <div class="flex min-h-0 flex-1 flex-col gap-2.5 px-7 py-5">
                            @foreach ([
                                ['icon' => 'funnel', 'title' => 'Filter yang benar-benar kepakai', 'body' => 'Jenis kos, rentang harga, dan fasilitas. Tidak ada menu yang tak berguna.'],
                                ['icon' => 'chat-bubble-oval-left', 'title' => 'Chat langsung ke pemilik', 'body' => 'Lewat WhatsApp. Tanpa perantara, tanpa balas-chat yang hilang.'],
                                ['icon' => 'shield-check', 'title' => 'Harga transparan', 'body' => 'Harga per bulan ditulis terang, tanpa biaya tersembunyi.'],
                                ['icon' => 'book-open', 'title' => 'Thrift untuk kampus', 'body' => 'Buku, elektronik, dan furnitur dari mahasiswa sekitar.'],
                            ] as $feature)
                                <div class="flex min-h-0 flex-1 items-center gap-3.5 rounded-2xl border border-ink-100 p-3.5">
                                    <span class="grid size-9 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-600">
                                        <x-ui.icon :name="$feature['icon']" class="size-4.5" />
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-[12.5px] font-bold text-ink-900">{{ $feature['title'] }}</p>
                                        <p class="mt-0.5 text-[11px] leading-snug text-ink-500">{{ $feature['body'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mx-7 mb-6 shrink-0 rounded-2xl bg-ink-gradient px-5 py-3.5 text-center">
                            <p class="text-[10px] font-bold tracking-wide text-white uppercase">Mulai gratis hari ini</p>
                            <p class="mt-1 font-display text-base font-extrabold break-all text-white">{{ siteHost() }}/register</p>
                        </div>
                    </div>
                    <p class="mt-3 text-center text-xs font-semibold text-ink-400">Halaman 2 — Fitur</p>
                </article>

                {{-- ---------- Sheet 3: CTA ---------- --}}
                <article class="print-page">
                    <div class="flyer-sheet relative flex aspect-148/210 flex-col overflow-hidden bg-ink-gradient shadow-lift">
                        <div class="grid-lines pointer-events-none absolute inset-0 opacity-70" aria-hidden="true"></div>
                        <div class="blob -top-24 -left-16 size-72 bg-brand-500/45 blur-3xl" aria-hidden="true"></div>
                        <div class="blob -right-20 -bottom-28 size-72 bg-accent-400/35 blur-3xl" aria-hidden="true"></div>

                        <div class="relative flex min-h-0 flex-1 flex-col justify-between gap-4 px-7 py-7">
                            <div class="shrink-0">
                                <x-ui.logo variant="light" class="size-12" />
                                <h2 class="mt-6 text-[1.75rem] leading-[1.1] font-extrabold text-white">
                                    Kos bulan depan<br>
                                    jangan sampai<br>
                                    <span class="text-brand-300">terlewat.</span>
                                </h2>
                                <p class="mt-3.5 text-[12.5px] leading-relaxed text-ink-300">
                                    Buka browser, ketik <strong class="text-white">{{ siteHost() }}</strong>,
                                    dan lihat listing yang sudah ditata rapi hari ini juga.
                                </p>
                            </div>

                            <div class="min-h-0 shrink-0 space-y-2.5">
                                <div class="rounded-2xl border border-white/15 bg-white/10 p-3.5 backdrop-blur">
                                    <p class="text-[9px] font-bold tracking-[0.14em] text-ink-400 uppercase">Scan untuk daftar</p>
                                    <div class="mt-2.5 grid grid-cols-[auto_1fr] items-center gap-3.5">
                                        <span class="grid size-[4.75rem] shrink-0 place-items-center rounded-xl bg-white p-1.5">
                                            @if ($qr ?? null)
                                                <img src="{{ $qr }}" alt="Kode QR pendaftaran {{ siteHost() }}" class="size-full rounded-lg object-contain" />
                                            @else
                                                <span class="grid size-full place-items-center rounded-lg bg-ink-950 text-center text-white">
                                                    <span class="px-0.5 text-[7px] leading-tight font-bold tracking-tight">
                                                        QR<br>belum<br>diisi
                                                    </span>
                                                </span>
                                            @endif
                                        </span>
                                        <p class="text-[11px] leading-relaxed text-ink-300">
                                            Pindai untuk langsung membuka halaman
                                            pendaftaran {{ siteHost() }}.
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2.5">
                                    @php $wa = localPhone(config('koskuy.contact.whatsapp')); @endphp
                                    <div class="rounded-2xl border border-white/15 bg-white/10 px-3.5 py-2.5 backdrop-blur">
                                        <p class="text-[9px] font-bold tracking-wide text-ink-400 uppercase">WhatsApp</p>
                                        <p class="mt-0.5 text-[12.5px] font-bold break-all text-white">
                                            {{ $wa ?: (config('koskuy.contact.email') ?: '-') }}
                                        </p>
                                    </div>
                                    <div class="rounded-2xl border border-white/15 bg-white/10 px-3.5 py-2.5 backdrop-blur">
                                        <p class="text-[9px] font-bold tracking-wide text-ink-400 uppercase">Biaya</p>
                                        <p class="mt-0.5 text-[12.5px] font-bold text-white">Gratis daftar</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-center gap-2 pt-0.5">
                                    <x-ui.icon name="check-badge" class="size-3.5 text-accent-300" />
                                    <p class="text-[10.5px] font-semibold text-ink-300">
                                        Tanpa biaya agen &amp; tanpa perantara
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 text-center text-xs font-semibold text-ink-400">Halaman 3 — Ajakan Daftar</p>
                </article>
            </div>

            {{-- Live stats strip --}}
            <div class="no-print mt-12 grid gap-4 sm:grid-cols-3">
                @foreach ([
                    ['value' => $stats['kos'], 'label' => 'unit kos siap huni', 'icon' => 'building-office-2'],
                    ['value' => $stats['thrift'], 'label' => 'barang thrift aktif', 'icon' => 'tag'],
                    ['value' => $stats['categories'], 'label' => 'kategori barang', 'icon' => 'view-columns'],
                ] as $item)
                    <div class="card flex items-center gap-4 p-5">
                        <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-accent-50 text-accent-600">
                            <x-ui.icon :name="$item['icon']" class="size-5" />
                        </span>
                        <div>
                            <p class="font-display text-2xl font-extrabold text-ink-900">{{ $item['value'] }}</p>
                            <p class="text-[13px] font-semibold text-ink-500">{{ $item['label'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

@endsection
