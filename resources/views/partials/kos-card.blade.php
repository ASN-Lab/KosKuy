@props([
    'kos',
    'compact' => false,
])

@php
    $image = listingImage($kos);
    $facilities = $kos->facilities->take(3);
@endphp

<article class="group card card-hover relative flex flex-col overflow-hidden">
    {{-- Media --}}
    <a href="{{ route('kos.show', $kos->id) }}" class="media block {{ $compact ? 'h-44' : 'h-56' }}" tabindex="-1" aria-hidden="true">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $kos->title }}" class="media-img" loading="lazy" decoding="async">
        @else
            <span class="absolute inset-0 grid place-items-center bg-brand-soft">
                <span class="grid size-14 place-items-center rounded-2xl bg-white/80 text-brand-400 shadow-soft">
                    <x-ui.icon name="building-office-2" class="size-7" />
                </span>
            </span>
        @endif

        {{-- Gradient scrim keeps the badges legible on any photo --}}
        <span class="pointer-events-none absolute inset-x-0 top-0 h-24 bg-linear-to-b from-ink-950/35 to-transparent" aria-hidden="true"></span>

        <span class="absolute top-3 left-3 flex flex-wrap gap-1.5">
            <span class="badge-glass">
                <x-ui.icon :name="genderIcon($kos->gender_type)" class="size-3.5" />
                {{ genderLabel($kos->gender_type) }}
            </span>
            @if (! $kos->is_available)
                <span class="badge bg-rose-600 text-white">Full</span>
            @endif
        </span>

        @if ($kos->latitude && $kos->longitude)
            <span class="absolute right-3 bottom-3 badge-glass">
                <x-ui.icon name="map-pin" class="size-3.5" />
                Dekat kampus
            </span>
        @endif
    </a>

    {{-- Body --}}
    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-start justify-between gap-3">
            <h3 class="line-clamp-2 text-base leading-snug font-bold">
                <a href="{{ route('kos.show', $kos->id) }}" class="transition-colors group-hover:text-brand-700">
                    {{ $kos->title }}
                </a>
            </h3>
            <x-ui.icon name="arrow-up-right" class="mt-0.5 size-5 shrink-0 text-ink-300 transition-all duration-300 group-hover:-translate-y-0.5 group-hover:translate-x-0.5 group-hover:text-brand-500" />
        </div>

        <p class="mt-1.5 flex items-start gap-1.5 text-[13px] leading-relaxed text-ink-500">
            <x-ui.icon name="map-pin" class="mt-0.5 size-3.5 shrink-0 text-ink-400" />
            <span class="line-clamp-1">{{ \Illuminate\Support\Str::limit($kos->address, 46) }}</span>
        </p>

        @if ($facilities->isNotEmpty())
            <ul class="mt-3.5 flex flex-wrap gap-1.5">
                @foreach ($facilities as $facility)
                    <li class="badge-ink">
                        <x-ui.icon :name="facilityIcon($facility->facility_name)" class="size-3" />
                        {{ $facility->facility_name }}
                    </li>
                @endforeach
                @if ($kos->facilities->count() > 3)
                    <li class="badge-ink">+{{ $kos->facilities->count() - 3 }}</li>
                @endif
            </ul>
        @endif

        <div class="mt-auto flex items-end justify-between gap-3 pt-5">
            <div>
                <p class="text-lg font-extrabold text-brand-700">
                    {{ rupiah($kos->price_per_month) }}
                    <span class="text-xs font-semibold text-ink-400">/bln</span>
                </p>
                @if ($kos->owner)
                    <p class="mt-0.5 text-xs text-ink-400">oleh {{ \Illuminate\Support\Str::limit($kos->owner->name, 22) }}</p>
                @endif
            </div>

            @php $wa = waLink($kos->owner?->phone, 'Halo ' . ($kos->owner?->name ?? 'pemilik') . ', saya tertarik dengan ' . $kos->title . ' di KosKuy.'); @endphp
            <a
                href="{{ $wa ?? route('kos.show', $kos->id) }}"
                @if ($wa) target="_blank" rel="noopener" @endif
                class="btn btn-sm {{ $wa ? 'btn-accent' : 'btn-outline' }}"
                aria-label="Hubungi pemilik {{ $kos->title }} lewat WhatsApp"
            >
                <x-ui.icon name="chat-bubble-oval-left" class="size-4" />
                <span class="hidden sm:inline">Chat</span>
            </a>
        </div>
    </div>
</article>
