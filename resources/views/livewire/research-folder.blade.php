<div class="container-page py-10">
    <nav class="text-sm text-muted" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-forest-800">Home</a>
        <span class="mx-1.5" aria-hidden="true">/</span>
        <span class="text-ink">Folder</span>
    </nav>

    <div class="mt-5 flex flex-wrap items-end justify-between gap-4 border-b border-line pb-5">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">My folder</h1>
            <p class="mt-2 text-ink-soft">Records you saved while searching. Use them for your literature review or to compare against your own topic.</p>
        </div>
        <a href="{{ route('repository') }}" class="btn-secondary btn-sm"><x-glyph name="search" class="h-4 w-4" /> Find more records</a>
    </div>

    @if ($records->isEmpty())
        <div class="py-20 text-center">
            <x-glyph name="bookmark" class="mx-auto h-10 w-10 text-subtle" />
            <h2 class="mt-4 text-base font-semibold">Your folder is empty</h2>
            <p class="mt-2 text-sm text-ink-soft">Select the bookmark on any search result or record to keep it here.</p>
        </div>
    @else
        <ol class="divide-y divide-line">
            @foreach ($records as $record)
                <li wire:key="saved-{{ $record->id }}" class="flex gap-4 py-6">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-muted">{{ $record->category->label() }} &middot; {{ $record->completion_year ?? 'n.d.' }}</p>
                        <h2 class="record-title mt-1 text-lg">
                            <a href="{{ route('repository.show', $record) }}" class="hover:text-forest-800 hover:underline">{{ $record->title }}</a>
                        </h2>
                        <p class="mt-1 text-sm text-ink-soft">{{ $record->student?->name }}@if ($record->supervisor) <span class="text-muted">&middot; Supervisor: {{ $record->supervisor->name }}</span>@endif</p>
                        <p class="mt-1 text-xs text-muted">Saved {{ $record->pivot->created_at?->diffForHumans() }}</p>
                    </div>
                    <button type="button" wire:click="toggleSave({{ $record->id }})" class="btn-ghost btn-sm shrink-0 self-start text-ink-soft hover:text-red-700">
                        <x-glyph name="trash" class="h-4 w-4" /> Remove
                    </button>
                </li>
            @endforeach
        </ol>
    @endif
</div>
