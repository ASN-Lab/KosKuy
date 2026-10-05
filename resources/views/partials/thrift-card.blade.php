@props([
    'product',
])

@php
    $image = listingImage($product);
    $isNew = $product->created_at && $product->created_at->gt(now()->subDays(14));
    $discountGuess = null;
@endphp

<article class="group card card-hover relative flex flex-col overflow-hidden">
    <a href="{{ route('thrift.show', $product->id) }}" class="media block h-48" tabindex="-1" aria-hidden="true">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $product->title }}" class="media-img" loading="lazy" decoding="async">
        @else
            <span class="absolute inset-0 grid place-items-center bg-linear-to-br from-accent-50 to-brand-soft">
                <span class="grid size-14 place-items-center rounded-2xl bg-white/80 text-accent-500 shadow-soft">
                    <x-ui.icon :name="categoryIcon($product->category)" class="size-7" />
                </span>
            </span>
        @endif

        <span class="pointer-events-none absolute inset-x-0 top-0 h-20 bg-linear-to-b from-ink-950/30 to-transparent" aria-hidden="true"></span>

        <span class="absolute top-3 left-3 flex flex-wrap gap-1.5">
            @if ($isNew)
                <span class="badge bg-accent-600 text-white">
                    <x-ui.icon name="sparkles" class="size-3" />
                    Baru
                </span>
            @endif
            @if ($product->is_sold)
                <span class="badge bg-ink-900 text-white">Terjual</span>
            @endif
        </span>

        @if ($product->category)
            <span class="absolute right-3 bottom-3 badge-glass">
                <x-ui.icon :name="categoryIcon($product->category)" class="size-3.5" />
                {{ $product->category->name }}
            </span>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-4">
        <h3 class="line-clamp-2 text-[15px] leading-snug font-bold">
            <a href="{{ route('thrift.show', $product->id) }}" class="transition-colors group-hover:text-accent-700">
                {{ $product->title }}
            </a>
        </h3>

        <p class="mt-2 inline-flex w-fit rounded-full px-2 py-0.5 text-[11px] font-semibold ring-1 {{ conditionTone($product->condition_status) }}">
            {{ $product->condition_status ?: 'Kondisi tidak dicantumkan' }}
        </p>

        <div class="mt-auto flex items-end justify-between gap-3 pt-4">
            <div>
                <p class="text-base font-extrabold text-accent-700">{{ rupiah($product->price) }}</p>
                @if ($product->seller)
                    <p class="mt-0.5 text-xs text-ink-400">{{ \Illuminate\Support\Str::limit($product->seller->name, 18) }}</p>
                @endif
            </div>

            @php $wa = waLink($product->seller?->phone, 'Halo ' . ($product->seller?->name ?? 'penjual') . ', saya tertarik dengan ' . $product->title . ' di KosKuy.'); @endphp
            <a
                href="{{ $wa ?? route('thrift.show', $product->id) }}"
                @if ($wa) target="_blank" rel="noopener" @endif
                class="grid size-9 place-items-center rounded-full bg-ink-100 text-ink-600 transition hover:bg-accent-600 hover:text-white"
                aria-label="Tanya penjual tentang {{ $product->title }}"
            >
                <x-ui.icon name="chat-bubble-oval-left" class="size-4" />
            </a>
        </div>
    </div>
</article>
