@php
    $record = $getRecord() ?? $record ?? null;
    $report = $record?->similarityReport ?? $record?->latestSimilarityReport;
    $rawStatus = $record?->similarity_check_status;
    $status = $rawStatus instanceof \App\Enums\SimilarityCheckStatus
        ? $rawStatus
        : ($rawStatus ? \App\Enums\SimilarityCheckStatus::tryFrom((string) $rawStatus) : null);

    // If report exists with score, default to completed unless actively processing or failed
    if (! $status) {
        $status = ($report && $report->similarity_score !== null)
            ? \App\Enums\SimilarityCheckStatus::COMPLETED
            : \App\Enums\SimilarityCheckStatus::UNTESTED;
    }

    $isProcessing = $status === \App\Enums\SimilarityCheckStatus::PROCESSING;
    $isUntested = $status === \App\Enums\SimilarityCheckStatus::UNTESTED;
    $isFailed = $status === \App\Enums\SimilarityCheckStatus::FAILED;
    $isCompleted = $status === \App\Enums\SimilarityCheckStatus::COMPLETED || ($report && $report->similarity_score !== null && ! $isProcessing && ! $isFailed && ! $isUntested);
@endphp

<div class="w-full space-y-4" @if ($isProcessing) wire:poll.3s @endif>
    @if ($isUntested)
        {{-- State 1: Untested --}}
        <div class="rounded-xl border border-zinc-200/90 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-900/40 p-5 shadow-xs transition-all">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-zinc-200/80 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zm3.75 11.625a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                AI Similarity & Plagiarism Inspection
                            </h4>
                            <span class="inline-flex items-center rounded-full bg-zinc-200/70 dark:bg-zinc-800 px-2 py-0.5 text-[10px] font-medium text-zinc-700 dark:text-zinc-300">
                                Untested
                            </span>
                        </div>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            This manuscript has not yet been scanned for academic overlap. Initiate an on-demand check to cross-reference with institutional dissertations and published sources.
                        </p>
                    </div>
                </div>

                @if (isset($getAction) && $action = $getAction('runSimilarityCheck'))
                    <div class="shrink-0 sm:self-center">
                        {{ $action }}
                    </div>
                @endif
            </div>
        </div>

    @elseif ($isProcessing)
        {{-- State 2: Processing / In Progress --}}
        <div class="rounded-xl border border-blue-200/80 dark:border-blue-900/60 bg-blue-50/50 dark:bg-blue-950/30 p-5 shadow-xs transition-all">
            <div class="flex items-start gap-3.5">
                <div class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-xl bg-blue-400 opacity-20"></span>
                    <svg class="h-5 w-5 animate-spin text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                            AI Similarity & Plagiarism Inspection
                        </h4>
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-medium text-blue-800 dark:bg-blue-950/80 dark:text-blue-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                            Processing
                        </span>
                    </div>
                    <p class="text-xs text-slate-700 dark:text-slate-300 font-medium leading-relaxed">
                        Analysis in progress. Please refresh the page shortly.
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                        Document text parsing and AI similarity analysis are currently in progress. Livewire polling will automatically update when the queue finishes.
                    </p>
                </div>
            </div>
        </div>

    @elseif ($isFailed)
        {{-- State 4: Failed --}}
        <div class="rounded-xl border border-rose-200 dark:border-rose-900/60 bg-rose-50/50 dark:bg-rose-950/20 p-5 shadow-xs transition-all">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-semibold text-rose-900 dark:text-rose-100">
                                AI Similarity & Plagiarism Inspection
                            </h4>
                            <span class="inline-flex items-center rounded-full bg-rose-100 dark:bg-rose-950 px-2 py-0.5 text-[10px] font-medium text-rose-700 dark:text-rose-300">
                                Analysis Failed
                            </span>
                        </div>
                        <p class="text-xs text-rose-700 dark:text-rose-300 leading-relaxed">
                            An error occurred during text extraction or LLM cross-comparison. Click below to retry the plagiarism inspection.
                        </p>
                    </div>
                </div>

                @if (isset($getAction) && $action = $getAction('runSimilarityCheck'))
                    <div class="shrink-0 sm:self-center">
                        {{ $action }}
                    </div>
                @endif
            </div>
        </div>

    @else
        {{-- State 3: Completed Analysis --}}
        @php
            $score = (float) ($report?->similarity_score ?? 0.0);
            $matchedSources = $report?->matched_sources ?? $report?->matched_sources_json ?? [];

            // Academic Threshold Configuration:
            // < 15%: Emerald / Green (Acceptable)
            // 15% - 25%: Amber / Yellow (Requires manual review)
            // > 25%: Rose / Red (High risk of duplication/plagiarism)
            $badgeColor = match (true) {
                $score > 25.0 => [
                    'card' => 'border-rose-200 dark:border-rose-900/60 bg-rose-50/40 dark:bg-rose-950/20',
                    'badge' => 'border-rose-300 bg-rose-100 text-rose-800 dark:border-rose-800 dark:bg-rose-950 dark:text-rose-300',
                    'dot' => 'bg-rose-500',
                    'label' => 'High Risk · Elevated Plagiarism Index',
                    'desc' => 'Conceptual overlap exceeds the 25% institutional limit. Critical manual inspection required.',
                ],
                $score >= 15.0 => [
                    'card' => 'border-amber-200 dark:border-amber-900/60 bg-amber-50/40 dark:bg-amber-950/20',
                    'badge' => 'border-amber-300 bg-amber-100 text-amber-800 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-300',
                    'dot' => 'bg-amber-500',
                    'label' => 'Moderate Overlap · Review Required',
                    'desc' => 'Calculated similarity is between 15% and 25%. Inspect highlighted source passages for proper citations.',
                ],
                default => [
                    'card' => 'border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/40 dark:bg-emerald-950/20',
                    'badge' => 'border-emerald-300 bg-emerald-100 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
                    'dot' => 'bg-emerald-500',
                    'label' => 'Acceptable · Original Research',
                    'desc' => 'Calculated similarity index is within standard academic tolerance (< 15%).',
                ],
            };
        @endphp

        {{-- Phase 2: Prominent Score Card --}}
        <div class="rounded-xl border {{ $badgeColor['card'] }} p-4 shadow-xs">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full {{ $badgeColor['dot'] }}"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                            Similarity Index: {{ $badgeColor['label'] }}
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">
                        {{ $badgeColor['desc'] }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 rounded-full border px-4 py-1.5 text-base font-extrabold shadow-xs {{ $badgeColor['badge'] }}">
                        {{ number_format($score, 1) }}%
                    </span>
                    @if (isset($getAction) && $action = $getAction('runSimilarityCheck'))
                        <div class="shrink-0">
                            {{ $action }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Phase 3: Side-by-Side Comparison UI --}}
        @if ($score == 0 || empty($matchedSources))
            {{-- Success Banner for Clean / Zero Matches --}}
            <div class="rounded-xl border border-emerald-200 dark:border-emerald-900/50 bg-emerald-50/70 dark:bg-emerald-950/30 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-xs font-semibold text-emerald-900 dark:text-emerald-100">
                            Academic Integrity Clearance Passed
                        </h5>
                        <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-0.5">
                            No significant conceptual overlap detected across the institutional archive.
                        </p>
                    </div>
                </div>
            </div>
        @else
            {{-- Side-by-Side Comparison Cards --}}
            <div class="space-y-3 pt-1">
                <div class="flex items-center justify-between px-1">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                        Detailed Source Comparisons ({{ count($matchedSources) }} Overlapping {{ count($matchedSources) === 1 ? 'Reference' : 'References' }})
                    </h5>
                    <span class="text-[11px] text-zinc-500 dark:text-zinc-400">
                        Side-by-side manuscript passage inspection
                    </span>
                </div>

                @foreach ($matchedSources as $match)
                    @php
                        $sourceTitle = is_array($match) ? ($match['source_project_title'] ?? $match['title'] ?? 'Archived Thesis Document') : (string) $match;
                        $studentText = is_array($match) ? ($match['student_text'] ?? $match['snippet'] ?? null) : null;
                        $originalText = is_array($match) ? ($match['original_text'] ?? $match['source_text'] ?? null) : null;
                        $percentage = is_array($match) && isset($match['percentage']) ? (float) $match['percentage'] : null;
                    @endphp

                    <div class="bg-zinc-50 border border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800 rounded-lg p-4 mb-4 shadow-xs">
                        {{-- Card Header with Linked Source Title --}}
                        <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-3">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-zinc-500 dark:text-zinc-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                                <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $sourceTitle }}
                                </span>
                            </div>

                            @if ($percentage !== null)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-medium bg-zinc-200/80 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                                    {{ number_format($percentage, 1) }}% Match
                                </span>
                            @endif
                        </div>

                        {{-- 2-Column Side-by-Side Comparison Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Left Column: Student's Manuscript --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700 dark:text-rose-400">
                                        Student's Manuscript
                                    </span>
                                </div>
                                <div class="rounded-lg border border-rose-200/80 dark:border-rose-900/60 bg-rose-50/60 dark:bg-rose-950/20 p-3 text-sm text-zinc-800 dark:text-zinc-200 leading-relaxed font-serif">
                                    @if ($studentText)
                                        <p class="whitespace-pre-line text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed">
                                            {{ $studentText }}
                                        </p>
                                    @else
                                        <p class="text-xs italic text-zinc-500 dark:text-zinc-400">
                                            Semantic overlap detected in submitted manuscript chapter text.
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Right Column: Archived Source Material --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 rounded-full bg-zinc-400"></span>
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">
                                        Archived Source Material
                                    </span>
                                </div>
                                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-100/70 dark:bg-zinc-800/50 p-3 text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed font-serif">
                                    @if ($originalText)
                                        <p class="whitespace-pre-line text-sm text-zinc-700 leading-relaxed">
                                            {{ $originalText }}
                                        </p>
                                    @else
                                        <p class="text-xs italic text-zinc-500 dark:text-zinc-400">
                                            Referenced excerpt indexed from institutional research repository archives.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
