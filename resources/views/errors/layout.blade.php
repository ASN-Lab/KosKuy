<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>@yield('title', 'Terjadi kesalahan') — {{ config('koskuy.name', 'KosKuy') }}</title>
    <meta name="theme-color" content="#4f46e5">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('images/app-icon-192.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen overflow-hidden bg-ink-gradient text-white antialiased">

    <div class="grid-lines pointer-events-none absolute inset-0 opacity-70" aria-hidden="true"></div>
    <div class="blob -top-24 -right-16 size-96 bg-brand-500/40 blur-3xl" aria-hidden="true"></div>
    <div class="blob -bottom-24 -left-16 size-96 bg-accent-400/30 blur-3xl" aria-hidden="true"></div>

    <main class="relative grid min-h-screen place-items-center px-5 py-16">
        <div class="w-full max-w-lg text-center">

            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                <x-ui.logo class="size-10" />
                <span class="font-display text-xl font-extrabold tracking-tight text-white">{{ config('koskuy.name', 'KosKuy') }}</span>
            </a>

            <p class="mt-10 bg-brand-gradient bg-clip-text text-7xl font-black tracking-tight text-transparent sm:text-8xl">
                @yield('code')
            </p>

            <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                @yield('heading')
            </h1>

            <p class="mx-auto mt-3 max-w-sm text-sm leading-relaxed text-ink-300">
                @yield('message')
            </p>

            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('home') }}" class="btn btn-md btn-gradient w-full sm:w-auto">
                    <x-ui.icon name="home" class="size-4" />
                    Kembali ke beranda
                </a>

                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-md w-full border border-white/15 bg-white/10 text-white backdrop-blur transition hover:bg-white/20 sm:w-auto">
                        <x-ui.icon name="arrow-right" class="size-4" />
                        Ke dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-md w-full border border-white/15 bg-white/10 text-white backdrop-blur transition hover:bg-white/20 sm:w-auto">
                        <x-ui.icon name="magnifying-glass" class="size-4" />
                        Masuk
                    </a>
                @endauth
            </div>

            @hasSection('actions')
                <div class="mt-4">@yield('actions')</div>
            @endif
        </div>
    </main>

    @include('partials.offline')
</body>
</html>
