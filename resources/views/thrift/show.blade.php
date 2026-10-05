@extends('layouts.app')

@section('title', $product->title . ' — ' . rupiah($product->price) . ' | Thrift KosKuy')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 155))

@section('content')

    @php
        $gallery = $product->images->map(fn ($img) => asset('storage/' . $img->image_path))->values();
        $wa = waLink($product->seller?->phone, 'Halo ' . ($product->seller?->name ?? 'penjual') . ', saya tertarik dengan "' . $product->title . '" di KosKuy. Apakah masih tersedia?');
        $shareUrl = url()->current();
    @endphp

    <section class="border-b border-ink-100 bg-ink-50/60">
        <div class="container-page py-6 sm:py-8">
            <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-sm text-ink-500">
                <a href="{{ route('home') }}" class="font-semibold transition hover:text-accent-700">Beranda</a>
                <x-ui.icon name="chevron-right" class="size-3.5 text-ink-300" />
                <a href="{{ route('thrift.index') }}" class="font-semibold transition hover:text-accent-700">Thrift</a>
                @if ($product->category)
                    <x-ui.icon name="chevron-right" class="size-3.5 text-ink-300" />
                    <a href="{{ route('thrift.index', ['category_id' => $product->category->id]) }}" class="font-semibold transition hover:text-accent-700">
                        {{ $product->category->name }}
                    </a>
                @endif
            </nav>
        </div>
    </section>

    <section class="section-tight pb-8">
        <div class="container-page">
            <div class="grid gap-10 lg:grid-cols-12 lg:gap-12">

                {{-- ================= LEFT ================= --}}
                <div class="lg:col-span-7 xl:col-span-8">
                    <div x-data="gallery({{ max($gallery->count(), 1) }})" class="reveal">
                        @if ($gallery->isNotEmpty())
                            <div class="relative overflow-hidden rounded-4xl border border-ink-100 bg-white shadow-soft">
                                <div class="media aspect-4/3 sm:aspect-16/10">
                                    <template x-for="(src, i) in {{ $gallery->toJson() }}" :key="src">
                                        <img
                                            x-show="index === i"
                                            x-transition.opacity.duration.400ms
                                            :src="src"
                                            alt="{{ $product->title }}"
                                            class="media-img absolute inset-0"
                                        >
                                    </template>
                                </div>

                                @if ($gallery->count() > 1)
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
                                    <span class="absolute right-4 bottom-4 rounded-full bg-ink-950/70 px-3 py-1.5 text-xs font-bold text-white backdrop-blur">
                                        <span x-text="index + 1"></span>/{{ $gallery->count() }}
                                    </span>
                                @endif

                                <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                                    @if ($product->is_sold)
                                        <span class="badge bg-ink-900 text-white">Terjual</span>
                                    @else
                                        <span class="badge bg-accent-600 text-white">
                                            <x-ui.icon name="check" class="size-3" />
                                            Tersedia
                                        </span>
                                    @endif
                                    @if ($product->category)
                                        <span class="badge-glass">
                                            <x-ui.icon :name="categoryIcon($product->category)" class="size-3.5" />
                                            {{ $product->category->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if ($gallery->count() > 1)
                                <div class="no-scrollbar mt-3 flex gap-3 overflow-x-auto pb-1">
                                    @foreach ($gallery as $i => $src)
                                        <button
                                            type="button"
                                            @click="go({{ $i }})"
                                            class="aspect-4/3 w-24 shrink-0 overflow-hidden rounded-2xl border-2 transition sm:w-28"
                                            :class="index === {{ $i }} ? 'border-accent-500 ring-2 ring-accent-500/20' : 'border-transparent opacity-70 hover:opacity-100'"
                                            aria-label="Lihat foto {{ $i + 1 }}"
                                        >
                                            <img src="{{ $src }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="grid aspect-4/3 place-items-center rounded-4xl border border-ink-100 bg-linear-to-br from-accent-50 to-brand-soft">
                                <div class="text-center">
                                    <span class="mx-auto grid size-16 place-items-center rounded-3xl bg-white/80 text-accent-500 shadow-soft">
                                        <x-ui.icon :name="categoryIcon($product->category)" class="size-8" />
                                    </span>
                                    <p class="mt-4 text-sm font-semibold text-ink-500">Penjual belum mengunggah foto</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-8">
                        <h1 class="text-2xl leading-tight font-extrabold sm:text-3xl">{{ $product->title }}</h1>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="badge {{ conditionTone($product->condition_status) }} ring-1">
                                <x-ui.icon name="check-badge" class="size-3.5" />
                                {{ $product->condition_status ?: 'Kondisi tidak dicantumkan' }}
                            </span>
                            <span class="badge-ink">
                                <x-ui.icon name="clock" class="size-3.5" />
                                Diunggah {{ $product->created_at?->translatedFormat('d M Y') ?? '-' }}
                            </span>
                        </div>

                        <div class="rich-text mt-6">
                            @if (filled($product->description))
                                @foreach (preg_split('/\R{2,}/', trim($product->description)) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            @else
                                <p class="text-ink-400">Penjual belum menambahkan deskripsi untuk barang ini.</p>
                            @endif
                        </div>
                    </div>

                    @if ($relatedProducts->isNotEmpty())
                        <div class="mt-14">
                            <div class="flex items-end justify-between gap-4">
                                <h2 class="heading-md">Barang serupa</h2>
                                <a href="{{ route('thrift.index', $product->category ? ['category_id' => $product->category->id] : []) }}" class="text-sm font-bold text-accent-700 hover:underline">
                                    Lihat semua
                                </a>
                            </div>

                            <div class="mt-6 grid grid-cols-2 gap-5 xl:grid-cols-3">
                                @foreach ($relatedProducts as $item)
                                    @include('partials.thrift-card', ['product' => $item])
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ================= RIGHT ================= --}}
                <div class="lg:col-span-5 xl:col-span-4">
                    <div class="space-y-5 lg:sticky lg:top-24">
                        <div class="card overflow-hidden">
                            <div class="bg-linear-to-br from-accent-600 to-emerald-500 p-6 text-white">
                                <p class="text-[11px] font-bold tracking-[0.14em] text-white/80 uppercase">Harga barang</p>
                                <p class="mt-2 font-display text-4xl font-extrabold">{{ rupiah($product->price) }}</p>
                                <p class="mt-2 text-xs text-white/85">KosKuy tidak memotong komisi dari penjual.</p>
                            </div>

                            <div class="space-y-3 p-6">
                                @if ($wa)
                                    <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-lg btn-accent w-full">
                                        <x-ui.icon name="chat-bubble-oval-left" class="size-5" />
                                        Chat Penjual via WhatsApp
                                    </a>
                                @else
                                    <a href="{{ route('thrift.index') }}" class="btn btn-lg btn-primary w-full">
                                        Lihat barang lain
                                    </a>
                                @endif

                                <div class="grid grid-cols-2 gap-3">
                                    <button
                                        type="button"
                                        x-data="{ url: @js($shareUrl), title: @js($product->title) }"
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
                            </div>
                        </div>

                        @if ($product->seller)
                            <div class="card p-6">
                                <h2 class="text-sm font-bold tracking-wide text-ink-400 uppercase">Penjual</h2>

                                <div class="mt-4 flex items-center gap-3.5">
                                    <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-accent-100 text-base font-extrabold text-accent-700">
                                        {{ initials($product->seller->name) }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-ink-900">{{ $product->seller->name }}</p>
                                        <p class="truncate text-xs text-ink-400">
                                            Bergabung {{ $product->seller->created_at?->translatedFormat('M Y') ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-5 grid grid-cols-2 gap-2.5">
                                    <div class="rounded-2xl bg-ink-50 px-3.5 py-3">
                                        <p class="text-[11px] font-semibold text-ink-400">Barang aktif</p>
                                        <p class="text-lg font-extrabold text-ink-900">{{ $product->seller->thriftProducts()->where('is_sold', false)->count() }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-ink-50 px-3.5 py-3">
                                        <p class="text-[11px] font-semibold text-ink-400">Rating</p>
                                        <p class="flex items-center gap-1 text-lg font-extrabold text-ink-900">
                                            4.8
                                            <x-ui.icon name="star" class="size-4 text-sun-400" />
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="card p-6">
                                <p class="text-sm text-ink-500">Informasi penjual tidak tersedia untuk barang ini.</p>
                            </div>
                        @endif

                        <div class="rounded-4xl border border-ink-100 bg-ink-50 p-6">
                            <h2 class="flex items-center gap-2 text-sm font-bold text-ink-900">
                                <x-ui.icon name="shield-check" class="size-4.5 text-accent-600" />
                                Tips transaksi aman
                            </h2>
                            <ul class="mt-4 space-y-2.5 text-[13px] leading-relaxed text-ink-600">
                                @foreach ([
                                    'Cek kondisi fisik barang saat terima.',
                                    'Sepakati titik temu yang aman dan ramai.',
                                    'Bayar hanya setelah barang sesuai.',
                                ] as $tip)
                                    <li class="flex gap-2.5">
                                        <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-accent-400"></span>
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
                window.koskuyToast('Tautan barang disalin ke clipboard.');
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
                <p class="truncate font-display text-lg font-extrabold text-accent-700">{{ rupiah($product->price) }}</p>
                <p class="truncate text-[11px] text-ink-400">{{ $product->condition_status ?: 'Kondisi tidak dicantumkan' }}</p>
            </div>
            @if ($wa)
                <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-md btn-accent flex-1">
                    <x-ui.icon name="chat-bubble-oval-left" class="size-4" />
                    Chat Penjual
                </a>
            @endif
        </div>
    </div>
    <div class="h-20 lg:hidden" aria-hidden="true"></div>
@endsection
