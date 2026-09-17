<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto space-y-8">

    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-[#e6e3da] dark:border-stone-800">
        <div>
            <div class="flex items-center gap-2 mb-1 text-xs">
                <a href="{{ route('student.dashboard') }}" class="text-[#737971] dark:text-stone-400 hover:underline">
                    &larr; Return to Workspace
                </a>
                <span class="text-stone-300 dark:text-stone-600">&bull;</span>
                <span class="font-semibold uppercase tracking-wider text-[#737971] dark:text-stone-400">
                    Institutional Vault Archive
                </span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-[#181a18] dark:text-white">
                Manuscript &amp; Submission History
            </h1>
            <p class="text-xs sm:text-sm text-[#575c56] dark:text-stone-400 mt-1">
                Audit trail of all manuscript iterations, progress reports, and associated supervisory assessments.
            </p>
        </div>

        @if ($project)
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded text-xs font-semibold bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950 dark:text-emerald-300 border border-[#c5dacf]/60">
                    <span class="w-2 h-2 rounded-full bg-[#1a3529] dark:bg-emerald-400"></span>
                    <span>{{ $project->status->label() }}</span>
                </span>
            </div>
        @endif
    </div>

    @if (! $project)
        <!-- Empty State -->
        <div class="text-center py-16 px-6 bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800">
            <svg class="w-10 h-10 mx-auto text-stone-300 dark:text-stone-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            <h2 class="text-base font-bold text-[#181a18] dark:text-white">No Registered Submissions</h2>
            <p class="text-xs text-[#737971] dark:text-stone-400 mt-1 max-w-sm mx-auto">
                You do not currently have an active research study registered in the system.
            </p>
            <div class="mt-5">
                <a
                    href="{{ route('student.proposal.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded text-xs font-semibold text-[#faf8f5] bg-[#1a3529] hover:bg-[#224233] transition"
                >
                    Register Research Proposal
                </a>
            </div>
        </div>
    @else
        <!-- Study Metadata Strip -->
        <div class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-[11px] font-mono text-[#737971] dark:text-stone-400 uppercase tracking-wider block">
                    Active Study Title
                </span>
                <h3 class="text-base font-bold text-[#181a18] dark:text-white mt-0.5">
                    {{ $project->title }}
                </h3>
                <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-[#575c56] dark:text-stone-400">
                    <span>{{ $project->category->label() }}</span>
                    <span>&bull;</span>
                    <span>Supervisor: <strong class="text-[#1c1e21] dark:text-stone-200">{{ $project->supervisor?->name ?? 'Allocation Pending' }}</strong></span>
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded text-xs font-medium bg-[#faf8f5] dark:bg-stone-800 border border-[#e6e3da] dark:border-stone-700 text-[#575c56] dark:text-stone-300">
                    <svg class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Private Disk Guard Active</span>
                </span>
            </div>
        </div>

        <!-- Manuscript Versions Table -->
        <div class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 overflow-hidden shadow-xs">
            <div class="p-5 border-b border-[#f0eee6] dark:border-stone-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-[#181a18] dark:text-white">Uploaded Manuscript Iterations</h3>
                    <p class="text-xs text-[#737971] dark:text-stone-400 mt-0.5">Every file submitted is archived with cryptographic timestamps.</p>
                </div>
                <span class="text-xs font-mono text-stone-500">
                    {{ $manuscripts->count() }} Total {{ Str::plural('Draft', $manuscripts->count()) }}
                </span>
            </div>

            @if ($manuscripts->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#faf8f5] dark:bg-[#121412] text-[#737971] dark:text-stone-400 uppercase tracking-wider font-semibold border-b border-[#f0eee6] dark:border-stone-800">
                            <tr>
                                <th class="py-3.5 px-4">Version</th>
                                <th class="py-3.5 px-4">Milestone Phase</th>
                                <th class="py-3.5 px-4">Original File &amp; Size</th>
                                <th class="py-3.5 px-4">Submission Date</th>
                                <th class="py-3.5 px-4">Author Notes</th>
                                <th class="py-3.5 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f0eee6] dark:divide-stone-800 text-[#1c1e21] dark:text-stone-200">
                            @foreach ($manuscripts as $manuscript)
                                <tr class="hover:bg-[#faf8f5]/60 dark:hover:bg-stone-800/30 transition">
                                    <td class="py-3.5 px-4 font-mono font-bold">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950 dark:text-emerald-300 border border-[#c5dacf]/60">
                                            {{ $manuscript->version }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 font-medium">
                                        {{ $manuscript->milestone_stage }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-medium text-[#181a18] dark:text-white">{{ $manuscript->file_name }}</div>
                                        <div class="text-[11px] text-stone-400 font-mono">{{ $manuscript->formatted_file_size }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-[11px] text-stone-500">
                                        {{ $manuscript->created_at->format('M d, Y · H:i') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-[#575c56] dark:text-stone-400 max-w-xs truncate">
                                        {{ $manuscript->submission_notes ?: '&mdash;' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <a
                                            href="{{ route('projects.manuscripts.download', ['project' => $project->id, 'manuscript' => $manuscript->id]) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded text-[#1a3529] dark:text-emerald-400 hover:bg-[#f2f7f4] dark:hover:bg-emerald-950/50 border border-[#c5dacf]/60 transition"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            <span>Stream / Download</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif ($project->full_document_path)
                <div class="p-6 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-0.5 rounded font-mono font-bold text-[11px] bg-[#f2f7f4] text-[#1a3529] border border-[#c5dacf]/60">
                            v1.0
                        </span>
                        <div>
                            <span class="font-semibold text-[#181a18] dark:text-white">Active Dissertation Manuscript Draft</span>
                            <p class="text-[11px] text-stone-400 mt-0.5">Primary manuscript recorded in vault.</p>
                        </div>
                    </div>
                    <a
                        href="{{ route('projects.download', $project) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded text-xs font-medium text-[#1a3529] bg-[#f2f7f4] hover:bg-[#e3ede6] border border-[#c5dacf]/60 transition"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Download Active Manuscript</span>
                    </a>
                </div>
            @else
                <div class="py-12 text-center text-xs text-[#737971] dark:text-stone-500">
                    No manuscript iterations have been uploaded for this study.
                </div>
            @endif
        </div>

        <!-- Historical Supervisor Assessments -->
        <div class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-6 space-y-4">
            <div class="pb-3 border-b border-[#f0eee6] dark:border-stone-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-[#181a18] dark:text-white">Supervisor Assessment Records</h3>
                    <p class="text-xs text-[#737971] dark:text-stone-400 mt-0.5">Evaluations and marks issued across submission milestones.</p>
                </div>
                <span class="text-xs font-mono text-stone-500">{{ $evaluations->count() }} Records</span>
            </div>

            @if ($evaluations->isNotEmpty())
                <div class="divide-y divide-[#f0eee6] dark:divide-stone-800">
                    @foreach ($evaluations as $eval)
                        <div class="py-4 flex flex-col sm:flex-row sm:items-start justify-between gap-4 text-xs">
                            <div class="space-y-1.5 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-[#181a18] dark:text-white">{{ $eval->supervisor?->name ?? 'Supervisor' }}</span>
                                    <span class="text-stone-400">&bull;</span>
                                    <span class="font-mono text-[11px] text-stone-500">{{ $eval->created_at->format('M d, Y · H:i') }}</span>
                                </div>
                                <p class="text-[#495046] dark:text-stone-300 italic bg-[#faf8f5] dark:bg-[#121412] p-3 rounded border border-[#e6e3da] dark:border-stone-800">
                                    &ldquo;{{ $eval->feedback ?: 'No written feedback provided.' }}&rdquo;
                                </p>
                            </div>

                            <div class="flex flex-col sm:items-end gap-2 shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded font-mono text-xs font-bold
                                    {{ (float)$eval->rubric_score >= 50.0 ? 'bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950 dark:text-emerald-300 border border-[#c5dacf]/60' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    Rubric: {{ number_format((float) $eval->rubric_score, 1) }}%
                                </span>

                                @if ($eval->document_markup_path)
                                    <a
                                        href="{{ route('projects.download.marked-up', $project) }}"
                                        class="inline-flex items-center gap-1 text-[11px] font-medium text-[#1a3529] dark:text-emerald-400 hover:underline"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Download Markup</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-6 text-center text-xs text-[#737971] dark:text-stone-500">
                    No supervisor assessments recorded yet.
                </div>
            @endif
        </div>
    @endif

</div>
