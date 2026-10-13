@extends('layouts.app')

@section('title', 'Jual & Sewa di KosKuy — Untuk Pemilik Kos dan Penjual')
@section('meta_description', 'Punya kos untuk disewakan atau barang bekas untuk dijual? Pelajari cara menjadi mitra KosKuy — gratis, tanpa komisi, dan langsung dijangkau mahasiswa.')

@section('content')

    @php
        $authUser = auth()->user();
        $isManager = in_array($authUser?->role, ['admin', 'owner'], true);
        $registerOwner = route('register', ['role' => 'owner']);
        $panelHref = url('/admin');
    @endphp

    {{-- ========================= HERO ========================= --}}
    <section class="relative overflow-hidden bg-ink-950">
        <div class="grid-lines pointer-events-none absolute inset-0" aria-hidden="true"></div>
        <div class="blob -top-24 -right-16 size-96 bg-brand-500/35 blur-3xl" aria-hidden="true"></div>
        <div class="blob -bottom-32 left-10 size-96 bg-accent-400/25 blur-3xl" aria-hidden="true"></div>

        <div class="container-page relative py-16 sm:py-20 lg:py-24">
            <div class="max-w-3xl">
                <span class="badge-glass inline-flex">
                    <x-ui.icon name="sparkles" class="size-3.5 text-accent-300" />
                    Program Mitra
                </span>

                <h1 class="mt-6 text-4xl leading-[1.08] font-extrabold tracking-tight text-white sm:text-5xl">
                    Punya kos atau barang bekas?<br class="hidden sm:block">
                    Jadikan <span class="gradient-text">penghasilan</span>.
                </h1>

                <p class="mt-6 max-w-2xl text-base leading-relaxed text-ink-300 sm:text-lg">
                    KosKuy menghubungkan pemilik kos dan penjual barang bekas langsung ke
                    mahasiswa di sekitarmu. Gratis, tanpa komisi, tanpa perantara — kamu
                    yang pegang kendali atas listingmu.
                </p>

                <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <a href="#sewa" class="btn btn-lg btn-gradient w-full sm:w-auto">
                        <x-ui.icon name="building-office-2" class="size-5" />
                        Sewakan kos
                    </a>
                    <a href="#jual" class="btn btn-lg w-full border border-white/15 bg-white/10 text-white backdrop-blur transition hover:bg-white/20 sm:w-auto">
                        <x-ui.icon name="tag" class="size-5" />
                        Jual barang
                    </a>
                </div>

                <dl class="mt-12 grid max-w-2xl grid-cols-2 gap-x-8 gap-y-6 sm:grid-cols-4">
                    @foreach ([
                        ['value' => 'Rp 0', 'label' => 'Biaya pendaftaran'],
                        ['value' => '0%', 'label' => 'Komisi transaksi'],
                        ['value' => 'WA', 'label' => 'Chat langsung'],
                        ['value' => '24/7', 'label' => 'Listing tayang'],
                    ] as $stat)
                        <div>
                            <dt class="font-display text-2xl font-extrabold text-white sm:text-3xl">{{ $stat['value'] }}</dt>
                            <dd class="mt-1 text-[13px] font-semibold text-ink-400">{{ $stat['label'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </section>

    {{-- ========================= DUA JALUR ========================= --}}
    <section class="section bg-white">
        <div class="container-page">
            <x-ui.section-heading
                eyebrow="Dua cara bergabung"
                title="Pilih jalur yang cocok buatmu"
                description="Alurnya simpel: buat akun, pasang listing, lalu terima chat langsung dari mahasiswa."
            />

            <div class="mt-14 grid gap-6 lg:grid-cols-2">
                {{-- Sewakan kos --}}
                <div class="card flex flex-col p-7 sm:p-9" id="sewa">
                    <div class="flex items-center justify-between gap-3">
                        <span class="grid size-12 place-items-center rounded-2xl bg-brand-50 text-brand-600">
                            <x-ui.icon name="building-office-2" class="size-6" />
                        </span>
                        <span class="badge badge-brand">Tersedia sekarang</span>
                    </div>

                    <h3 class="mt-5 text-xl font-extrabold text-ink-900">Sewakan kosmu</h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink-500">
                        Pasang kamar kos dengan foto, harga, jenis, dan fasilitasnya.
                        Listingmu langsung tampil di katalog dan bisa ditemukan mahasiswa
                        lewat WhatsApp.
                    </p>

                    <ul class="mt-6 space-y-3">
                        @foreach ([
                            'Buat akun Pemilik — gratis, tanpa biaya bulanan.',
                            'Tambahkan unit kos beserta foto dan fasilitasnya lewat panel pengelola.',
                            'Terima chat minat dari mahasiswa langsung ke WhatsApp-mu.',
                        ] as $point)
                            <li class="flex gap-2.5 text-sm leading-relaxed text-ink-600">
                                <x-ui.icon name="check-badge" class="mt-0.5 size-4 shrink-0 text-brand-500" />
                                {{ $point }}
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-8 pt-6 border-t border-ink-100">
                        @guest
                            <a href="{{ $registerOwner }}" class="btn btn-md btn-primary w-full">
                                <x-ui.icon name="user-plus" class="size-4" />
                                Daftar sebagai Pemilik
                            </a>
                        @elseif ($isManager)
                            <a href="{{ $panelHref }}" class="btn btn-md btn-primary w-full">
                                <x-ui.icon name="building-office-2" class="size-4" />
                                Buka Panel Pengelola
                            </a>
                        @else
                            <p class="rounded-2xl bg-brand-50/70 p-4 text-[13px] leading-relaxed text-ink-600">
                                Kamu masuk sebagai <strong>Mahasiswa</strong>. Untuk memasang kos,
                                siapkan akun <strong>Pemilik / Penjual</strong> — keluar dulu, lalu
                                daftar dengan peran itu.
                            </p>
                        @endguest
                    </div>
                </div>

                {{-- Jual barang --}}
                <div class="card flex flex-col p-7 sm:p-9" id="jual">
                    <div class="flex items-center justify-between gap-3">
                        <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-violet-50 text-violet-600">
                            <x-ui.icon name="tag" class="size-6" />
                        </span>
                        <span class="badge badge-ink">Segera hadir</span>
                    </div>

                    <h3 class="mt-5 text-xl font-extrabold text-ink-900">Jual barang bekas</h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink-500">
                        Buku, elektronik, peralatan kos, sampai pakaian — beri barang yang
                        masih layak pakai kesempatan kedua. Alur jual dari akunmu sedang
                        kami siapkan.
                    </p>

                    <ul class="mt-6 space-y-3">
                        @foreach ([
                            'Unggah barang, tulis kondisi apa adanya, pasang harga.',
                            'Pembeli menghubungimu langsung — transaksi antar pengguna.',
                            'Belum aktif: fitur pasang barang sedang dalam pengembangan.',
                        ] as $point)
                            <li class="flex gap-2.5 text-sm leading-relaxed text-ink-600">
                                <x-ui.icon name="check-badge" class="mt-0.5 size-4 shrink-0 text-violet-500" />
                                {{ $point }}
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-8 pt-6 border-t border-ink-100">
                        <a href="{{ route('thrift.index') }}" class="btn btn-md btn-outline w-full">
                            <x-ui.icon name="magnifying-glass" class="size-4" />
                            Lihat katalog thrift
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================= CARA KERJA ========================= --}}
    <section class="section bg-ink-50/60">
        <div class="container-page">
            <x-ui.section-heading
                eyebrow="Cara jadi mitra"
                title="Tiga langkah, langsung jalan"
                description="Tidak perlu keahlian khusus — cukup waktu beberapa menit."
            />

            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['n' => '01', 'icon' => 'user-plus', 'title' => 'Buat akun', 'desc' => 'Daftar gratis sebagai Pemilik / Penjual. Cukup nama, email, dan nomor WhatsApp.'],
                    ['n' => '02', 'icon' => 'document-text', 'title' => 'Pasang listing', 'desc' => 'Isi detail kos atau barang, tambahkan foto, lalu atur harga dan ketersediaannya.'],
                    ['n' => '03', 'icon' => 'chat-bubble-oval-left', 'title' => 'Terima chat', 'desc' => 'Mahasiswa menghubungimu langsung lewat WhatsApp. Sepakati, dan transaksi beres.'],
                ] as $step)
                    <div class="card relative p-7">
                        <span class="font-display text-4xl font-extrabold text-ink-200">{{ $step['n'] }}</span>
                        <span class="mt-4 grid size-11 place-items-center rounded-2xl bg-brand-50 text-brand-600">
                            <x-ui.icon :name="$step['icon']" class="size-5" />
                        </span>
                        <h3 class="mt-4 text-base font-extrabold text-ink-900">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-500">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================= JAMINAN ========================= --}}
    <section class="section bg-white">
        <div class="container-page">
            <div class="grid gap-6 sm:grid-cols-3">
                @foreach ([
                    ['icon' => 'banknotes', 'title' => 'Tanpa komisi', 'desc' => 'Kami tidak memotong sepeser pun dari transaksimu. Kamu terima 100%.'],
                    ['icon' => 'chat-bubble-oval-left', 'title' => 'Kontak langsung', 'desc' => 'Nomor WhatsApp hanya tampil sebagai tautan wa.me — datamu tetap terjaga.'],
                    ['icon' => 'shield-check', 'title' => 'Listing milikmu', 'desc' => 'Kamu yang mengatur harga dan ketersediaan. Perbarui kapan saja dari dashboard.'],
                ] as $item)
                    <div class="card p-6">
                        <span class="grid size-11 place-items-center rounded-2xl bg-accent-50 text-accent-600">
                            <x-ui.icon :name="$item['icon']" class="size-5" />
                        </span>
                        <h3 class="mt-4 text-base font-extrabold text-ink-900">{{ $item['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-500">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================= FAQ ========================= --}}
    <section class="section bg-ink-50/60">
        <div class="container-narrow">
            <x-ui.section-heading
                eyebrow="Pertanyaan umum"
                title="Pertanyaan seputar jadi mitra"
                description="Kalau masih ada yang mengganjal, chat saja langsung ke tim kami."
            />

            <div class="mt-12 divide-y divide-ink-100 overflow-hidden rounded-4xl border border-ink-100 bg-white shadow-soft">
                @foreach ([
                    ['q' => 'Apakah benar-benar gratis?', 'a' => 'Ya. Pendaftaran, pemasangan listing, dan chat tidak dipungut biaya. KosKuy tidak mengambil komisi dari transaksi.'],
                    ['q' => 'Bagaimana menyewakan kos saya?', 'a' => 'Buat akun dengan peran Pemilik, lalu tambahkan unit kos beserta foto, harga, jenis kamar, dan fasilitasnya melalui panel pengelola.'],
                    ['q' => 'Bagaimana cara menghubungi calon penyewa?', 'a' => 'Setiap listing punya tombol Chat yang membuka WhatsApp dengan pesan otomatis terisi. Penyewa menghubungimu langsung.'],
                    ['q' => 'Kapan fitur jual barang aktif?', 'a' => 'Alur jual dari akun sedang dikembangkan. Untuk sekarang katalog thrift sudah bisa dijelajahi, dan fitur pasang barang akan menyusul.'],
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

    {{-- ========================= CTA PENUTUP ========================= --}}
    <section class="section bg-white">
        <div class="container-page">
            <div class="relative overflow-hidden rounded-4xl bg-ink-950 px-7 py-14 text-center sm:px-12 sm:py-16">
                <div class="grid-lines pointer-events-none absolute inset-0" aria-hidden="true"></div>
                <div class="blob -top-24 right-0 size-96 bg-brand-500/30 blur-3xl" aria-hidden="true"></div>

                <div class="relative mx-auto max-w-2xl">
                    <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                        Siap mulai dari <span class="gradient-text">kos pertamamu?</span>
                    </h2>
                    <p class="mt-4 text-[15px] leading-relaxed text-ink-300">
                        Daftar sekarang dan biarkan mahasiswa menemukan tempat tinggal atau
                        barang yang mereka butuhkan — langsung dari kamu.
                    </p>

                    <div class="mt-9 flex flex-col justify-center gap-3 sm:flex-row">
                        @guest
                            <a href="{{ $registerOwner }}" class="btn btn-lg btn-gradient w-full sm:w-auto">
                                <x-ui.icon name="user-plus" class="size-5" />
                                Daftar sebagai Pemilik
                            </a>
                            <a href="{{ route('kos.index') }}" class="btn btn-lg w-full border border-white/15 bg-white/10 text-white backdrop-blur transition hover:bg-white/20 sm:w-auto">
                                Jelajahi katalog
                            </a>
                        @elseif ($isManager)
                            <a href="{{ $panelHref }}" class="btn btn-lg btn-gradient w-full sm:w-auto">
                                <x-ui.icon name="building-office-2" class="size-5" />
                                Buka Panel Pengelola
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-lg btn-gradient w-full sm:w-auto">
                                <x-ui.icon name="arrow-right" class="size-5" />
                                Kembali ke dashboard
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
