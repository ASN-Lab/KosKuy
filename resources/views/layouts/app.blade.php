<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $title ?? 'KosKuy — Cari Kos & Barang Bekas Berkualitas untuk Mahasiswa')</title>

    <meta name="description" content="@yield('meta_description', 'KosKuy adalah platform marketplace kos dan barang bekas untuk mahasiswa. Cari kos sesuai budget, bandingkan fasilitas, lalu chat pemilik langsung lewat WhatsApp. 100% gratis.')">
    <meta name="theme-color" content="#4f46e5">
    <meta name="author" content="KosKuy">

    {{-- Social sharing --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="KosKuy">
    <meta property="og:locale" content="id_ID">
    <meta property="og:title" content="@yield('title', 'KosKuy — Kos Impian, Thrift Pilihan')">
    <meta property="og:description" content="@yield('meta_description', 'Platform kos & barang bekas untuk mahasiswa. Gratis, aman, dan langsung chat pemilik.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/og-cover.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'KosKuy — Kos Impian, Thrift Pilihan')">
    <meta name="twitter:description" content="@yield('meta_description', 'Platform kos & barang bekas untuk mahasiswa.')">
    <meta name="twitter:image" content="{{ asset('images/og-cover.png') }}">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.png') }}" sizes="32x32">
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
<body class="min-h-screen bg-white text-ink-700 antialiased">

    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:rounded-full focus:bg-brand-600 focus:px-5 focus:py-2.5 focus:text-sm focus:font-semibold focus:text-white">
        Lewati ke konten utama
    </a>

    @include('partials.navbar')

    @include('partials.flash')

    <main id="main">
        @yield('content')
    </main>

    @include('partials.footer')

    @push('scripts')
    <script>
        window.koskuyToast = (message) => {
            let toast = document.getElementById('koskuy-toast');

            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'koskuy-toast';
                toast.className = 'fixed bottom-24 left-1/2 z-90 -translate-x-1/2 rounded-full bg-ink-950 px-5 py-3 text-sm font-semibold text-white shadow-lift transition-opacity duration-300 opacity-0';
                document.body.appendChild(toast);
            }

            toast.textContent = message;
            toast.style.opacity = '1';

            clearTimeout(window.__koskuyToastTimer);
            window.__koskuyToastTimer = setTimeout(() => {
                toast.style.opacity = '0';
            }, 2600);
        };

        window.startChat = async (type, id, ownerId) => {
            const res = await fetch('{{ url('/messages/start') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ listing_type: type, listing_id: id, owner_id: ownerId })
            });
            if (res.redirected) window.location.href = res.url;
            else window.koskuyToast('Gagal memulai chat. Coba login ulang.');
        };
    </script>
    @endpush

    @stack('modals')
    @stack('scripts')

    {{-- Mobile sticky action bar: only rendered on listing pages --}}
    @hasSection('mobile-bar')
        @yield('mobile-bar')
    @endif
</body>
</html>
