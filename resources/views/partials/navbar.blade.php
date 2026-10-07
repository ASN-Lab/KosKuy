@php
    $navLinks = [
        ['label' => 'Beranda', 'route' => 'home', 'href' => route('home'), 'icon' => 'home'],
        ['label' => 'Cari Kos', 'route' => 'kos.index', 'href' => route('kos.index'), 'icon' => 'magnifying-glass'],
        ['label' => 'Thrift', 'route' => 'thrift.index', 'href' => route('thrift.index'), 'icon' => 'tag'],
    ];
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
            <a href="{{ route('home') }}" class="group flex shrink-0 items-center gap-2.5">
                <x-ui.logo class="size-10 transition-transform duration-300 group-hover:scale-105" />
                <span class="flex flex-col leading-none">
                    <span class="font-display text-xl font-extrabold tracking-tight text-ink-900">KosKuy</span>
                    <span class="mt-0.5 text-[10px] font-semibold tracking-[0.16em] text-ink-400 uppercase">Kos &amp; Thrift</span>
                </span>
            </a>

            {{-- Desktop navigation --}}
            <nav class="hidden items-center gap-1 lg:flex" aria-label="Navigasi utama">
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

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                @auth
                    <div class="relative" x-data="{ userMenu: false }">
                        <button @click="userMenu = !userMenu" class="flex items-center gap-2 rounded-full border border-ink-200 bg-white py-1.5 pr-4 pl-1.5 text-sm font-semibold text-ink-700 transition hover:border-brand-300 hover:text-brand-700">
                            <span class="grid size-8 place-items-center rounded-full bg-brand-100 text-xs font-bold text-brand-700">
                                {{ initials(auth()->user()->name) }}
                            </span>
                            <span class="max-w-24 truncate">{{ auth()->user()->name }}</span>
                            <x-ui.icon name="chevron-down" class="size-4 text-ink-400" />
                        </button>
                        <div x-show="userMenu" @click.outside="userMenu = false" class="absolute right-0 mt-2 w-48 rounded-2xl border border-ink-100 bg-white p-2 shadow-xl">
                            <a href="{{ route('profile') }}" class="block rounded-xl px-4 py-2 text-sm font-medium hover:bg-ink-50">Informasi Akun</a>
                            <a href="{{ auth()->user()->role === 'owner' ? route('owner.dashboard') : (auth()->user()->role === 'seller' ? route('seller.dashboard') : route('dashboard')) }}" class="block rounded-xl px-4 py-2 text-sm font-medium hover:bg-ink-50">Dashboard</a>
                            <hr class="my-2 border-ink-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full rounded-xl px-4 py-2 text-left text-sm font-bold text-rose-600 hover:bg-rose-50">Keluar</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-md btn-outline inline-flex">
                        <x-ui.icon name="lock-closed" class="size-4" />
                        Masuk
                    </a>
                @endauth

                <a href="{{ route('kos.index') }}" class="btn btn-md btn-primary hidden sm:inline-flex">
                    <x-ui.icon name="magnifying-glass" class="size-4" />
                    Cari Kos
                </a>

                {{-- Mobile toggle --}}
                <button
                    type="button"
                    @click="open = !open"
                    class="grid size-10 place-items-center rounded-full text-ink-700 transition hover:bg-ink-100 lg:hidden"
                    :aria-expanded="open ? 'true' : 'false'"
                    aria-controls="mobile-nav"
                    aria-label="Buka menu navigasi"
                >
                    <x-ui.icon name="bars-3" class="size-6" x-show="!open" />
                    <x-ui.icon name="x-mark" class="size-6" x-cloak x-show="open" />
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile panel --}}
    <div
        id="mobile-nav"
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
                @auth
                    <a href="{{ auth()->user()->role === 'owner' ? route('owner.dashboard') : (auth()->user()->role === 'seller' ? route('seller.dashboard') : route('dashboard')) }}" class="btn btn-md btn-outline">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-md btn-outline w-full">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-md btn-outline">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-md btn-primary">Daftar Gratis</a>
                @endauth
            </div>
        </nav>
    </div>
</header>

{{-- Spacer so the fixed header never overlaps page content --}}
<div class="h-18" aria-hidden="true"></div>
