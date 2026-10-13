@props([
    'user',
    'size' => 'size-10',
    'text' => 'text-sm',
])

@php $url = $user->avatarUrl(); @endphp

@if ($url)
    <img
        src="{{ $url }}"
        alt="{{ $user->name }}"
        {{ $attributes->merge(['class' => "$size shrink-0 rounded-full object-cover ring-1 ring-ink-200"]) }}
    >
@else
    <span {{ $attributes->merge(['class' => "grid $size shrink-0 place-items-center rounded-full bg-brand-100 font-bold text-brand-700 $text"]) }}>
        {{ initials($user->name) }}
    </span>
@endif
