<div>
    <section class="border-b border-line bg-canvas">
        <div class="container-page py-6 sm:py-10">
            <nav class="text-sm text-muted" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-forest-800">Home</a>
                <span class="mx-1.5" aria-hidden="true">/</span>
                <span class="text-ink">Research gaps</span>
            </nav>
            <div class="mt-5 max-w-3xl">
                <h1 class="text-3xl font-bold tracking-tight">Research gaps index</h1>
                <p class="mt-3 text-base leading-relaxed text-ink-soft sm:text-lg">
                    Open questions and future directions drawn from completed studies, grouped by thematic domain. Emerging areas are under-explored; well-studied areas already have several projects behind them.
                </p>
            </div>
        </div>
    </section>

    <div class="container-page grid grid-cols-1 gap-6 py-6 sm:py-10 lg:grid-cols-[19rem_1fr] lg:gap-10">
        {{-- Domain coverage: collapsed behind a toggle on small screens so the gap list stays near the top --}}
        <aside x-data="{ domainsOpen: false }" aria-labelledby="domains-heading">
            <button type="button" @click="domainsOpen = !domainsOpen" class="flex w-full items-center justify-between rounded-md border border-line-strong px-4 py-2.5 text-sm font-semibold lg:hidden" :aria-expanded="domainsOpen">
                <span>Thematic domains @if ($activeDomain)<span class="font-normal text-muted">&middot; {{ $activeDomain->name }}</span>@endif</span>
                <x-glyph name="chevron-down" class="h-4 w-4 text-subtle transition" ::class="domainsOpen ? 'rotate-180' : ''" />
            </button>
            <div :class="domainsOpen ? 'block' : 'hidden lg:block'" class="mt-4 lg:mt-0">
                <h2 id="domains-heading" class="hidden text-base font-semibold lg:block">Thematic domains</h2>
                <p class="text-sm text-muted lg:mt-1">Bar length shows how many gaps each domain holds.</p>

                <ul class="mt-4 space-y-1">
                    @forelse ($domains as $item)
                        <li>
                            <button type="button" wire:click="selectDomain('{{ $item->slug }}')" @class([
                                'w-full rounded-md px-3 py-2.5 text-left transition',
                                'bg-forest-50 ring-1 ring-forest-200' => $domain === $item->slug,
                                'hover:bg-canvas' => $domain !== $item->slug,
                            ]) aria-pressed="{{ $domain === $item->slug ? 'true' : 'false' }}">
                                <span class="flex items-start justify-between gap-3 text-sm">
                                    <span class="font-medium leading-snug text-ink">{{ $item->name }}</span>
                                    <span class="text-xs text-muted tabular-nums">{{ $item->gaps_count }}</span>
                                </span>
                                <span class="mt-2 block h-1.5 overflow-hidden rounded-full bg-line" aria-hidden="true">
                                    <span class="block h-full rounded-full {{ $item->saturated_count > 0 ? 'bg-ochre-500' : 'bg-forest-500' }}" style="width: {{ max(6, round($item->gaps_count / $maxDomainCount * 100)) }}%"></span>
                                </span>
                            </button>
                        </li>
                    @empty
                        <li class="rounded-md bg-canvas p-4 text-sm text-muted">No domains have been indexed yet.</li>
                    @endforelse
                </ul>

                <div class="mt-6 flex flex-wrap gap-4 text-xs text-muted">
                    <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-forest-500"></span> Open for exploration</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-ochre-500"></span> Contains well-studied areas</span>
                </div>
            </div>
        </aside>

        <section class="min-w-0" aria-labelledby="gap-list-heading">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative min-w-0 flex-1">
                    <x-glyph name="search" class="pointer-events-none absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-subtle" />
                    <label for="gap-search" class="sr-only">Search research gaps</label>
                    <input id="gap-search" type="search" wire:model.live.debounce.400ms="search" placeholder="Search gaps, e.g. irrigation, adoption, storage" class="field pl-10" />
                </div>
                <div class="flex rounded-md border border-line-strong p-0.5 text-sm" role="group" aria-label="Signal">
                    @foreach (['all' => 'All', 'emerging' => 'Emerging', 'saturated' => 'Well-studied'] as $value => $label)
                        <button type="button" wire:click="$set('signal', '{{ $value }}')" @class(['flex-1 rounded px-3 py-1.5 font-medium sm:flex-none', 'bg-forest-700 text-white' => $signal === $value, 'text-ink-soft hover:text-ink' => $signal !== $value])>{{ $label }}</button>
                    @endforeach
                </div>
            </div>

            <div class="mt-5 flex flex-wrap items-center justify-between gap-2 border-b border-line pb-3">
                <h2 id="gap-list-heading" class="text-sm text-ink-soft">
                    <span class="font-semibold text-ink">{{ number_format($gaps->total()) }}</span> {{ str('gap')->plural($gaps->total()) }}
                    @if ($activeDomain)
                        in <span class="font-semibold text-ink">{{ $activeDomain->name }}</span>
                    @endif
                </h2>
                @if ($search !== '' || $domain !== '' || $signal !== 'all')
                    <button type="button" wire:click="resetFilters" class="text-sm font-medium text-forest-700 hover:underline">Clear filters</button>
                @endif
            </div>

            @if ($gaps->isEmpty())
                <div class="py-16 text-center">
                    <x-glyph name="light-bulb" class="mx-auto h-10 w-10 text-subtle" />
                    <h3 class="mt-4 text-base font-semibold">No gaps match these filters</h3>
                    <p class="mt-2 text-sm text-ink-soft">Try another domain or clear the search.</p>
                </div>
            @else
                <ul class="mt-2 divide-y divide-line">
                    @foreach ($gaps as $gap)
                        <li wire:key="gap-{{ $gap->id }}" class="py-5">
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="font-semibold tracking-wide text-ochre-700 uppercase">{{ $gap->domain?->name ?? 'General research' }}</span>
                                @if ($gap->is_saturated)
                                    <span class="tag bg-ochre-50 text-ochre-700">Well-studied</span>
                                @elseif ($gap->is_emerging)
                                    <span class="tag bg-forest-50 text-forest-800">Emerging</span>
                                @endif
                            </div>
                            <p class="mt-2 text-base leading-relaxed text-ink">{{ $gap->extracted_gap_text }}</p>
                            @if ($gap->project)
                                <p class="mt-2 text-sm text-muted">
                                    From
                                    <a href="{{ route('repository.show', $gap->project) }}" class="font-medium text-forest-700 hover:underline">{{ $gap->project->title }}</a>
                                    @if ($gap->project->completion_year)
                                        ({{ $gap->project->completion_year }})
                                    @endif
                                </p>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <div class="border-t border-line pt-6">
                    {{ $gaps->links() }}
                </div>
            @endif

            @auth
                @if (auth()->user()->isStudent())
                    <div class="mt-10 flex flex-col items-start gap-4 rounded-lg bg-forest-950 p-5 sm:p-6 text-forest-50 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-semibold text-white">Found a direction worth pursuing?</p>
                            <p class="mt-1 text-sm text-forest-200">Register a proposal and cite the gap that motivates your study.</p>
                        </div>
                        <a href="{{ route('student.proposal.create') }}" class="btn shrink-0 bg-white text-forest-900 hover:bg-forest-50">Register a proposal</a>
                    </div>
                @endif
            @endauth
        </section>
    </div>
</div>
