@props(['color' => 'gray'])

@php
    // Maps the Filament colour tokens returned by the office enums onto the archive palette.
    $styles = match ($color) {
        'primary' => 'bg-sky-50 text-sky-800 ring-sky-200',
        'info' => 'bg-indigo-50 text-indigo-800 ring-indigo-200',
        'warning' => 'bg-ochre-50 text-ochre-700 ring-ochre-100',
        'success' => 'bg-forest-50 text-forest-800 ring-forest-200',
        'danger' => 'bg-red-50 text-red-800 ring-red-200',
        default => 'bg-stone-100 text-stone-700 ring-stone-200',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset', $styles]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70" aria-hidden="true"></span>
    {{ $slot }}
</span>
