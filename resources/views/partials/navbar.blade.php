@php
    $navLinks = [
        ['label' => 'Beranda', 'route' => 'home', 'href' => route('home'), 'icon' => 'home'],
        ['label' => 'Cari Kos', 'route' => 'kos.index', 'href' => route('kos.index'), 'icon' => 'magnifying-glass'],
        ['label' => 'Thrift', 'route' => 'thrift.index', 'href' => route('thrift.index'), 'icon' => 'tag'],
    ];

    $authUser = auth()->user();
    $roleLabels = ['admin' => 'Admin', 'owner' => 'Pemilik', 'student' => 'Mahasiswa'];
    $roleLabel = $authUser ? ($roleLabels[$authUser->role] ?? 'Pengguna') : 'Pengguna';
    $dashboardHref = ($authUser?->role === 'admin') ? url('/admin') : route('dashboard');
    $dashboardLabel = ($authUser?->role === 'admin') ? 'Panel Admin' : 'Dashboard';

    // Signed-in users land straight in their own area, so the public browse
    // links (Beranda / Cari Kos / Thrift) are only shown to guests. The brand
    // logo and the account chip both lead to their dashboard instead of home.
    $brandHref = $authUser ? $dashboardHref : route('home');
@endphp

<header
    x-data="{ open: false, scrolled: false }"
    x-init="scrolled = window.scrollY > 12; window.addEventListener('scroll', () => scrolled = window.scrollY > 12, { passive: true })"
    @keydown.escape.window="open = false"
    class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
    :class="scrolled || open ? 'bg-white/85 shadow-[0_1px_0_rgb(16_24_40/0.06),0_10px_30px_-18px_rgb(16_24_40/0.35)] backdrop-blur-xl backdrop-saturate-150' : 'bg-transparent'"
>
    <div class="container-page">
        <div class="flex h-18 items-center justify-between gap-4 py-3.5">
            {{-- Brand --}}
            <a href="{{ $brandHref }}" class="group flex shrink-0 items-center gap-2.5">
                <x-ui.logo class="size-10 transition-transform duration-300 group-hover:scale-105" />
                <span class="flex flex-col leading-none">
                    <span class="font-display text-xl font-extrabold tracking-tight text-ink-900">KosKuy</span>
                    <span class="hidden text-[10px] font-semibold tracking-[0.16em] text-ink-400 uppercase sm:block">Kos &amp; Thrift</span>
                </span>
            </a>

            {{-- Desktop navigation (guests only) --}}
            @guest
                <nav data-public-nav class="hidden items-center gap-1 lg:flex" aria-label="Navigasi utama">
                    @foreach ($navLinks as $link)
                        @php $isActive = request()->routeIs($link['route']); @endphp
                        <a
                            href="{{ $link['href'] }}"
                            @if ($isActive) aria-current="page" @endif
                            class="relative rounded-full px-4 py-2 text-sm font-semibold transition-colors {{ $isActive ? 'bg-brand-50 text-brand-700' : 'text-ink-600 hover:bg-ink-100 hover:text-ink-900' }}"
                        >
                            {{ $link['label'] }}
                            @if ($isActive)
                                <span class="absolute inset-x-4 -bottom-0.5 h-0.5 rounded-full bg-brand-500"></span>
                            @endif
                        </a>
                    @endforeach
                </nav>
            @endguest

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                @auth
                    {{-- Account chip → dashboard. Avatar only on phones, name + role from sm. --}}
                    <a
                        href="{{ $dashboardHref }}"
                        class="inline-flex items-center gap-2 rounded-full border border-ink-200 bg-white p-1 text-sm font-semibold text-ink-700 transition hover:border-brand-300 hover:text-brand-700 sm:py-1.5 sm:pr-4 sm:pl-1.5"
                    >
                        <x-ui.avatar :user="$authUser" size="size-8" text="text-xs" />
                        <span class="hidden max-w-24 truncate sm:inline">{{ $authUser->name }}</span>
                        <span class="hidden rounded-full bg-ink-100 px-2 py-0.5 text-[10px] font-bold tracking-wide text-ink-500 uppercase sm:inline">{{ $roleLabel }}</span>
                    </a>

                    <a
                        href="{{ route('settings.edit') }}"
                        class="hidden items-center gap-2 rounded-full border border-ink-200 bg-white px-3.5 py-2 text-sm font-semibold text-ink-600 transition hover:border-brand-300 hover:text-brand-700 sm:inline-flex"
                        aria-label="Pengaturan akun"
                        title="Pengaturan akun"
                    >
                        <x-ui.icon name="cog-6-tooth" class="size-5" />
                        <span>Pengaturan</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden btn btn-md btn-outline sm:inline-flex">
                        <x-ui.icon name="lock-closed" class="size-4" />
                        Masuk
                    </a>

                    <a href="{{ route('kos.index') }}" class="btn btn-md btn-primary hidden sm:inline-flex">
                        <x-ui.icon name="magnifying-glass" class="size-4" />
                        Cari Kos
                    </a>

                    {{-- Mobile toggle (guests only) --}}
                    <button
                        type="button"
                        @click="open = !open"
                        class="grid size-10 place-items-center rounded-full bg-ink-100/80 text-ink-700 ring-1 ring-ink-950/5 transition hover:bg-ink-100 active:scale-95 lg:hidden"
                        :aria-expanded="open ? 'true' : 'false'"
                        aria-controls="mobile-nav"
                        :aria-label="open ? 'Tutup menu navigasi' : 'Buka menu navigasi'"
                    >
                        <x-ui.icon name="bars-3" class="size-6" x-show="!open" />
                        <x-ui.icon name="x-mark" class="size-6" x-cloak x-show="open" />
                    </button>
                @endauth
            </div>
        </div>
    </div>

    {{-- Mobile panel (guests only) --}}
    @guest
        <div
            id="mobile-nav"
            data-public-nav
            x-show="open"
            x-cloak
            x-collapse.duration.250ms
            class="border-t border-ink-100 bg-white/95 backdrop-blur-xl lg:hidden"
        >
            <nav class="container-page space-y-1 py-4" aria-label="Navigasi seluler">
                @foreach ($navLinks as $link)
                    @php $isActive = request()->routeIs($link['route']); @endphp
                    <a
                        href="{{ $link['href'] }}"
                        class="flex items-center gap-3 rounded-2xl px-4 py-3 text-base font-semibold transition {{ $isActive ? 'bg-brand-50 text-brand-700' : 'text-ink-700 hover:bg-ink-100' }}"
                    >
                        <span class="grid size-9 place-items-center rounded-xl {{ $isActive ? 'bg-brand-100 text-brand-700' : 'bg-ink-100 text-ink-500' }}">
                            <x-ui.icon :name="$link['icon']" class="size-5" />
                        </span>
                        {{ $link['label'] }}
                    </a>
                @endforeach

                <div class="!mt-4 grid grid-cols-2 gap-3 border-t border-ink-100 pt-4">
                    <a href="{{ route('login') }}" class="btn btn-md btn-outline w-full">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-md btn-primary w-full">Daftar Gratis</a>
                </div>
            </nav>
        </div>
    @endguest
</header>

{{-- Spacer so the fixed header never overlaps page content --}}
<div class="h-18" aria-hidden="true"></div>
