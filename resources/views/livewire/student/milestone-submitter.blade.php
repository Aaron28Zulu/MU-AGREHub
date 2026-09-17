<div class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-5 border-b border-[#f0eee6] dark:border-stone-800 gap-2">
        <div>
            <h3 class="text-sm font-bold text-[#181a18] dark:text-white">Milestone Submission Action Zone</h3>
            <p class="text-xs text-[#737971] dark:text-stone-400 mt-0.5">Submit draft manuscripts, chapter revisions, or final defense documents.</p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[11px] font-medium bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950/60 dark:text-emerald-300 border border-[#c5dacf]/60">
            <svg class="w-3 h-3 text-[#1a3529] dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <span>Institutional Research Vault</span>
        </span>
    </div>

    @if (session()->has('submission_success'))
        <div class="mb-5 p-3.5 rounded bg-[#f2f7f4] border border-[#c5dacf] text-[#1a3529] dark:bg-emerald-950/40 dark:border-emerald-800 dark:text-emerald-300 text-xs flex items-start gap-2.5">
            <svg class="w-4 h-4 text-[#1a3529] dark:text-emerald-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <strong>Manuscript Dispatched:</strong> {{ session('submission_success') }}
            </div>
        </div>
    @endif

    @if ($isCompleted)
        <!-- Completed & Defended State -->
        <div class="p-6 rounded-lg bg-[#fbfaf7] dark:bg-[#121412] border border-[#c5dacf] dark:border-emerald-900 text-center">
            <div class="w-10 h-10 rounded-full bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950 dark:text-emerald-400 mx-auto flex items-center justify-center mb-3 border border-[#c5dacf]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
            <h4 class="text-sm font-bold text-[#181a18] dark:text-white">Research Milestone Pipeline Completed</h4>
            <p class="text-xs text-[#575c56] dark:text-stone-400 mt-1 max-w-md mx-auto">
                Your thesis has passed formal supervisor evaluation and electronic rubric grading. Active submission upload is closed.
            </p>
            <div class="mt-4 flex items-center justify-center gap-3">
                <a
                    href="{{ route('projects.download', $project) }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded text-white bg-[#1a3529] hover:bg-[#224233] transition"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Download Final Archival Copy</span>
                </a>
                <a
                    href="{{ route('repository', ['search' => $project->title]) }}"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-medium rounded text-[#1c1e21] dark:text-stone-200 bg-white dark:bg-stone-800 border border-[#e6e3da] dark:border-stone-700 hover:bg-stone-50 transition"
                >
                    <span>View in Public Archive &rarr;</span>
                </a>
            </div>
        </div>

    @elseif ($isUnderReview)
        <!-- Under Review Locked State -->
        <div class="p-6 rounded-lg bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/40">
            <div class="flex items-start gap-3.5">
                <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 flex items-center justify-center shrink-0 border border-amber-200">
                    <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-amber-900 dark:text-amber-200">
                            Manuscript Currently Under Supervisor Review
                        </h4>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300">
                            Evaluation Pending
                        </span>
                    </div>
                    <p class="text-xs text-amber-800/90 dark:text-amber-300/80 mt-1 leading-relaxed">
                        Your latest manuscript submission is currently being reviewed by <strong class="font-semibold text-amber-950 dark:text-amber-100">{{ $supervisor?->name ?? 'your designated faculty supervisor' }}</strong>.
                        To preserve evaluation integrity, active submissions are temporarily paused until supervisory feedback and rubric assessment are recorded.
                    </p>
                    <div class="mt-3.5 pt-3 border-t border-amber-200/60 dark:border-amber-900/40 flex flex-wrap items-center gap-4 text-xs text-amber-900/80 dark:text-amber-300">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-700 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Notification sent to: <span class="font-mono">{{ $supervisor?->email ?? 'Supervisor Desk' }}</span>
                        </span>
                        <span>&bull;</span>
                        <a href="#feedback-timeline" class="text-amber-900 dark:text-amber-200 font-semibold underline hover:text-amber-950">
                            Jump to Historical Feedback &darr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Active Submission State (Draft or Revisions Requested) -->
        @if ($hasRevisionsRequested && $latestEvaluation)
            <!-- Explicit Revisions Alert Banner -->
            <div class="mb-5 p-4 rounded-lg bg-rose-50/80 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/50">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-300 flex items-center justify-center shrink-0 border border-rose-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-rose-900 dark:text-rose-200">
                                Corrections &amp; Revisions Requested
                            </h4>
                            <span class="text-[11px] font-mono font-bold text-rose-700 dark:text-rose-400">
                                Latest Rubric: {{ number_format((float) $latestEvaluation->rubric_score, 1) }}%
                            </span>
                        </div>
                        <p class="text-xs text-rose-800 dark:text-rose-300/90 mt-1 leading-relaxed">
                            {{ $latestEvaluation->feedback ?: 'Your supervisor has requested revisions on your latest manuscript before milestone approval.' }}
                        </p>
                        @if ($latestEvaluation->document_markup_path)
                            <div class="mt-2.5">
                                <a
                                    href="{{ route('projects.download.marked-up', $project) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded text-rose-900 bg-rose-100 hover:bg-rose-200 border border-rose-300 dark:bg-rose-900/60 dark:text-rose-200 dark:border-rose-800 transition"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    <span>Download Supervisor Annotated Markup Draft</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit="submit" class="space-y-5">
            <!-- File Upload Drag/Drop Area -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#737971] dark:text-stone-400 mb-1.5">
                    Manuscript Document (.PDF or .DOCX) <span class="text-red-600">*</span>
                </label>
                <div
                    x-data="{ isDropping: false, progress: 0 }"
                    x-on:livewire-upload-start="progress = 0"
                    x-on:livewire-upload-finish="progress = 100"
                    x-on:livewire-upload-error="progress = 0"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                    class="relative border border-dashed rounded-lg p-6 text-center transition bg-[#faf8f5] dark:bg-[#121412]"
                    :class="isDropping ? 'border-[#1a3529] bg-[#f2f7f4] dark:bg-emerald-950/20' : 'border-[#e6e3da] dark:border-stone-700 hover:border-[#1a3529]/60'"
                    x-on:dragover.prevent="isDropping = true"
                    x-on:dragleave.prevent="isDropping = false"
                    x-on:drop.prevent="isDropping = false"
                >
                    <input
                        type="file"
                        wire:model="document"
                        id="document-upload"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                    />

                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-8 h-8 text-[#737971] dark:text-stone-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>

                        @if ($document)
                            <div class="text-xs font-semibold text-[#1a3529] dark:text-emerald-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span>Ready to attach: {{ $document->getClientOriginalName() }}</span>
                            </div>
                            <p class="text-[11px] text-[#737971] mt-0.5">Click or drag another file to replace</p>
                        @else
                            <p class="text-xs font-medium text-[#1c1e21] dark:text-stone-200">
                                <span class="text-[#1a3529] dark:text-emerald-400 underline">Select manuscript file</span> or drag and drop document here
                            </p>
                            <p class="text-[11px] text-[#737971] dark:text-stone-400 mt-1">
                                Accepted: Strict PDF or Microsoft Word Document (.docx) &bull; Max 10MB
                            </p>
                        @endif
                    </div>

                    <!-- Upload Progress Bar -->
                    <div x-show="progress > 0 && progress < 100" class="mt-3 w-full bg-stone-200 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-[#1a3529] h-1.5 transition-all duration-200" :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>

                @error('document')
                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Submission Notes Text Area -->
            <div>
                <label for="submission_notes" class="block text-xs font-bold uppercase tracking-wider text-[#737971] dark:text-stone-400 mb-1.5">
                    Submission Notes &amp; Revision Log <span class="text-xs normal-case text-stone-400 font-normal">(Optional)</span>
                </label>
                <textarea
                    wire:model="submission_notes"
                    id="submission_notes"
                    rows="3"
                    class="w-full rounded border border-[#e6e3da] dark:border-stone-700 dark:bg-[#121412] dark:text-white text-xs sm:text-sm p-3 focus:outline-none focus:border-[#1a3529] focus:ring-1 focus:ring-[#1a3529] transition placeholder-[#8c928a]"
                    placeholder="Describe specific chapter revisions, experimental updates, or responses to supervisor comments..."
                ></textarea>
                @error('submission_notes')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Button with Loading State -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded text-xs font-semibold text-[#faf8f5] bg-[#1a3529] hover:bg-[#224233] disabled:opacity-50 disabled:cursor-not-allowed transition"
                >
                    <svg wire:loading.remove wire:target="submit, document" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>

                    <svg wire:loading wire:target="submit, document" class="w-3.5 h-3.5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>

                    <span wire:loading.remove wire:target="submit, document">
                        {{ $hasRevisionsRequested ? 'Submit Revised Draft for Review' : 'Submit Manuscript for Supervisor Review' }}
                    </span>
                    <span wire:loading wire:target="submit, document">Encrypting &amp; Uploading...</span>
                </button>
            </div>
        </form>
    @endif
</div>
