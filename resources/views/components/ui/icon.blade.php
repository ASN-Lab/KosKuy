@props([
    'name' => 'sparkles',
    'class' => 'size-5',
])

{!! \App\Support\Icons::svg($name, $class, (string) $attributes) !!}
