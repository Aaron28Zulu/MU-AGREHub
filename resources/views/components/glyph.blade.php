@props(['name', 'solid' => false])

@php
    $aliases = ['menu' => 'bars-3', 'x' => 'x-mark', 'search' => 'magnifying-glass'];
    $component = ($solid ? 'heroicon-s-' : 'heroicon-o-').($aliases[$name] ?? $name);
@endphp

<x-dynamic-component :component="$component" {{ $attributes->merge(['aria-hidden' => 'true']) }} />
