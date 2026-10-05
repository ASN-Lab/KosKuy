@props([
    'href' => null,
    'variant' => 'dark', // dark | light
    'class' => 'size-9',
])

@php
    $tag = $href ? 'a' : 'span';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    class="grid {{ $class }} shrink-0 place-items-center rounded-2xl {{ $variant === 'light' ? 'bg-white/10 ring-1 ring-white/20' : 'bg-brand-gradient shadow-[0_8px_20px_-8px_rgb(79_70_229/0.8)]' }}"
>
    <img src="{{ asset('images/logo.svg') }}" alt="KosKuy" class="size-[68%]">
    @if ($href)
        <span class="sr-only">KosKuy</span>
    @endif
</{{ $tag }}>
