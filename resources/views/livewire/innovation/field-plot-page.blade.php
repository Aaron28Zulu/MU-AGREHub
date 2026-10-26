<div>
    <section class="border-b border-line bg-canvas">
        <div class="container-page py-8">
            <nav class="text-sm text-muted" aria-label="Breadcrumb">
                <a href="{{ route('innovation') }}" class="hover:text-forest-800">Research &amp; Innovation</a>
                <span class="mx-1.5" aria-hidden="true">/</span>
                <a href="{{ route('innovation') }}#field" class="hover:text-forest-800">Crop production</a>
            </nav>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <x-pill :color="$plot->status->color()">{{ $plot->status->label() }}</x-pill>
                <span class="text-sm text-muted">{{ $plot->season }}</span>
            </div>
            <h1 class="mt-3 text-3xl font-bold tracking-tight">{{ $plot->name }}</h1>
            <p class="mt-2 text-ink-soft">{{ $plot->crop }}@if ($plot->variety) &middot; {{ $plot->variety }}@endif @if ($plot->location) &middot; {{ $plot->location }}@endif</p>
        </div>
    </section>

    <div class="container-page grid gap-10 py-10 lg:grid-cols-[1fr_20rem]">
        <div class="min-w-0 space-y-8">
            @if ($plot->objectives)
                <section>
                    <h2 class="text-lg font-semibold">Learning and research objectives</h2>
                    <div class="mt-3 leading-relaxed whitespace-pre-line text-ink-soft">{{ $plot->objectives }}</div>
                </section>
            @endif

            @if ($canRecord)
                <section>
                    <h2 class="text-lg font-semibold">Add a field record</h2>
                    <form wire:submit="record" class="panel mt-4 space-y-5 p-6">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="observed_on" class="field-label">Date <span class="text-red-700">*</span></label>
                                <input id="observed_on" type="date" wire:model="observed_on" max="{{ now()->toDateString() }}" class="field" />
                                @error('observed_on') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="activity" class="field-label">Activity <span class="text-red-700">*</span></label>
                                <select id="activity" wire:model="activity" class="field">
                                    <option value="">Select an activity</option>
                                    @foreach ($activities as $item)
                                        <option value="{{ $item->value }}">{{ $item->label() }}</option>
                                    @endforeach
                                </select>
                                @error('activity') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid gap-5 sm:grid-cols-[1fr_8rem_6rem]">
                            <div>
                                <label for="metric" class="field-label">Measurement <span class="font-normal text-muted">(optional)</span></label>
                                <input id="metric" type="text" wire:model="metric" class="field" placeholder="e.g. Plant height, Yield, Rainfall" />
                                @error('metric') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="value" class="field-label">Value</label>
                                <input id="value" type="number" step="0.01" min="0" wire:model="value" class="field" />
                                @error('value') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="unit" class="field-label">Unit</label>
                                <input id="unit" type="text" wire:model="unit" class="field" placeholder="cm" />
                                @error('unit') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label for="notes" class="field-label">Notes</label>
                            <textarea id="notes" wire:model="notes" rows="3" class="field leading-relaxed" placeholder="What did you do or observe? Pests, crop condition, weather, inputs used"></textarea>
                            @error('notes') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="btn-primary">Save record</button>
                        </div>
                    </form>
                </section>
            @elseif (! auth()->check() && $plot->status->acceptsObservations())
                <p class="panel p-5 text-sm text-ink-soft"><a href="{{ route('login') }}" class="link font-semibold">Sign in</a> with your student account to add field records.</p>
            @endif

            <section>
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <h2 class="text-lg font-semibold">Field log</h2>
                    @auth
                        <a href="{{ route('innovation.plot.export', $plot) }}" class="btn-secondary btn-sm"><x-glyph name="arrow-down-tray" class="h-4 w-4" /> Download CSV</a>
                    @endauth
                </div>

                @if ($observations->isEmpty())
                    <p class="panel mt-4 p-8 text-center text-sm text-muted">No field records yet.</p>
                @else
                    <div class="mt-4 overflow-x-auto rounded-lg border border-line">
                        <table class="min-w-full divide-y divide-line text-sm">
                            <thead class="bg-canvas text-left text-xs font-semibold text-muted">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Date</th>
                                    <th scope="col" class="px-4 py-3">Activity</th>
                                    <th scope="col" class="px-4 py-3">Measurement</th>
                                    <th scope="col" class="px-4 py-3">Notes</th>
                                    <th scope="col" class="px-4 py-3">Recorded by</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line bg-white">
                                @foreach ($observations as $observation)
                                    <tr wire:key="obs-{{ $observation->id }}" class="align-top">
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $observation->observed_on->format('j M Y') }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $observation->activity->label() }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if ($observation->metric)
                                                {{ $observation->metric }}: <span class="font-semibold">{{ rtrim(rtrim((string) $observation->value, '0'), '.') }}</span> {{ $observation->unit }}
                                            @else
                                                <span class="text-subtle">&mdash;</span>
                                            @endif
                                        </td>
                                        <td class="max-w-sm px-4 py-3 text-ink-soft">{{ $observation->notes }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-muted">{{ $observation->recorder?->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $observations->links() }}</div>
                @endif
            </section>
        </div>

        <aside>
            <dl class="panel divide-y divide-line text-sm">
                <div class="flex justify-between gap-4 p-4"><dt class="text-muted">Planted</dt><dd class="font-medium">{{ $plot->planted_on?->format('j M Y') ?? 'Not yet' }}</dd></div>
                <div class="flex justify-between gap-4 p-4"><dt class="text-muted">Expected harvest</dt><dd class="font-medium">{{ $plot->expected_harvest_on?->format('j M Y') ?? '—' }}</dd></div>
                @if ($plot->daysAfterPlanting() !== null)
                    <div class="flex justify-between gap-4 p-4"><dt class="text-muted">Days after planting</dt><dd class="font-medium">{{ $plot->daysAfterPlanting() }}</dd></div>
                @endif
                <div class="flex justify-between gap-4 p-4"><dt class="text-muted">Plot size</dt><dd class="font-medium">{{ $plot->area_m2 ? number_format((float) $plot->area_m2).' m²' : '—' }}</dd></div>
                <div class="flex justify-between gap-4 p-4"><dt class="text-muted">Field records</dt><dd class="font-medium">{{ $observations->total() }}</dd></div>
                <div class="flex justify-between gap-4 p-4"><dt class="text-muted">Student contributors</dt><dd class="font-medium">{{ $contributors }}</dd></div>
                @if ($plot->coordinator)
                    <div class="flex justify-between gap-4 p-4"><dt class="text-muted">Coordinator</dt><dd class="font-medium">{{ $plot->coordinator->name }}</dd></div>
                @endif
                @if ($plot->project && $plot->project->status->isPubliclyVisible())
                    <div class="p-4"><dt class="text-muted">Linked study</dt><dd class="mt-1"><a href="{{ route('repository.show', $plot->project) }}" class="link">{{ $plot->project->title }}</a></dd></div>
                @endif
            </dl>
        </aside>
    </div>
</div>
