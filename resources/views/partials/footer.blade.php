@php
    $year = now()->year;

    $explore = [
        ['label' => 'Semua Kos', 'href' => route('kos.index')],
        ['label' => 'Kos Putra', 'href' => route('kos.index', ['gender_type' => 'putra'])],
        ['label' => 'Kos Putri', 'href' => route('kos.index', ['gender_type' => 'putri'])],
        ['label' => 'Kos Campur', 'href' => route('kos.index', ['gender_type' => 'campur'])],
        ['label' => 'Katalog Thrift', 'href' => route('thrift.index')],
    ];

    $company = [
        ['label' => 'Tentang Kami', 'href' => route('home') . '#tentang'],
        ['label' => 'Cara Kerja', 'href' => route('home') . '#cara-kerja'],
        ['label' => 'FAQ', 'href' => route('home') . '#faq'],
    ];

    $legal = [
        ['label' => 'Kebijakan Privasi', 'href' => route('home') . '#legal'],
        ['label' => 'Syarat & Ketentuan', 'href' => route('home') . '#legal'],
        ['label' => 'Admin Panel', 'href' => url('/admin')],
        ['label' => 'API v1', 'href' => url('/api/v1/kos')],
    ];
@endphp

<footer class="relative mt-auto overflow-hidden bg-ink-950 text-ink-300">
    {{-- Ambient glow --}}
    <div class="pointer-events-none absolute -top-24 -left-24 size-96 rounded-full bg-brand-600/25 blur-3xl" aria-hidden="true"></div>
    <div class="pointer-events-none absolute right-0 -bottom-32 size-96 rounded-full bg-accent-500/15 blur-3xl" aria-hidden="true"></div>
    <div class="grid-lines pointer-events-none absolute inset-0 opacity-60" aria-hidden="true"></div>

    <div class="container-page relative">
        {{-- Newsletter / CTA --}}
        <div class="grid gap-8 border-b border-white/10 py-14 lg:grid-cols-2 lg:items-center lg:gap-16">
            <div>
                <span class="badge-glass mb-5 inline-flex">
                    <x-ui.icon name="sparkles" class="size-3.5 text-brand-600" />
                    Gratis untuk mahasiswa
                </span>
                <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                    Kos bulan depan<br class="hidden sm:block"> sudah siap? <span class="text-brand-300">Jangan sampai telat.</span>
                </h2>
                <p class="mt-4 max-w-md text-[15px] leading-relaxed text-ink-400">
                    Ribuan mahasiswa sudah menemukan kos dan barang bekas berkualitas di KosKuy — tanpa biaya agen, tanpa perantara.
                </p>
            </div>

            <div class="rounded-4xl border border-white/10 bg-white/5 p-6 backdrop-blur-sm sm:p-8">
                <form method="POST" action="{{ route('subscribe') }}" class="space-y-4">
                    @csrf
                    <label for="footer-email" class="text-sm font-semibold text-white">Dapatkan info kos baru tiap minggu</label>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <div class="relative flex-1">
                            <x-ui.icon name="envelope" class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-ink-500" />
                            <input
                                id="footer-email"
                                type="email"
                                name="email"
                                required
                                placeholder="nama@kampus.ac.id"
                                class="h-12 w-full rounded-2xl border border-white/10 bg-white/10 pr-4 pl-12 text-sm text-white placeholder:text-ink-500 focus:border-brand-400 focus:ring-4 focus:ring-brand-500/20 focus:outline-none"
                            >
                        </div>
                        <button type="submit" class="btn btn-md btn-gradient shrink-0">
                            Daftar
                            <x-ui.icon name="arrow-right" class="size-4" />
                        </button>
                    </div>

                    @error('email')
                        <p class="flex items-center gap-1.5 text-xs font-semibold text-rose-300">
                            <x-ui.icon name="exclamation-triangle" class="size-3.5" />
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="text-xs text-ink-500">Tanpa spam. Berhenti kapan saja dengan satu klik.</p>
                </form>
            </div>
        </div>

        {{-- Link columns --}}
        <div class="grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-12 lg:gap-8">
            <div class="lg:col-span-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <x-ui.logo variant="light" class="size-11" />
                    <span class="flex flex-col leading-none">
                        <span class="font-display text-2xl font-extrabold text-white">KosKuy</span>
                        <span class="mt-1 text-[10px] font-semibold tracking-[0.18em] text-ink-500 uppercase">Kos &amp; Thrift</span>
                    </span>
                </a>

                <p class="mt-5 max-w-xs text-sm leading-relaxed text-ink-400">
                    Platform marketplace kos dan barang bekas yang lahir dari rah DBA kampus. Dibuat untuk mahasiswa, gratis untuk mahasiswa.
                </p>

                @php
                    // WhatsApp is configured as a bare phone number so it can also
                    // build wa.me deep links; every other network is a full URL.
                    $social = collect(config('koskuy.social'))
                        ->filter()
                        ->map(fn ($value, $network) => $network === 'whatsapp' ? waLink($value) : $value)
                        ->filter();
                @endphp

                <div class="mt-6 flex items-center gap-2">
                    @foreach ($social as $network => $url)
                        <a
                            href="{{ $url }}"
                            target="_blank"
                            rel="noopener"
                            class="grid size-10 place-items-center rounded-xl border border-white/10 bg-white/5 text-ink-400 transition hover:border-brand-400/50 hover:bg-brand-500/15 hover:text-white"
                            aria-label="KosKuy di {{ ucfirst($network) }}"
                        >
                            <span class="text-[11px] font-bold">{{ initials($network) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            @foreach (['Jelajahi' => $explore, 'Perusahaan' => $company, 'Lainnya' => $legal] as $heading => $items)
                <div class="lg:col-span-2">
                    <h3 class="text-sm font-bold tracking-wide text-white">{{ $heading }}</h3>
                    <ul class="mt-4 space-y-3">
                        @foreach ($items as $item)
                            <li>
                                <a href="{{ $item['href'] }}" class="text-sm text-ink-400 transition hover:text-brand-300">
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            <div class="lg:col-span-2">
                <h3 class="text-sm font-bold tracking-wide text-white">Unduh Aplikasi</h3>

                @php $stores = array_filter(config('koskuy.stores')); @endphp

                @if (count($stores))
                    <p class="mt-4 text-sm text-ink-400">Segera hadir di Play Store &amp; App Store.</p>
                    <div class="mt-4 space-y-2.5">
                        @foreach ($stores as $store => $url)
                            <a
                                href="{{ $url }}"
                                target="_blank"
                                rel="noopener"
                                class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 transition hover:border-brand-400/40 hover:bg-white/10"
                            >
                                <x-ui.icon name="device-phone-mobile" class="size-5 text-ink-300" />
                                <span class="flex flex-col leading-tight">
                                    <span class="text-[10px] tracking-wide text-ink-500 uppercase">
                                        {{ $store === 'play' ? 'Get it on' : 'Download on the' }}
                                    </span>
                                    <span class="text-sm font-semibold text-white">
                                        {{ $store === 'play' ? 'Google Play' : 'App Store' }}
                                    </span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="mt-4 text-sm leading-relaxed text-ink-400">
                        Aplikasi mobile KosKuy sedang kami bangun. Untuk sekarang, semua listing bisa diakses lewat browser.
                    </p>
                    <span class="mt-4 inline-flex items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-ink-400">
                        <x-ui.icon name="device-phone-mobile" class="size-5" />
                        Segera hadir
                    </span>
                @endif
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="flex flex-col gap-4 border-t border-white/10 py-7 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-ink-500">
                &copy; {{ $year }} <span class="font-semibold text-ink-400">KosKuy</span> · Dibuat oleh mahasiswa Magelang.
            </p>
            <p class="flex items-center gap-2 text-xs text-ink-500">
                <span class="relative flex size-2">
                    <span class="absolute inline-flex size-full animate-ping rounded-full bg-accent-400 opacity-75"></span>
                    <span class="relative inline-flex size-2 rounded-full bg-accent-500"></span>
                </span>
                Semua server aktif
            </p>
        </div>
    </div>
</footer>
