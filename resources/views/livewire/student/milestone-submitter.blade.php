<section class="panel" aria-labelledby="submit-heading">
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line px-5 py-4 sm:px-6">
        <div>
            <h2 id="submit-heading" class="font-semibold">Submit a milestone</h2>
            <p class="text-sm text-muted">Proposals, progress reports, chapters and final manuscripts.</p>
        </div>
        <span class="inline-flex items-center gap-1.5 text-xs text-muted"><x-glyph name="lock-closed" class="h-4 w-4" /> Stored privately</span>
    </div>

    <div class="p-5 sm:p-6">
        @if (session()->has('submission_success'))
            <div class="mb-5 flex gap-3 rounded-md bg-forest-50 p-4 text-sm text-forest-900" role="status">
                <x-glyph name="check-circle" class="h-5 w-5 shrink-0 text-forest-700" />
                <p>{{ session('submission_success') }}</p>
            </div>
        @endif

        @if ($isCompleted)
            <div class="text-center">
                <x-glyph name="check-badge" class="mx-auto h-8 w-8 text-forest-700" />
                <h3 class="mt-3 font-semibold">Research Milestone Pipeline Completed</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-ink-soft">Your final manuscript was approved, so uploads are closed.</p>
                <div class="mt-5 flex flex-wrap justify-center gap-3">
                    @if ($project->full_document_path)
                        <a href="{{ route('projects.download', $project) }}" class="btn-primary btn-sm"><x-glyph name="arrow-down-tray" class="h-4 w-4" /> Download final copy</a>
                    @endif
                    <a href="{{ route('repository.show', $project) }}" class="btn-secondary btn-sm">View in Public Archive</a>
                </div>
            </div>
        @elseif ($isUnderReview)
            <div class="flex gap-3 rounded-md border border-ochre-100 bg-ochre-50 p-4 sm:gap-4 sm:p-5">
                <x-glyph name="clock" class="h-6 w-6 shrink-0 text-ochre-700" />
                <div>
                    <h3 class="font-semibold text-ink">Manuscript Currently Under Supervisor Review</h3>
                    <p class="mt-1 text-sm leading-relaxed text-ink-soft">
                        <span class="font-medium text-ink">{{ $supervisor?->name ?? 'Your supervisor' }}</span> is reviewing your latest submission. To keep the assessment fair, active submissions are temporarily paused until supervisory feedback is recorded.
                    </p>
                    <a href="#feedback-timeline" class="mt-3 inline-block text-sm font-medium text-forest-700 hover:underline">See earlier feedback</a>
                </div>
            </div>
        @else
            @if ($hasRevisionsRequested && $latestEvaluation)
                <div class="mb-6 rounded-md border border-red-200 bg-red-50/70 p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="flex items-center gap-2 font-semibold text-red-900"><x-glyph name="exclamation-triangle" class="h-5 w-5" /> Corrections &amp; Revisions Requested</h3>
                        <span class="text-xs font-medium text-red-800">Latest rubric: {{ number_format((float) $latestEvaluation->rubric_score, 1) }} / 100</span>
                    </div>
                    <p class="mt-2 text-sm leading-relaxed whitespace-pre-line text-red-900/90">{{ $latestEvaluation->feedback ?: 'Your supervisor has requested revisions before approving this milestone.' }}</p>
                    @if ($latestEvaluation->document_markup_path)
                        <a href="{{ route('projects.download.marked-up', $project) }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-red-900 underline">
                            <x-glyph name="arrow-down-tray" class="h-4 w-4" /> Download annotated draft
                        </a>
                    @endif
                </div>
            @endif

            <form wire:submit="submit" class="space-y-5">
                <div>
                    <span class="field-label">Document <span class="text-red-700">*</span></span>
                    <label
                        for="document-upload"
                        x-data="{ dropping: false, progress: 0 }"
                        x-on:livewire-upload-start="progress = 1"
                        x-on:livewire-upload-finish="progress = 0"
                        x-on:livewire-upload-error="progress = 0"
                        x-on:livewire-upload-progress="progress = $event.detail.progress"
                        x-on:dragover.prevent="dropping = true"
                        x-on:dragleave.prevent="dropping = false"
                        x-on:drop="dropping = false"
                        :class="dropping ? 'border-forest-600 bg-forest-50' : 'border-line-strong hover:border-forest-400'"
                        class="relative flex cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed bg-paper px-4 py-8 sm:px-6 text-center transition"
                    >
                        <input type="file" wire:model="document" id="document-upload" class="absolute inset-0 h-full w-full cursor-pointer opacity-0" accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document" />
                        <x-glyph name="arrow-up-tray" class="h-7 w-7 text-subtle" />
                        @if ($document)
                            <p class="mt-3 max-w-full truncate text-sm font-semibold text-forest-800">{{ $document->getClientOriginalName() }}</p>
                            <p class="mt-0.5 text-xs text-muted">Choose another file to replace it</p>
                        @else
                            <p class="mt-3 text-sm"><span class="font-semibold text-forest-700">Choose a file</span> or drag it here</p>
                            <p class="mt-0.5 text-xs text-muted">PDF or Word (.docx), up to 10 MB</p>
                        @endif
                        <span x-cloak x-show="progress > 0" class="mt-4 block h-1 w-full max-w-xs overflow-hidden rounded-full bg-line">
                            <span class="block h-full bg-forest-600 transition-all" :style="`width: ${progress}%`"></span>
                        </span>
                    </label>
                    @error('document') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="submission_notes" class="field-label">Notes for your supervisor <span class="font-normal text-muted">(optional)</span></label>
                    <textarea wire:model="submission_notes" id="submission_notes" rows="3" class="field" placeholder="What changed since the last version, and how you responded to earlier comments"></textarea>
                    @error('submission_notes') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" wire:loading.attr="disabled" wire:target="submit, document" class="btn-primary w-full sm:w-auto">
                        <x-glyph name="paper-airplane" class="h-4 w-4" wire:loading.remove wire:target="submit, document" />
                        <x-glyph name="arrow-path" class="h-4 w-4 animate-spin" wire:loading wire:target="submit, document" />
                        <span wire:loading.remove wire:target="submit, document">{{ $hasRevisionsRequested ? 'Submit Revised Draft for Review' : 'Submit Manuscript for Supervisor Review' }}</span>
                        <span wire:loading wire:target="submit, document">Uploading…</span>
                    </button>
                </div>
            </form>
        @endif
    </div>
</section>
