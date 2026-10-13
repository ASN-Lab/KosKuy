<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Masuk ke KosKuy')</title>
    <meta name="description" content="@yield('meta_description', 'Masuk atau daftar akun KosKuy untuk mengelola listing kos dan barang thrift kamu.')">
    <meta name="robots" content="noindex">

    <meta name="theme-color" content="#4f46e5">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('images/app-icon-192.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-ink-50 text-ink-700 antialiased">

    @include('partials.flash')

    <main id="main" class="grid min-h-screen lg:grid-cols-2">
        {{-- Form column --}}
        <div class="flex flex-col">
            <div class="flex items-center justify-between px-5 py-5 sm:px-10">
                <a href="{{ route('home') }}" class="group inline-flex items-center gap-2.5">
                    <x-ui.logo class="size-9 transition-transform duration-300 group-hover:scale-105" />
                    <span class="font-display text-lg font-extrabold tracking-tight text-ink-900">KosKuy</span>
                </a>

                <a href="{{ route('home') }}" class="btn btn-sm btn-ghost">
                    <x-ui.icon name="chevron-left" class="size-4" />
                    Kembali
                </a>
            </div>

            <div class="flex flex-1 items-center justify-center px-5 pb-12 sm:px-10">
                <div class="w-full max-w-md">
                    @yield('form')
                </div>
            </div>
        </div>

        {{-- Brand panel --}}
        <aside class="relative hidden overflow-hidden bg-ink-gradient p-12 lg:flex lg:flex-col lg:justify-between">
            <div class="grid-lines pointer-events-none absolute inset-0 opacity-70" aria-hidden="true"></div>
            <div class="blob -top-24 -right-16 size-96 bg-brand-500/40 blur-3xl" aria-hidden="true"></div>
            <div class="blob -bottom-24 -left-16 size-96 bg-accent-400/30 blur-3xl" aria-hidden="true"></div>

            <div class="relative">
                <span class="badge-glass inline-flex">
                    <x-ui.icon name="shield-check" class="size-3.5 text-accent-300" />
                    100% gratis untuk mahasiswa
                </span>
            </div>

            <div class="relative max-w-md">
                <blockquote class="font-display text-2xl leading-snug font-extrabold text-white sm:text-3xl">
                    &ldquo;{{ $panelQuote ?? 'Kos bulan depan jangan sampai terlewat. Cari, bandingkan, lalu chat pemilik langsung.' }}&rdquo;
                </blockquote>

                <div class="mt-6 flex items-center gap-3">
                    <span class="grid size-11 place-items-center rounded-2xl bg-white/10 text-sm font-bold text-white">
                        {{ initials($panelAuthor ?? 'Tim KosKuy') }}
                    </span>
                    <div class="leading-tight">
                        <p class="text-sm font-bold text-white">{{ $panelAuthor ?? 'Tim KosKuy' }}</p>
                        <p class="text-xs text-ink-400">{{ $panelRole ?? 'Tim KosKuy Magelang' }}</p>
                    </div>
                </div>
            </div>

            <ul class="relative space-y-3">
                @foreach ([
                    'Buat listing kos dan thrift gratis.',
                    'Tanpa biaya agen dan tanpa perantara.',
                    'Chat langsung ke pemilik lewat WhatsApp.',
                ] as $point)
                    <li class="flex items-start gap-3 text-sm text-ink-300">
                        <span class="mt-0.5 grid size-5 shrink-0 place-items-center rounded-full bg-accent-500/20 text-accent-300">
                            <x-ui.icon name="check" class="size-3" />
                        </span>
                        {{ $point }}
                    </li>
                @endforeach
            </ul>
        </aside>
    </main>

    @stack('scripts')

    @include('partials.offline')
</body>
</html>