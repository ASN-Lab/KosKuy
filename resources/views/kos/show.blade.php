@extends('layouts.app')

@section('title', $kos->title . ' — ' . rupiah($kos->price_per_month) . '/bulan | KosKuy')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $kos->description), 155))

@section('content')

    @php
        $image = listingImage($kos);
        $gallery = $kos->images->map(fn ($img) => asset('storage/' . $img->image_path))->values();
        $wa = waLink($kos->owner?->phone, 'Halo ' . ($kos->owner?->name ?? 'pemilik') . ', saya tertarik dengan "' . $kos->title . '" di KosKuy. Apakah kamar masih tersedia?');
        $mapsEmbed = mapsEmbedUrl($kos->latitude, $kos->longitude);
        $mapsUrl = mapsLink($kos->latitude, $kos->longitude);
    @endphp

    <section class="border-b border-ink-100 bg-ink-50/60">
        <div class="container-page py-6 sm:py-8">
            <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-sm text-ink-500">
                <a href="{{ route('home') }}" class="font-semibold transition hover:text-brand-700">Beranda</a>
                <x-ui.icon name="chevron-right" class="size-3.5 text-ink-300" />
                <a href="{{ route('kos.index') }}" class="font-semibold transition hover:text-brand-700">Cari Kos</a>
                <x-ui.icon name="chevron-right" class="size-3.5 text-ink-300" />
                <span class="truncate font-semibold text-ink-900">{{ $kos->title }}</span>
            </nav>
        </div>
    </section>

    <section class="section-tight pb-8">
        <div class="container-page">
            <div class="grid gap-10 lg:grid-cols-12 lg:gap-12">

                {{-- ================= LEFT COLUMN ================= --}}
                <div class="lg:col-span-7 xl:col-span-8">

                    {{-- Gallery --}}
                    <div
                        x-data="gallery({{ max($gallery->count(), 1) }})"
                        class="reveal"
                    >
                        @if ($gallery->isNotEmpty())
                            <div class="relative overflow-hidden rounded-4xl border border-ink-100 bg-white shadow-soft">
                                {{-- Main image --}}
                                <div class="media aspect-16/10 sm:aspect-16/9">
                                    <template x-for="(src, i) in {{ $gallery->toJson() }}" :key="src">
                                        <img
                                            x-show="index === i"
                                            x-transition.opacity.duration.400ms
                                            :src="src"
                                            alt="{{ $kos->title }}"
                                            class="media-img absolute inset-0"
                                        >
                                    </template>
                                </div>

                                @if ($gallery->count() > 1)
                                    {{-- Arrows --}}
                                    <button
                                        type="button"
                                        @click="prev()"
                                        class="absolute top-1/2 left-4 grid size-10 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-ink-800 shadow-card backdrop-blur transition hover:bg-white"
                                        aria-label="Foto sebelumnya"
                                    >
                                        <x-ui.icon name="chevron-left" class="size-5" />
                                    </button>
                                    <button
                                        type="button"
                                        @click="next()"
                                        class="absolute top-1/2 right-4 grid size-10 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-ink-800 shadow-card backdrop-blur transition hover:bg-white"
                                        aria-label="Foto berikutnya"
                                    >
                                        <x-ui.icon name="chevron-right" class="size-5" />
                                    </button>

                                    {{-- Counter --}}
                                    <span class="absolute right-4 bottom-4 rounded-full bg-ink-950/70 px-3 py-1.5 text-xs font-bold text-white backdrop-blur">
                                        <span x-text="index + 1"></span>/{{ $gallery->count() }}
                                    </span>
                                @endif

                                {{-- Badges --}}
                                <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                                    <span class="badge-glass">
                                        <x-ui.icon :name="genderIcon($kos->gender_type)" class="size-3.5" />
                                        {{ genderLabel($kos->gender_type) }}
                                    </span>
                                    @unless ($kos->is_available)
                                        <span class="badge bg-rose-600 text-white">Kamar penuh</span>
                                    @endunless
                                </div>
                            </div>

                            {{-- Thumbnails --}}
                            @if ($gallery->count() > 1)
                                <div class="no-scrollbar mt-3 flex gap-3 overflow-x-auto pb-1">
                                    @foreach ($gallery as $i => $src)
                                        <button
                                            type="button"
                                            @click="go({{ $i }})"
                                            class="media aspect-4/3 w-24 shrink-0 rounded-2xl border-2 transition sm:w-28"
                                            :class="index === {{ $i }} ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-transparent opacity-70 hover:opacity-100'"
                                            aria-label="Lihat foto {{ $i + 1 }}"
                                        >
                                            <img src="{{ $src }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="grid aspect-16/9 place-items-center rounded-4xl border border-ink-100 bg-brand-soft">
                                <div class="text-center">
                                    <span class="mx-auto grid size-16 place-items-center rounded-3xl bg-white/80 text-brand-400 shadow-soft">
                                        <x-ui.icon name="building-office-2" class="size-8" />
                                    </span>
                                    <p class="mt-4 text-sm font-semibold text-ink-500">Pemilik belum mengunggah foto</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Title block (mobile) --}}
                    <div class="mt-8 lg:hidden">
                        <h1 class="text-2xl leading-tight font-extrabold sm:text-3xl">{{ $kos->title }}</h1>
                        <p class="mt-2 flex items-start gap-1.5 text-sm text-ink-500">
                            <x-ui.icon name="map-pin" class="mt-0.5 size-4 shrink-0 text-ink-400" />
                            {{ $kos->address }}
                        </p>
                    </div>

                    {{-- Key facts --}}
                    <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach ([
                            ['icon' => 'banknotes', 'label' => 'Harga / bulan', 'value' => rupiah($kos->price_per_month), 'tone' => 'text-brand-600 bg-brand-50'],
                            ['icon' => 'users', 'label' => 'Jenis kamar', 'value' => genderLabel($kos->gender_type), 'tone' => 'text-accent-600 bg-accent-50'],
                            ['icon' => 'check-badge', 'label' => 'Status', 'value' => $kos->is_available ? 'Tersedia' : 'Penuh', 'tone' => $kos->is_available ? 'text-accent-600 bg-accent-50' : 'text-rose-600 bg-rose-50'],
                            ['icon' => 'photo', 'label' => 'Foto tersedia', 'value' => $kos->images->count() . ' foto', 'tone' => 'text-violet-600 bg-violet-50'],
                        ] as $fact)
                            <div class="rounded-3xl border border-ink-100 bg-white p-4">
                                <span class="grid size-9 place-items-center rounded-xl {{ $fact['tone'] }}">
                                    <x-ui.icon :name="$fact['icon']" class="size-4.5" />
                                </span>
                                <p class="mt-3 text-[11px] font-semibold tracking-wide text-ink-400 uppercase">{{ $fact['label'] }}</p>
                                <p class="mt-0.5 truncate text-sm font-bold text-ink-900">{{ $fact['value'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Description --}}
                    <div class="mt-10">
                        <h2 class="heading-md">Tentang {{ $kos->title }}</h2>
                        <div class="rich-text mt-4">
                            @if (filled($kos->description))
                                @foreach (preg_split('/\R{2,}/', trim($kos->description)) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            @else
                                <p class="text-ink-400">Pemilik belum menambahkan deskripsi untuk listing ini.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Facilities --}}
                    <div class="mt-10">
                        <div class="flex items-center justify-between gap-4">
                            <h2 class="heading-md">Fasilitas</h2>
                            @if ($kos->facilities->isNotEmpty())
                                <span class="badge-ink">{{ $kos->facilities->count() }} fasilitas</span>
                            @endif
                        </div>

                        @if ($kos->facilities->isNotEmpty())
                            <ul class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($kos->facilities as $facility)
                                    <li class="flex items-center gap-3 rounded-2xl border border-ink-100 bg-white px-4 py-3.5 text-sm font-semibold text-ink-700">
                                        <span class="grid size-9 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-600">
                                            <x-ui.icon :name="facilityIcon($facility->facility_name)" class="size-4.5" />
                                        </span>
                                        {{ $facility->facility_name }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-4 rounded-2xl border border-dashed border-ink-200 bg-ink-50 px-5 py-6 text-sm text-ink-500">
                                Informasi fasilitas belum tersedia untuk listing ini.
                            </p>
                        @endif
                    </div>

                    {{-- Location --}}
                    <div class="mt-10">
                        <h2 class="heading-md">Lokasi</h2>

                        <div class="mt-5 overflow-hidden rounded-4xl border border-ink-100 bg-white shadow-soft">
                            @if ($mapsEmbed)
                                <div class="relative aspect-16/10 bg-ink-100">
                                    <iframe
                                        src="{{ $mapsEmbed }}"
                                        title="Peta lokasi {{ $kos->title }}"
                                        class="absolute inset-0 h-full w-full"
                                        loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"
                                    ></iframe>
                                </div>
                            @else
                                <div class="grid aspect-16/10 place-items-center bg-ink-50 px-6 text-center">
                                    <div>
                                        <span class="mx-auto grid size-14 place-items-center rounded-3xl bg-white text-ink-300 shadow-xs">
                                            <x-ui.icon name="map-pin" class="size-7" />
                                        </span>
                                        <p class="mt-4 text-sm font-semibold text-ink-600">Koordinat belum diisi</p>
                                        <p class="mt-1 text-xs text-ink-400">Alamat lengkap: {{ $kos->address }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="flex flex-col gap-3 border-t border-ink-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                                <p class="flex items-start gap-2 text-sm text-ink-600">
                                    <x-ui.icon name="map-pin" class="mt-0.5 size-4 shrink-0 text-ink-400" />
                                    <span>
                                        {{ $kos->address }}
                                        @if ($kos->latitude)
                                            <span class="mt-0.5 block text-xs text-ink-400">
                                                {{ number_format((float) $kos->latitude, 5) }}, {{ number_format((float) $kos->longitude, 5) }}
                                            </span>
                                        @endif
                                    </span>
                                </p>

                                @if ($mapsUrl)
                                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline shrink-0">
                                        <x-ui.icon name="map-pin" class="size-4" />
                                        Buka Google Maps
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

    <div class="bg-white p-8 rounded-xl shadow-md">
        <h1 class="text-3xl font-bold mb-4">{{ $kos->title }}</h1>
        <p class="text-2xl text-indigo-600 font-bold mb-4">Rp {{ number_format($kos->price_per_month, 0, ',', '.') }} / bulan</p>
        <span class="inline-block bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm capitalize mb-6">{{ $kos->gender_type }}</span>

        <h3 class="font-semibold text-lg mb-2">Deskripsi</h3>
        <p class="text-gray-600 mb-6">{{ $kos->description }}</p>

        <h3 class="font-semibold text-lg mb-2">Alamat</h3>
        <p class="text-gray-600 mb-6">{{ $kos->address }}</p>

        @if($kos->facilities->isNotEmpty())
        <h3 class="font-semibold text-lg mb-2">Fasilitas</h3>
        <ul class="list-disc list-inside text-gray-600 mb-6">
            @foreach($kos->facilities as $facility)
            <li>{{ $facility->facility_name }}</li>
            @endforeach
        </ul>
        @endif

        @if($kos->latitude && $kos->longitude)
        <h3 class="font-semibold text-lg mb-2">Lokasi</h3>
        <a href="https://www.google.com/maps?q={{ $kos->latitude }},{{ $kos->longitude }}" target="_blank" class="text-indigo-600 underline mb-6 inline-block">Lihat di Google Maps</a>
        @endif

        @auth
            @if(auth()->id() !== $kos->owner_id)
            <button type="button" onclick="startChat('kos', {{ $kos->id }}, {{ $kos->owner_id }})"
               class="block w-full bg-indigo-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-indigo-700 mt-4">
                Chat Pemilik
            </button>
            @endif
        @endauth

        @if($kos->owner && $kos->owner->phone)
        <a href="{{ waLink($kos->owner->phone, 'Halo, saya tertarik dengan kos ' . $kos->title) }}" target="_blank"
           class="block w-full bg-green-500 text-white text-center py-3 rounded-lg font-semibold hover:bg-green-600 mt-4">
            Hubungi Pemilik via WhatsApp
        </a>
        @endif

        @auth
            @if(auth()->user()->role === 'student')
                @if($kos->is_available)
                    <form method="POST" action="{{ route('web.bookings.store') }}">
                        @csrf
                        <input type="hidden" name="kos_id" value="{{ $kos->id }}">
                        <input type="date" name="booking_date" class="field w-full mb-2" required min="{{ date('Y-m-d') }}">
                        <textarea name="notes" placeholder="Catatan untuk pemilik (opsional)" class="field w-full mb-2"></textarea>
                        <button type="submit" class="block w-full bg-indigo-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-indigo-700">
                            Booking Kos Ini
                        </button>
                    </form>
                @else
                    <p class="text-center text-gray-400 mt-4">Kos ini sedang tidak tersedia untuk booking.</p>
                @endif
            @endif
        @else
            <a href="{{ route('login') }}" class="block w-full bg-gray-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-gray-700 mt-4">
                Login untuk Booking
            </a>
        @endauth
                    {{-- Similar --}}
                    @if ($similarKos->isNotEmpty())
                        <div class="mt-14">
                            <div class="flex items-end justify-between gap-4">
                                <h2 class="heading-md">Kos serupa</h2>
                                <a href="{{ route('kos.index', ['gender_type' => $kos->gender_type]) }}" class="text-sm font-bold text-brand-700 hover:underline">
                                    Lihat semua
                                </a>
                            </div>

                            <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach ($similarKos as $item)
                                    @include('partials.kos-card', ['kos' => $item, 'compact' => true])
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ================= RIGHT COLUMN (sticky) ================= --}}
                <div class="lg:col-span-5 xl:col-span-4">
                    <div class="space-y-5 lg:sticky lg:top-24">
                        {{-- Booking card --}}
                        <div class="card overflow-hidden">
                            <div class="bg-linear-to-br from-brand-600 to-violet-600 p-6 text-white">
                                <p class="text-[11px] font-bold tracking-[0.14em] text-brand-100 uppercase">Sewa bulanan</p>
                                <p class="mt-2 flex items-baseline gap-1.5">
                                    <span class="font-display text-4xl font-extrabold">{{ rupiah($kos->price_per_month) }}</span>
                                    <span class="text-sm font-semibold text-brand-100">/ bulan</span>
                                </p>
                                <p class="mt-2 text-xs text-brand-100">
                                    Belum termasuk biaya listrik dan air.
                                </p>
                            </div>

                            <div class="space-y-3 p-6">
                                @if ($wa)
                                    <a
                                        href="{{ $wa }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="btn btn-lg btn-accent w-full"
                                    >
                                        <x-ui.icon name="chat-bubble-oval-left" class="size-5" />
                                        Chat Pemilik via WhatsApp
                                    </a>
                                @else
                                    <a href="{{ route('kos.index') }}" class="btn btn-lg btn-primary w-full">
                                        Cari kos lain
                                    </a>
                                @endif

                                <div class="grid grid-cols-2 gap-3">
                                    <button
                                        type="button"
                                        x-data="{ url: @js(url()->current()), title: @js($kos->title) }"
                                        @click="share(url, title)"
                                        class="btn btn-md btn-outline"
                                    >
                                        <x-ui.icon name="share" class="size-4" />
                                        Bagikan
                                    </button>
                                    <button type="button" onclick="window.print()" class="btn btn-md btn-outline">
                                        <x-ui.icon name="printer" class="size-4" />
                                        Cetak
                                    </button>
                                </div>

                                <p class="flex items-start gap-2 pt-1 text-xs leading-relaxed text-ink-400">
                                    <x-ui.icon name="shield-check" class="mt-0.5 size-4 shrink-0 text-accent-500" />
                                    Transaksi dilakukan langsung antara kamu dan pemilik. KosKuy tidak memproses pembayaran.
                                </p>
                            </div>
                        </div>

                        {{-- Owner --}}
                        <div class="card p-6">
                            <h2 class="text-sm font-bold tracking-wide text-ink-400 uppercase">Pemilik kos</h2>

                            @if ($kos->owner)
                                <div class="mt-4 flex items-center gap-3.5">
                                    <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-base font-extrabold text-brand-700">
                                        {{ initials($kos->owner->name) }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-ink-900">{{ $kos->owner->name }}</p>
                                        <p class="truncate text-xs text-ink-400">
                                            Bergabung {{ $kos->owner->created_at?->translatedFormat('M Y') ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-5 grid grid-cols-2 gap-2.5">
                                    <div class="rounded-2xl bg-ink-50 px-3.5 py-3">
                                        <p class="text-[11px] font-semibold text-ink-400">Unit aktif</p>
                                        <p class="text-lg font-extrabold text-ink-900">{{ $kos->owner->kosListings()->where('is_available', true)->count() }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-ink-50 px-3.5 py-3">
                                        <p class="text-[11px] font-semibold text-ink-400">Rating</p>
                                        <p class="flex items-center gap-1 text-lg font-extrabold text-ink-900">
                                            4.9
                                            <x-ui.icon name="star" class="size-4 text-sun-400" />
                                        </p>
                                    </div>
                                </div>
                            @else
                                <p class="mt-3 text-sm text-ink-500">Informasi pemilik tidak tersedia.</p>
                            @endif
                        </div>

                        {{-- Safety tips --}}
                        <div class="rounded-4xl border border-ink-100 bg-ink-50 p-6">
                            <h2 class="flex items-center gap-2 text-sm font-bold text-ink-900">
                                <x-ui.icon name="shield-check" class="size-4.5 text-accent-600" />
                                Tips aman bertransaksi
                            </h2>
                            <ul class="mt-4 space-y-2.5 text-[13px] leading-relaxed text-ink-600">
                                @foreach ([
                                    'Kunjungi langsung sebelum membayar.',
                                    'Minta foto dan video kamar terbaru.',
                                    'Pastikan biaya utilitas jelas sejak awal.',
                                    'Simpan bukti transaksi atau kontrak sewanya.',
                                ] as $tip)
                                    <li class="flex gap-2.5">
                                        <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-brand-400"></span>
                                        {{ $tip }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        /* Shared helpers: native share with clipboard fallback, plus a tiny toast. */
        

        window.share = async (url, title) => {
            if (navigator.share) {
                try {
                    await navigator.share({ title, url });
                    return;
                } catch (error) {
                    // Share sheet dismissed — fall through to the clipboard.
                }
            }

            try {
                await navigator.clipboard.writeText(url);
                window.koskuyToast('Tautan listing disalin ke clipboard.');
            } catch (error) {
                window.koskuyToast('Tidak bisa menyalin tautan.');
            }
        };
    </script>
@endpush

@section('mobile-bar')
    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-ink-100 bg-white/95 p-3 backdrop-blur-xl lg:hidden">
        <div class="flex items-center gap-3">
            <div class="min-w-0 flex-1">
                <p class="truncate font-display text-lg font-extrabold text-brand-700">{{ rupiah($kos->price_per_month) }}</p>
                <p class="truncate text-[11px] text-ink-400">per bulan</p>
            </div>
            @if ($wa)
                <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-md btn-accent flex-1">
                    <x-ui.icon name="chat-bubble-oval-left" class="size-4" />
                    Chat Pemilik
                </a>
            @endif
        </div>
    </div>
    <div class="h-20 lg:hidden" aria-hidden="true"></div>
@endsection
