<div x-data="{ filtersOpen: false }">
    {{-- Search band --}}
    <section class="border-b border-line bg-canvas">
        <div class="container-page py-8">
            <nav class="text-sm text-muted" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-forest-800">Home</a>
                <span class="mx-1.5" aria-hidden="true">/</span>
                <span class="text-ink">Search</span>
            </nav>

            <p class="eyebrow mt-5">Institutional Research Archive</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight">Search the archive</h1>

            <form wire:submit.prevent class="mt-5" role="search">
                <div class="flex flex-col gap-2 rounded-xl border border-line-strong bg-white p-2 shadow-sm sm:flex-row sm:items-center">
                    <select wire:model.live="field" aria-label="Search field" class="rounded-lg border-0 bg-canvas py-2.5 pr-9 pl-3 text-sm font-medium text-ink-soft focus:ring-2 focus:ring-forest-600/20">
                        @foreach ($searchFields as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="relative flex-1">
                        <x-glyph name="search" class="pointer-events-none absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-subtle" />
                        <label for="archive-search" class="sr-only">Search terms</label>
                        <input
                            id="archive-search"
                            type="search"
                            wire:model.live.debounce.400ms="search"
                            placeholder="Search titles, abstracts, authors, supervisors and research gaps"
                            class="w-full rounded-lg border-0 py-2.5 pr-3 pl-10 text-base text-ink placeholder:text-subtle focus:ring-0 focus:outline-none"
                            autocomplete="off"
                        />
                    </div>
                    <button type="submit" class="btn-primary rounded-lg px-6">Search</button>
                </div>
            </form>
        </div>
    </section>

    <div class="container-page grid gap-8 py-8 lg:grid-cols-[17rem_1fr]">
        {{-- Refine results --}}
        <aside :class="filtersOpen ? 'block' : 'hidden lg:block'" aria-label="Refine results">
            <div class="flex items-center justify-between border-b border-line pb-3">
                <h2 class="text-base font-semibold">Refine results</h2>
                @if ($search !== '' || ! empty($selectedCategories) || ! empty($selectedYears) || $onlyWithGaps)
                    <button type="button" wire:click="resetFilters" class="text-sm font-medium text-forest-700 hover:underline">Clear all</button>
                @endif
            </div>
            <p class="sr-only">Faceted Filter Index</p>

            <div x-data="{ open: true }" class="border-b border-line">
                <button type="button" @click="open = !open" class="facet-heading" :aria-expanded="open">
                    Subject
                    <x-glyph name="chevron-down" class="h-4 w-4 text-subtle transition" ::class="open ? '' : '-rotate-90'" />
                </button>
                <div x-show="open" class="space-y-4 pb-4">
                    @foreach ($schools as $school => $subjects)
                        <fieldset class="space-y-2.5">
                            <legend class="mb-2 text-xs font-semibold tracking-wide text-muted uppercase">{{ $school }}</legend>
                            @foreach ($subjects as $category)
                                <label class="flex cursor-pointer items-start gap-2.5 text-sm text-ink-soft hover:text-ink">
                                    <input type="checkbox" value="{{ $category->value }}" wire:model.live="selectedCategories" class="mt-0.5 h-4 w-4 rounded border-line-strong text-forest-700 focus:ring-forest-600" />
                                    <span class="flex-1 leading-snug">{{ $category->label() }}</span>
                                    <span class="text-xs text-subtle tabular-nums">{{ $categoryCounts[$category->value] ?? 0 }}</span>
                                </label>
                            @endforeach
                        </fieldset>
                    @endforeach
                </div>
            </div>

            <div x-data="{ open: true }" class="border-b border-line">
                <button type="button" @click="open = !open" class="facet-heading" :aria-expanded="open">
                    Completion year
                    <x-glyph name="chevron-down" class="h-4 w-4 text-subtle transition" ::class="open ? '' : '-rotate-90'" />
                </button>
                <div x-show="open" class="grid grid-cols-2 gap-2.5 pb-4">
                    @foreach ($availableYears as $year)
                        <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-soft hover:text-ink">
                            <input type="checkbox" value="{{ $year }}" wire:model.live="selectedYears" class="h-4 w-4 rounded border-line-strong text-forest-700 focus:ring-forest-600" />
                            {{ $year }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div x-data="{ open: true }" class="border-b border-line">
                <button type="button" @click="open = !open" class="facet-heading" :aria-expanded="open">
                    Research signals
                    <x-glyph name="chevron-down" class="h-4 w-4 text-subtle transition" ::class="open ? '' : '-rotate-90'" />
                </button>
                <div x-show="open" class="pb-4">
                    <label class="flex cursor-pointer items-start gap-2.5 text-sm text-ink-soft hover:text-ink">
                        <input type="checkbox" wire:model.live="onlyWithGaps" class="mt-0.5 h-4 w-4 rounded border-line-strong text-forest-700 focus:ring-forest-600" />
                        <span class="leading-snug">Has identified research gaps</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 rounded-lg bg-forest-50 p-4 text-sm text-forest-900">
                <p class="font-semibold">Looking for a topic?</p>
                <p class="mt-1 leading-relaxed">The <a href="{{ route('gaps') }}" class="font-medium underline">research gaps index</a> lists open questions left by completed studies.</p>
            </div>
        </aside>

        {{-- Results --}}
        <section aria-labelledby="results-heading">
            <div class="flex flex-col gap-4 border-b border-line pb-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 id="results-heading" class="text-sm text-ink-soft">
                        @if ($projects->total() > 0)
                            Showing <span class="font-semibold text-ink">{{ $projects->firstItem() }}–{{ $projects->lastItem() }}</span> of <span class="font-semibold text-ink">{{ number_format($projects->total()) }}</span> results
                        @else
                            <span class="font-semibold text-ink">0</span> results
                        @endif
                        @if ($search !== '')
                            for <span class="font-semibold text-ink">“{{ $search }}”</span>
                        @endif
                        <span class="text-subtle">in {{ number_format($totalCatalogCount) }} catalogued studies</span>
                    </h2>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="filtersOpen = !filtersOpen" class="btn-secondary btn-sm lg:hidden">
                        <x-glyph name="funnel" class="h-4 w-4" /> Filters
                    </button>
                    <label for="sort" class="sr-only">Sort results</label>
                    <select id="sort" wire:model.live="sort" class="rounded-md border border-line-strong bg-white py-1.5 pr-8 pl-3 text-sm text-ink-soft focus:border-forest-600 focus:ring-forest-600/20">
                        <option value="latest">Newest first</option>
                        <option value="oldest">Oldest first</option>
                        <option value="title_asc">Title A–Z</option>
                    </select>
                    <div class="flex rounded-md border border-line-strong p-0.5" role="group" aria-label="Result display">
                        <button type="button" wire:click="setViewMode('grid')" @class(['rounded px-2.5 py-1 text-xs font-medium', 'bg-forest-700 text-white' => $viewMode === 'grid', 'text-ink-soft hover:text-ink' => $viewMode !== 'grid']) aria-pressed="{{ $viewMode === 'grid' ? 'true' : 'false' }}">Detailed</button>
                        <button type="button" wire:click="setViewMode('list')" @class(['rounded px-2.5 py-1 text-xs font-medium', 'bg-forest-700 text-white' => $viewMode === 'list', 'text-ink-soft hover:text-ink' => $viewMode !== 'list']) aria-pressed="{{ $viewMode === 'list' ? 'true' : 'false' }}">Brief</button>
                    </div>
                </div>
            </div>

            {{-- Active filters --}}
            @if (! empty($selectedCategories) || ! empty($selectedYears) || $onlyWithGaps || $field !== 'all')
                <div class="flex flex-wrap items-center gap-2 border-b border-line py-3">
                    <span class="text-xs font-medium text-muted">Active filters:</span>
                    @if ($field !== 'all')
                        <span class="chip">Field: {{ $searchFields[$field] ?? $field }}
                            <button type="button" wire:click="$set('field', 'all')" class="text-subtle hover:text-red-700" aria-label="Remove field filter"><x-glyph name="x" class="h-3.5 w-3.5" /></button>
                        </span>
                    @endif
                    @foreach ($selectedCategories as $selected)
                        <span class="chip">{{ \App\Enums\ResearchCategory::tryFrom($selected)?->label() ?? $selected }}
                            <button type="button" wire:click="removeCategory('{{ $selected }}')" class="text-subtle hover:text-red-700" aria-label="Remove subject filter"><x-glyph name="x" class="h-3.5 w-3.5" /></button>
                        </span>
                    @endforeach
                    @foreach ($selectedYears as $selectedYear)
                        <span class="chip">{{ $selectedYear }}
                            <button type="button" wire:click="removeYear('{{ $selectedYear }}')" class="text-subtle hover:text-red-700" aria-label="Remove year filter"><x-glyph name="x" class="h-3.5 w-3.5" /></button>
                        </span>
                    @endforeach
                    @if ($onlyWithGaps)
                        <span class="chip">Has research gaps
                            <button type="button" wire:click="toggleOnlyWithGaps" class="text-subtle hover:text-red-700" aria-label="Remove research gap filter"><x-glyph name="x" class="h-3.5 w-3.5" /></button>
                        </span>
                    @endif
                </div>
            @endif

            {{-- Loading placeholder --}}
            <div wire:loading.delay.flex class="hidden flex-col divide-y divide-line" aria-hidden="true">
                @foreach (range(1, 3) as $placeholder)
                    <div class="animate-pulse space-y-3 py-6">
                        <div class="h-3 w-40 rounded bg-canvas"></div>
                        <div class="h-5 w-3/4 rounded bg-canvas"></div>
                        <div class="h-3 w-1/2 rounded bg-canvas"></div>
                        <div class="h-3 w-full rounded bg-canvas"></div>
                    </div>
                @endforeach
            </div>

            <div wire:loading.delay.remove>
                @if ($projects->isEmpty())
                    <div class="py-16 text-center">
                        <x-glyph name="document-magnifying-glass" class="mx-auto h-10 w-10 text-subtle" />
                        <h3 class="mt-4 text-base font-semibold">No matching studies found</h3>
                        <p class="mx-auto mt-2 max-w-md text-sm text-ink-soft">Try a broader term, search all fields, or remove a filter. If nothing exists on your topic, that may be a gap worth studying.</p>
                        <div class="mt-6 flex justify-center gap-3">
                            <button type="button" wire:click="resetFilters" class="btn-secondary btn-sm">Clear search &amp; filters</button>
                            <a href="{{ route('gaps') }}" class="btn-ghost btn-sm">Browse research gaps</a>
                        </div>
                    </div>
                @else
                    <ol class="divide-y divide-line" start="{{ $projects->firstItem() }}">
                        @foreach ($projects as $project)
                            <li wire:key="record-{{ $project->id }}" class="flex gap-4 py-6">
                                <span class="w-6 shrink-0 pt-0.5 text-right text-sm text-subtle tabular-nums">{{ $projects->firstItem() + $loop->index }}.</span>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-muted">
                                        <span class="inline-flex items-center gap-1 font-medium text-ink-soft"><x-glyph name="document-text" class="h-4 w-4" /> Research project</span>
                                        <span aria-hidden="true">&middot;</span>
                                        <span>{{ $project->category->label() }}</span>
                                    </div>

                                    <h3 class="record-title mt-1.5 text-lg">
                                        <a href="{{ route('repository.show', $project) }}" class="hover:text-forest-800 hover:underline">{{ $project->title }}</a>
                                    </h3>

                                    <p class="mt-1 text-sm text-ink-soft">
                                        <span class="text-muted">By</span> {{ $project->student?->name ?? 'Unknown author' }}
                                        @if ($project->supervisor)
                                            <span class="text-muted">&middot; Supervisor:</span> {{ $project->supervisor->name }}
                                        @endif
                                        <span class="text-muted">&middot; {{ $project->completion_year ?? 'n.d.' }}</span>
                                    </p>

                                    @if ($viewMode === 'grid')
                                        <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-ink-soft">{{ $project->plainAbstract() }}</p>

                                        @if ($project->researchGaps->isNotEmpty())
                                            <div class="mt-3 rounded-md border-l-2 border-ochre-500 bg-ochre-50/60 px-3 py-2 text-sm text-ink-soft">
                                                <span class="font-medium text-ochre-700">Research gap:</span>
                                                {{ $project->researchGaps->first()->extracted_gap_text }}
                                                @if ($project->researchGaps->count() > 1)
                                                    <span class="text-muted">(+{{ $project->researchGaps->count() - 1 }} more)</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endif

                                    <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">
                                        <a href="{{ route('repository.show', $project) }}" class="font-semibold text-forest-700 hover:underline">Detailed record</a>
                                        <span class="inline-flex items-center gap-1 text-muted"><x-glyph name="lock-closed" class="h-4 w-4" /> Full text restricted</span>
                                        @if ($project->researchGaps->isNotEmpty())
                                            <span class="inline-flex items-center gap-1 text-muted"><x-glyph name="light-bulb" class="h-4 w-4" /> {{ $project->researchGaps->count() }} {{ str('gap')->plural($project->researchGaps->count()) }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="shrink-0">
                                    @php $saved = in_array($project->id, $savedIds, true); @endphp
                                    <button type="button" wire:click="toggleSave({{ $project->id }})" @class(['rounded-md p-2 transition', 'text-forest-700 hover:bg-forest-50' => $saved, 'text-subtle hover:bg-canvas hover:text-ink' => ! $saved]) title="{{ $saved ? 'Remove from folder' : 'Save to folder' }}" aria-pressed="{{ $saved ? 'true' : 'false' }}">
                                        <x-glyph name="bookmark" :solid="$saved" class="h-5 w-5" />
                                        <span class="sr-only">{{ $saved ? 'Remove from folder' : 'Save to folder' }}</span>
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ol>

                    <div class="border-t border-line pt-6">
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>
