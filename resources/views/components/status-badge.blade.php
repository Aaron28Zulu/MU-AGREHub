@props(['status'])

@php
    $styles = match ($status) {
        \App\Enums\ProjectStatus::DRAFT => 'bg-stone-100 text-stone-700 ring-stone-200',
        \App\Enums\ProjectStatus::PROPOSAL_SUBMITTED => 'bg-sky-50 text-sky-800 ring-sky-200',
        \App\Enums\ProjectStatus::UNDER_SUPERVISION => 'bg-ochre-50 text-ochre-700 ring-ochre-100',
        \App\Enums\ProjectStatus::UNDER_REVIEW => 'bg-indigo-50 text-indigo-800 ring-indigo-200',
        \App\Enums\ProjectStatus::COMPLETED => 'bg-forest-50 text-forest-800 ring-forest-200',
        \App\Enums\ProjectStatus::REJECTED => 'bg-red-50 text-red-800 ring-red-200',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset', $styles]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70" aria-hidden="true"></span>
    {{ $status->label() }}
</span>
