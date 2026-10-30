<div>
    <section class="border-b border-line bg-canvas">
        <div class="container-page py-8">
            <nav class="text-sm text-muted" aria-label="Breadcrumb">
                <a href="{{ route('student.dashboard') }}" class="hover:text-forest-800">My research</a>
                <span class="mx-1.5" aria-hidden="true">/</span>
                <span class="text-ink">Register a proposal</span>
            </nav>
            <h1 class="mt-4 text-3xl font-bold tracking-tight">Register a research proposal</h1>
            <p class="mt-2 max-w-2xl text-ink-soft">Describe your topic and plan. Your supervisor is notified as soon as you submit; drafts stay private until you're ready.</p>
        </div>
    </section>

    <div class="container-page grid gap-10 py-10 lg:grid-cols-[1fr_20rem]">
        <form wire:submit="submit" class="space-y-8">
            {{-- 1. Focus --}}
            <fieldset class="panel p-6 sm:p-8">
                <legend class="sr-only">Study focus</legend>
                <p class="text-sm font-semibold text-forest-700">1 of 4</p>
                <h2 class="mt-1 text-lg font-semibold">Study focus</h2>

                <div class="mt-6 space-y-5">
                    <div>
                        <label for="title" class="field-label">Proposed title <span class="text-red-700">*</span></label>
                        <input type="text" id="title" wire:model.live.debounce.600ms="title" class="field" placeholder="e.g. Mobile money adoption among market traders in Kabwe" />
                        @error('title') <p class="field-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="category" class="field-label">Subject <span class="text-red-700">*</span></label>
                            <select id="category" wire:model="category" class="field">
                                <option value="">Select a subject</option>
                                @foreach ($schools as $school => $subjects)
                                    <optgroup label="{{ $school }}">
                                        @foreach ($subjects as $cat)
                                            <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('category') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="supervisor_id" class="field-label">Preferred supervisor <span class="font-normal text-muted">(optional)</span></label>
                            <select id="supervisor_id" wire:model="supervisor_id" class="field">
                                <option value="">Let the department allocate</option>
                                @foreach ($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}">{{ $supervisor->name }} · {{ $supervisor->department ?? 'Faculty' }}</option>
                                @endforeach
                            </select>
                            @error('supervisor_id') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- 2. Scope --}}
            <fieldset class="panel p-6 sm:p-8">
                <legend class="sr-only">Problem statement and methodology</legend>
                <p class="text-sm font-semibold text-forest-700">2 of 4</p>
                <h2 class="mt-1 text-lg font-semibold">Problem statement &amp; methodology</h2>

                <div class="mt-6 space-y-5">
                    <div>
                        <label for="abstract" class="field-label">Background and problem statement <span class="text-red-700">*</span></label>
                        <textarea id="abstract" wire:model="abstract" rows="6" class="field leading-relaxed" placeholder="What problem does this study address, why does it matter, and what does existing work leave open?"></textarea>
                        @error('abstract') <p class="field-error">{{ $message }}</p> @enderror
                        <p class="field-hint">At least 50 characters. This becomes the abstract shown in the archive once your study is completed.</p>
                    </div>
                    <div>
                        <label for="objectives_methodology" class="field-label">Objectives and methodology <span class="font-normal text-muted">(optional)</span></label>
                        <textarea id="objectives_methodology" wire:model="objectives_methodology" rows="4" class="field leading-relaxed" placeholder="Objectives, study design, sampling, data collection and analysis"></textarea>
                        @error('objectives_methodology') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </fieldset>

            {{-- 3. Gaps --}}
            <fieldset class="panel p-6 sm:p-8">
                <legend class="sr-only">Research gaps addressed</legend>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-forest-700">3 of 4</p>
                        <h2 class="mt-1 text-lg font-semibold">Research gaps addressed</h2>
                        <p class="mt-1 text-sm text-ink-soft">Which open questions will this study answer?</p>
                    </div>
                    <button type="button" wire:click="addGap" class="btn-ghost btn-sm shrink-0 text-forest-700"><x-glyph name="plus" class="h-4 w-4" /> Add gap</button>
                </div>

                <div class="mt-5 space-y-3">
                    @foreach ($research_gaps as $index => $gap)
                        <div wire:key="gap-input-{{ $index }}" class="flex items-center gap-2">
                            <label for="gap-{{ $index }}" class="sr-only">Research gap {{ $index + 1 }}</label>
                            <input id="gap-{{ $index }}" type="text" wire:model="research_gaps.{{ $index }}" class="field" placeholder="e.g. No baseline data on informal traders outside the central business district" />
                            @if (count($research_gaps) > 1)
                                <button type="button" wire:click="removeGap({{ $index }})" class="rounded-md p-2 text-subtle hover:bg-canvas hover:text-red-700" aria-label="Remove gap {{ $index + 1 }}">
                                    <x-glyph name="trash" class="h-4 w-4" />
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </fieldset>

            {{-- 4. Attachment --}}
            <fieldset class="panel p-6 sm:p-8">
                <legend class="sr-only">Proposal document</legend>
                <p class="text-sm font-semibold text-forest-700">4 of 4</p>
                <h2 class="mt-1 text-lg font-semibold">Proposal document <span class="text-base font-normal text-muted">(optional)</span></h2>
                <p class="mt-1 text-sm text-ink-soft">Stored privately. Only you, your supervisor and faculty can open it.</p>

                <div class="mt-5">
                    <input type="file" wire:model="full_document" accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="block w-full cursor-pointer text-sm text-ink-soft file:mr-4 file:rounded-md file:border-0 file:bg-forest-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-forest-800 hover:file:bg-forest-100" />
                    <div wire:loading wire:target="full_document" class="field-hint">Uploading…</div>
                    @error('full_document') <p class="field-error">{{ $message }}</p> @enderror
                    <p class="field-hint">PDF or Word (.docx), up to 20 MB.</p>
                </div>
            </fieldset>

            <div class="flex flex-col-reverse gap-3 border-t border-line pt-6 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('student.dashboard') }}" class="btn-ghost">Cancel</a>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <button type="button" wire:click="saveDraft" wire:loading.attr="disabled" class="btn-secondary">Save as draft</button>
                    <button type="submit" wire:click="$set('is_draft', false)" wire:loading.attr="disabled" class="btn-primary">
                        <span wire:loading.remove wire:target="submit, saveDraft">Register Research Proposal</span>
                        <span wire:loading wire:target="submit, saveDraft">Submitting…</span>
                    </button>
                </div>
            </div>
        </form>

        {{-- Duplication check --}}
        <aside class="lg:sticky lg:top-24 lg:self-start">
            <div class="panel">
                <div class="border-b border-line p-5">
                    <h2 class="font-semibold">Institutional Duplication Prevention &amp; Academic Gap Check</h2>
                    <p class="mt-1 text-sm text-ink-soft">Completed studies that share terms with your title appear here as you type.</p>
                </div>

                <div class="p-5" wire:loading.class="opacity-50" wire:target="title">
                    @if (mb_strlen(trim($title)) < 5)
                        <p class="text-sm text-muted">Start typing a title to check the archive.</p>
                    @elseif ($similarRecords->isEmpty())
                        <p class="flex items-start gap-2 text-sm text-forest-800"><x-glyph name="check-circle" class="mt-0.5 h-4 w-4 shrink-0" /> No closely related completed studies found.</p>
                    @else
                        <p class="text-sm font-medium text-ochre-700">{{ $similarRecords->count() }} related {{ str('study')->plural($similarRecords->count()) }} found. Make sure your angle is new.</p>
                        <ul class="mt-3 space-y-3">
                            @foreach ($similarRecords as $similar)
                                <li>
                                    <a href="{{ route('repository.show', $similar) }}" target="_blank" class="record-title block text-sm hover:text-forest-800 hover:underline">{{ $similar->title }}</a>
                                    <p class="mt-0.5 text-xs text-muted">{{ $similar->student?->name }} &middot; {{ $similar->completion_year ?? 'n.d.' }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="border-t border-line p-5">
                    <a href="{{ route('gaps') }}" target="_blank" class="inline-flex items-center gap-1 text-sm font-semibold text-forest-700 hover:underline">Explore research gaps <x-glyph name="arrow-top-right-on-square" class="h-4 w-4" /></a>
                </div>
            </div>
        </aside>
    </div>
</div>
