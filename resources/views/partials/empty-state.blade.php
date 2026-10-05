@props([
    'icon' => 'magnifying-glass',
    'title' => 'Belum ada hasil',
    'description' => 'Coba ubah kata kunci atau longgarkan filter pencarian kamu.',
    'actionLabel' => null,
    'actionHref' => null,
])

<div class="reveal mx-auto flex max-w-md flex-col items-center rounded-4xl border border-dashed border-ink-200 bg-ink-50/60 px-6 py-14 text-center">
    <span class="grid size-16 place-items-center rounded-3xl bg-white text-brand-400 shadow-soft">
        <x-ui.icon :name="$icon" class="size-8" />
    </span>

    <h3 class="mt-5 text-lg font-bold">{{ $title }}</h3>
    <p class="mt-2 text-sm leading-relaxed text-ink-500">{{ $description }}</p>

    @if ($actionLabel && $actionHref)
        <a href="{{ $actionHref }}" class="btn btn-md btn-primary mt-6">
            {{ $actionLabel }}
        </a>
    @endif
</div>
