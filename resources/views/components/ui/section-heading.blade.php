@props([
    'title',
    'eyebrow' => null,
    'description' => null,
    'align' => 'center', // center | left
    'level' => 'h2',
])

{{--
    Section heading used across the landing page.

    `$title` and `$description` are echoed unescaped so marketing copy can carry
    inline <span class="gradient-text"> highlights. Only ever pass developer
    authored strings here — never user input.
--}}

@php
    $alignClass = $align === 'center'
        ? 'mx-auto max-w-2xl items-center text-center'
        : 'max-w-2xl items-start text-left';
@endphp

<div class="reveal flex flex-col {{ $alignClass }}">
    @if ($eyebrow)
        <span class="eyebrow mb-4">
            <span class="size-1.5 rounded-full bg-brand-500"></span>
            {{ $eyebrow }}
        </span>
    @endif

    <{{ $level }} class="heading-lg">{!! $title !!}</{{ $level }}>

    @if ($description)
        <p class="lede mt-4">{!! $description !!}</p>
    @endif
</div>
