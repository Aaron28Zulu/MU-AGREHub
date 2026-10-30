<div>
    <div class="border-b border-line bg-canvas">
        <div class="container-page flex flex-wrap items-center justify-between gap-3 py-4 text-sm">
            <nav class="text-muted" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-forest-800">Home</a>
                <span class="mx-1.5" aria-hidden="true">/</span>
                <a href="{{ route('repository') }}" class="hover:text-forest-800">Search</a>
                <span class="mx-1.5" aria-hidden="true">/</span>
                <span class="text-ink">Detailed record</span>
            </nav>
            <a href="{{ url()->previous() !== url()->current() && str_contains(url()->previous(), '/repository') ? url()->previous() : route('repository') }}" class="inline-flex items-center gap-1 font-medium text-forest-700 hover:underline">
                <x-glyph name="arrow-left" class="h-4 w-4" /> Back to results
            </a>
        </div>
    </div>

    <div class="container-page grid grid-cols-1 gap-8 py-6 sm:py-10 lg:grid-cols-[1fr_18rem] lg:gap-10">
        <article class="min-w-0">
            <div class="flex flex-wrap items-center gap-3">
                <span class="tag bg-forest-50 text-forest-800">Research project</span>
                <span class="text-sm text-muted">{{ $record->category->label() }}</span>
                @unless ($record->status->isPubliclyVisible())
                    <x-status-badge :status="$record->status" />
                    <span class="text-xs text-muted">Visible to you because you are the author, supervisor or faculty.</span>
                @endunless
            </div>

            <h1 class="record-title mt-4 text-2xl break-words sm:text-4xl">{{ $record->title }}</h1>

            <p class="mt-4 text-base text-ink-soft">
                {{ $record->student?->name ?? 'Unknown author' }}
                @if ($record->student?->department)
                    <span class="text-muted">&middot; {{ $record->student->department }}</span>
                @endif
            </p>

            {{-- Record metadata, catalogue style --}}
            <dl class="mt-6 grid grid-cols-1 border-t sm:mt-8 border-line text-sm sm:grid-cols-[11rem_1fr]">
                <dt class="pt-3 font-medium text-muted sm:border-b sm:border-line sm:py-3">Title</dt>
                <dd class="border-b border-line pb-3 break-words sm:py-3">{{ $record->title }}</dd>

                <dt class="pt-3 font-medium text-muted sm:border-b sm:border-line sm:py-3">Author</dt>
                <dd class="border-b border-line pb-3 break-words sm:py-3">{{ $record->student?->name ?? 'Unknown author' }}</dd>

                <dt class="pt-3 font-medium text-muted sm:border-b sm:border-line sm:py-3">Supervisor</dt>
                <dd class="border-b border-line pb-3 break-words sm:py-3">{{ $record->supervisor?->name ?? 'Not recorded' }}@if ($record->supervisor?->department)<span class="text-muted"> &middot; {{ $record->supervisor->department }}</span>@endif</dd>

                <dt class="pt-3 font-medium text-muted sm:border-b sm:border-line sm:py-3">Subject</dt>
                <dd class="border-b border-line pb-3 break-words sm:py-3">
                    <a href="{{ route('repository', ['category' => $record->category->value]) }}" class="link">{{ $record->category->label() }}</a>
                </dd>

                <dt class="pt-3 font-medium text-muted sm:border-b sm:border-line sm:py-3">Completion year</dt>
                <dd class="border-b border-line pb-3 break-words sm:py-3">{{ $record->completion_year ?? 'In progress' }}</dd>

                <dt class="pt-3 font-medium text-muted sm:border-b sm:border-line sm:py-3">Document type</dt>
                <dd class="border-b border-line pb-3 break-words sm:py-3">Student research project</dd>

                <dt class="pt-3 font-medium text-muted sm:border-b sm:border-line sm:py-3">Access</dt>
                <dd class="border-b border-line pb-3 break-words sm:py-3">
                    @if ($canAccessFullText)
                        <span class="inline-flex items-center gap-1.5 text-forest-800"><x-glyph name="lock-open" class="h-4 w-4" /> Full text available to you</span>
                    @else
                        <span class="inline-flex items-center gap-1.5"><x-glyph name="lock-closed" class="h-4 w-4 text-muted" /> Abstract and research gaps only</span>
                    @endif
                </dd>
            </dl>

            <section class="mt-10" aria-labelledby="abstract-heading">
                <h2 id="abstract-heading" class="text-lg font-semibold">Abstract</h2>
                <div class="mt-3 max-w-3xl space-y-4 text-base leading-relaxed text-ink-soft">
                    @foreach (preg_split('/\n{2,}/', $record->plainAbstract()) as $paragraph)
                        <p>{!! nl2br(e($paragraph)) !!}</p>
                    @endforeach
                </div>
            </section>

            <section class="mt-10" aria-labelledby="gaps-heading">
                <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                    <h2 id="gaps-heading" class="text-lg font-semibold">Research gaps &amp; future directions</h2>
                    <a href="{{ route('gaps') }}" class="text-sm font-medium text-forest-700 hover:underline">All gaps</a>
                </div>
                @if ($record->researchGaps->isEmpty())
                    <p class="mt-3 text-sm text-muted">No research gaps have been recorded for this study yet.</p>
                @else
                    <ul class="mt-4 space-y-3">
                        @foreach ($record->researchGaps as $gap)
                            <li class="flex gap-3 rounded-lg border border-line p-4">
                                <x-glyph name="light-bulb" class="mt-0.5 h-5 w-5 shrink-0 text-ochre-500" />
                                <div class="min-w-0">
                                    <p class="text-sm leading-relaxed text-ink">{{ $gap->extracted_gap_text }}</p>
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                                        @if ($gap->domain)
                                            <a href="{{ route('gaps', ['domain' => $gap->domain->slug]) }}" class="text-muted hover:text-forest-800">{{ $gap->domain->name }}</a>
                                        @endif
                                        @if ($gap->is_saturated)
                                            <span class="tag bg-ochre-50 text-ochre-700">Well-studied area</span>
                                        @elseif ($gap->is_emerging)
                                            <span class="tag bg-forest-50 text-forest-800">Emerging</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            @if ($related->isNotEmpty())
                <section class="mt-12 border-t border-line pt-8" aria-labelledby="related-heading">
                    <h2 id="related-heading" class="text-lg font-semibold">Related studies in {{ $record->category->label() }}</h2>
                    <ul class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach ($related as $item)
                            <li>
                                <a href="{{ route('repository.show', $item) }}" class="group block rounded-lg border border-line p-4 transition hover:border-forest-300 hover:bg-forest-50/50">
                                    <p class="record-title text-base group-hover:text-forest-800">{{ $item->title }}</p>
                                    <p class="mt-1 text-xs text-muted">{{ $item->student?->name }} &middot; {{ $item->completion_year ?? 'n.d.' }}</p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </article>

        {{-- Record tools --}}
        <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start" x-data="{ copied: null, copy(text, key) { navigator.clipboard.writeText(text); this.copied = key; setTimeout(() => this.copied = null, 2000) } }">
            <div class="panel p-5">
                <h2 class="eyebrow">Full text</h2>
                @if ($canAccessFullText && $record->full_document_path)
                    <p class="mt-2 text-sm text-ink-soft">You have access to the manuscript for this study.</p>
                    <a href="{{ route('projects.download', $record) }}" class="btn-primary mt-4 w-full">
                        <x-glyph name="arrow-down-tray" class="h-4 w-4" /> Download manuscript
                    </a>
                @elseif ($canAccessFullText)
                    <p class="mt-2 text-sm text-ink-soft">No manuscript file has been uploaded yet.</p>
                @else
                    <div class="mt-3 flex gap-3 text-sm text-ink-soft">
                        <x-glyph name="lock-closed" class="mt-0.5 h-5 w-5 shrink-0 text-muted" />
                        <p>Manuscripts, methodology and data are available only to the author, their supervisor and faculty.</p>
                    </div>
                    @guest
                        <a href="{{ route('login') }}" class="btn-secondary mt-4 w-full">Sign in for access</a>
                    @endguest
                @endif
            </div>

            <div class="panel divide-y divide-line">
                <h2 class="eyebrow px-5 pt-5 pb-3">Tools</h2>
                <button type="button" wire:click="toggleSave({{ $record->id }})" class="flex w-full items-center gap-3 px-5 py-3 text-left text-sm font-medium text-ink-soft hover:bg-canvas hover:text-ink">
                    <x-glyph name="bookmark" :solid="$isSaved" :class="$isSaved ? 'h-5 w-5 text-forest-700' : 'h-5 w-5'" />
                    {{ $isSaved ? 'Saved to folder' : 'Add to folder' }}
                </button>
                <button type="button" @click="copy(@js($record->citation()), 'cite')" class="flex w-full items-center gap-3 px-5 py-3 text-left text-sm font-medium text-ink-soft hover:bg-canvas hover:text-ink">
                    <x-glyph name="chat-bubble-bottom-center-text" class="h-5 w-5" />
                    <span x-text="copied === 'cite' ? 'Citation copied' : 'Copy citation (APA)'">Copy citation (APA)</span>
                </button>
                <button type="button" @click="copy(@js(route('repository.show', $record)), 'link')" class="flex w-full items-center gap-3 px-5 py-3 text-left text-sm font-medium text-ink-soft hover:bg-canvas hover:text-ink">
                    <x-glyph name="link" class="h-5 w-5" />
                    <span x-text="copied === 'link' ? 'Link copied' : 'Copy permalink'">Copy permalink</span>
                </button>
                <button type="button" onclick="window.print()" class="flex w-full items-center gap-3 px-5 py-3 text-left text-sm font-medium text-ink-soft hover:bg-canvas hover:text-ink">
                    <x-glyph name="printer" class="h-5 w-5" /> Print record
                </button>
            </div>

            <div class="rounded-lg bg-canvas p-5 text-sm">
                <h2 class="eyebrow">Citation</h2>
                <p class="mt-2 leading-relaxed break-words text-ink-soft">{{ $record->citation() }}</p>
            </div>
        </aside>
    </div>
</div>
