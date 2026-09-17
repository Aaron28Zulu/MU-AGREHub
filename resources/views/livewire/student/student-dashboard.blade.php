<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto space-y-8">

    <!-- Workspace Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-[#e6e3da] dark:border-stone-800">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-[#737971] dark:text-stone-400">
                    Student Researcher Workspace
                </span>
                <span class="text-stone-300 dark:text-stone-600">&bull;</span>
                <span class="text-xs font-mono text-[#575c56] dark:text-stone-400">
                    ID: #{{ auth()->user()->id }} &middot; {{ auth()->user()->department ?? 'School of Agriculture' }}
                </span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-[#181a18] dark:text-white">
                Welcome back, {{ auth()->user()->name }}
            </h1>
            <p class="text-xs sm:text-sm text-[#575c56] dark:text-stone-400 mt-1">
                Manage your dissertation progress, access faculty feedback, track manuscript versions, and review evaluations.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            @if ($project)
                <a
                    href="{{ route('student.manuscripts') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded text-[#1c1e21] dark:text-stone-200 bg-white dark:bg-[#181b18] border border-[#e6e3da] dark:border-stone-700 hover:bg-[#faf8f5] dark:hover:bg-stone-800 transition"
                >
                    <svg class="w-3.5 h-3.5 text-[#1a3529] dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Manuscript History</span>
                    @if ($manuscripts->isNotEmpty())
                        <span class="ml-0.5 px-1.5 py-0.2 rounded font-mono text-[10px] font-bold bg-[#f2f7f4] text-[#1a3529] border border-[#c5dacf]/60">
                            {{ $manuscripts->count() }}
                        </span>
                    @endif
                </a>
            @endif

            <a
                href="{{ route('repository') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded text-[#575c56] dark:text-stone-300 bg-[#faf8f5] dark:bg-stone-800/60 border border-[#e6e3da] dark:border-stone-700 hover:bg-[#f2f7f4] hover:text-[#1a3529] transition"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <span>Browse Repository</span>
            </a>
        </div>
    </div>

    @if (! $project)
        <!-- ========================================== -->
        <!-- SCENARIO A: UNENROLLED / ORDINARY STUDENT  -->
        <!-- ========================================== -->
        <div class="space-y-6">
            <div class="rounded-xl border border-[#e6e3da] dark:border-stone-800 bg-white dark:bg-[#181b18] p-8 text-center sm:p-12 shadow-xs">
                <div class="mx-auto w-14 h-14 rounded-full bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950/60 dark:text-emerald-400 flex items-center justify-center mb-5 border border-[#c5dacf]/70">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-[#181a18] dark:text-white">
                    Initiate Your Postgraduate Research Journey
                </h2>
                <p class="text-xs sm:text-sm text-[#575c56] dark:text-stone-400 mt-2 max-w-lg mx-auto leading-relaxed">
                    You do not have an active research project registered in AgriHub. Register your dissertation proposal to be matched with a faculty supervisor and begin submitting academic milestones.
                </p>

                <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a
                        href="{{ route('student.proposal.create') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded text-xs font-semibold text-[#faf8f5] bg-[#1a3529] hover:bg-[#224233] transition shadow-xs"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        <span>Register Research Proposal &rarr;</span>
                    </a>
                    <a
                        href="{{ route('repository') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded text-xs font-medium text-[#1c1e21] dark:text-stone-200 bg-white dark:bg-stone-800 border border-[#e6e3da] dark:border-stone-700 hover:bg-[#faf8f5] transition"
                    >
                        <span>Explore Identified Research Gaps</span>
                    </a>
                </div>
            </div>

            <!-- Dissertation Roadmap Preview -->
            <div class="rounded-lg border border-[#e6e3da] dark:border-stone-800 bg-[#faf8f5] dark:bg-[#121412] p-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#737971] dark:text-stone-400 mb-4">
                    Dissertation Lifecycle Overview
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
                    <div class="p-4 bg-white dark:bg-[#181b18] rounded border border-[#e6e3da] dark:border-stone-800">
                        <span class="w-6 h-6 rounded-full bg-[#f2f7f4] text-[#1a3529] font-bold inline-flex items-center justify-center mb-2">1</span>
                        <h4 class="font-bold text-[#181a18] dark:text-white">Proposal Registration</h4>
                        <p class="text-[11px] text-[#575c56] dark:text-stone-400 mt-1">Submit background, problem statement, and initial methodology.</p>
                    </div>
                    <div class="p-4 bg-white dark:bg-[#181b18] rounded border border-[#e6e3da] dark:border-stone-800">
                        <span class="w-6 h-6 rounded-full bg-[#f2f7f4] text-[#1a3529] font-bold inline-flex items-center justify-center mb-2">2</span>
                        <h4 class="font-bold text-[#181a18] dark:text-white">Supervisor Matching</h4>
                        <p class="text-[11px] text-[#575c56] dark:text-stone-400 mt-1">Department head reviews topic and allocates academic supervisor.</p>
                    </div>
                    <div class="p-4 bg-white dark:bg-[#181b18] rounded border border-[#e6e3da] dark:border-stone-800">
                        <span class="w-6 h-6 rounded-full bg-[#f2f7f4] text-[#1a3529] font-bold inline-flex items-center justify-center mb-2">3</span>
                        <h4 class="font-bold text-[#181a18] dark:text-white">Milestone Iterations</h4>
                        <p class="text-[11px] text-[#575c56] dark:text-stone-400 mt-1">Upload chapter progress, receive markup drafts, and revise.</p>
                    </div>
                    <div class="p-4 bg-white dark:bg-[#181b18] rounded border border-[#e6e3da] dark:border-stone-800">
                        <span class="w-6 h-6 rounded-full bg-[#f2f7f4] text-[#1a3529] font-bold inline-flex items-center justify-center mb-2">4</span>
                        <h4 class="font-bold text-[#181a18] dark:text-white">Defense &amp; Archive</h4>
                        <p class="text-[11px] text-[#575c56] dark:text-stone-400 mt-1">Electronic rubric grading and cataloging in public repository.</p>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- ==================================================== -->
        <!-- SCENARIO B & C: ACTIVE OR COMPLETED RESEARCHER      -->
        <!-- ==================================================== -->

        @if ($isCompleted)
            <!-- SCENARIO C: DEFENDED / COMPLETED BANNER -->
            <div class="p-5 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-emerald-950 dark:text-emerald-200">
                            Research Study Successfully Completed &amp; Defended
                        </h3>
                        <p class="text-xs text-emerald-800/90 dark:text-emerald-300/80 mt-0.5">
                            This project has been reviewed, scored, and archived. Public metadata is indexed in the MUSAGRICA Research Archive.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2.5 shrink-0">
                    <a
                        href="{{ route('repository', ['search' => $project->title]) }}"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded text-white bg-emerald-700 hover:bg-emerald-800 transition"
                    >
                        <span>View Public Metadata</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            </div>
        @endif

        <!-- Milestone Stepper Pipeline -->
        <div class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6 pb-4 border-b border-[#f0eee6] dark:border-stone-800">
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-[#737971] dark:text-stone-400">
                        Academic Milestone Pipeline
                    </h2>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-[#575c56] dark:text-stone-400">Current Phase:</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                            {{ $isCompleted ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200' : 'bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950 dark:text-emerald-300 border border-[#c5dacf]/60' }}">
                            {{ $project->status->label() }}
                        </span>
                    </div>
                </div>

                @if ($project->full_document_path)
                    <div class="flex items-center gap-3">
                        <a
                            href="{{ route('projects.download', $project) }}"
                            class="inline-flex items-center gap-1.5 text-xs font-medium text-[#1a3529] dark:text-emerald-400 hover:underline"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Download Current Active Manuscript</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Horizontal Academic Stepper -->
            @php
                $stages = [
                    1 => ['label' => 'Proposal Draft', 'desc' => 'Initial scope'],
                    2 => ['label' => 'Proposal Submitted', 'desc' => 'Faculty allocation'],
                    3 => ['label' => 'Under Supervision', 'desc' => 'Investigations & Drafts'],
                    4 => ['label' => 'Under Review', 'desc' => 'Rubric evaluation'],
                    5 => ['label' => 'Completed', 'desc' => 'Archived & published'],
                ];
            @endphp

            <div class="overflow-x-auto pb-2">
                <ol class="flex items-center w-full min-w-[650px] justify-between text-xs">
                    @foreach ($stages as $index => $stage)
                        @php
                            $isStageCompleted = $stepIndex > $index || $project->status === \App\Enums\ProjectStatus::COMPLETED;
                            $isCurrent = $stepIndex === $index && $project->status !== \App\Enums\ProjectStatus::COMPLETED;
                        @endphp

                        <li class="flex-1 relative text-center {{ ! $loop->last ? 'after:content-[\'\'] after:w-full after:h-px after:inline-block after:absolute after:top-4 after:left-1/2 ' . ($stepIndex > $index ? 'after:bg-[#1a3529] dark:after:bg-emerald-600' : 'after:bg-[#e6e3da] dark:after:bg-stone-700') : '' }}">
                            <div class="flex flex-col items-center relative z-10">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-mono font-bold text-xs transition border
                                    @if($isStageCompleted) bg-[#1a3529] text-white border-[#1a3529] dark:bg-emerald-700 dark:border-emerald-600 shadow-xs
                                    @elseif($isCurrent) bg-[#f2f7f4] text-[#1a3529] border-[#1a3529] ring-2 ring-[#c5dacf] ring-offset-1 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-500
                                    @else bg-stone-50 text-stone-400 border-stone-300 dark:bg-stone-800 dark:text-stone-500 dark:border-stone-700
                                    @endif">
                                    @if ($isStageCompleted)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                    @else
                                        {{ $index }}
                                    @endif
                                </div>
                                <span class="mt-2 font-medium {{ $isCurrent ? 'text-[#1a3529] dark:text-emerald-400 font-bold' : ($isStageCompleted ? 'text-[#1c1e21] dark:text-stone-200' : 'text-stone-400 dark:text-stone-500') }}">
                                    {{ $stage['label'] }}
                                </span>
                                <span class="text-[10px] text-[#737971] dark:text-stone-500 hidden sm:block">{{ $stage['desc'] }}</span>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>

        <!-- Active Project & Supervisor Overview (Two-column layout) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Project Scope & Objectives -->
            <div class="lg:col-span-2 bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-6 flex flex-col justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2 text-xs">
                        <span class="font-medium px-2 py-0.5 rounded bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950 dark:text-emerald-300 border border-[#c5dacf]/60">
                            {{ $project->category->label() }}
                        </span>
                        @if ($project->completion_year)
                            <span class="text-stone-400 dark:text-stone-500">&bull; Academic Year {{ $project->completion_year }}</span>
                        @endif

                        @if ($project->latestSimilarityReport)
                            <span class="px-2 py-0.5 rounded font-mono text-[11px] font-bold border
                                {{ (float)$project->latestSimilarityReport->similarity_score > 25.0 ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                                Similarity: {{ number_format((float)$project->latestSimilarityReport->similarity_score, 1) }}%
                            </span>
                        @endif
                    </div>

                    <h2 class="text-base sm:text-lg font-bold text-[#181a18] dark:text-white mb-2.5 leading-snug">
                        {{ $project->title }}
                    </h2>

                    <div class="text-xs sm:text-sm text-[#495046] dark:text-stone-300 leading-relaxed whitespace-pre-line">
                        {{ $project->abstract }}
                    </div>

                    @if ($project->researchGaps->isNotEmpty())
                        <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-stone-800">
                            <span class="text-xs font-semibold text-zinc-700 dark:text-stone-300 block mb-1.5">
                                Addressed Research Gap(s):
                            </span>
                            <ul class="space-y-1">
                                @foreach ($project->researchGaps as $gap)
                                    <li class="text-xs text-zinc-600 dark:text-stone-400 flex items-start gap-1.5">
                                        <span class="text-emerald-700 dark:text-emerald-400">&bull;</span>
                                        <span>{{ $gap->description }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                @if ($evaluations->isNotEmpty())
                    @php $latestEval = $evaluations->first(); @endphp
                    <div class="mt-5 p-3.5 rounded border border-[#e6e3da] dark:border-stone-800 bg-[#faf8f5] dark:bg-[#121412] text-xs">
                        <div class="flex items-center justify-between font-medium text-stone-700 dark:text-stone-300 mb-1">
                            <span>Latest Review Mark: <strong class="font-bold text-[#181a18] dark:text-white">{{ number_format((float) $latestEval->rubric_score, 1) }}/100</strong></span>
                            <span class="text-[11px] text-stone-400 font-mono">{{ $latestEval->created_at->format('M d, Y') }}</span>
                        </div>
                        <p class="text-[11px] text-[#575c56] dark:text-stone-400 line-clamp-2 italic">
                            &ldquo;{{ $latestEval->feedback ?: 'No written commentary attached.' }}&rdquo;
                        </p>
                    </div>
                @endif
            </div>

            <!-- Right: Assigned Academic Supervisor Card -->
            <div class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#737971] dark:text-stone-400 mb-4">
                        Assigned Academic Supervisor
                    </h3>

                    @if ($project->supervisor)
                        <div class="flex items-start gap-3">
                            <div class="w-11 h-11 rounded-full bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950 dark:text-emerald-300 flex items-center justify-center font-bold text-sm shrink-0 border border-[#c5dacf]/60">
                                {{ substr($project->supervisor->name, 0, 2) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-[#181a18] dark:text-white">{{ $project->supervisor->name }}</h4>
                                <p class="text-xs text-[#737971] dark:text-stone-400 mt-0.5">{{ $project->supervisor->department ?? 'School of Agriculture' }}</p>
                                <a href="mailto:{{ $project->supervisor->email }}" class="inline-flex items-center gap-1 mt-2 text-xs font-medium text-[#1a3529] dark:text-emerald-400 hover:underline">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    <span>{{ $project->supervisor->email }}</span>
                                </a>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-[#f0eee6] dark:border-stone-800 text-xs text-[#575c56] dark:text-stone-400 space-y-2">
                            <div class="flex items-center justify-between">
                                <span>Supervision Office Hours:</span>
                                <span class="font-medium text-stone-700 dark:text-stone-300">Tue &middot; Thu (14:00 - 16:00)</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Faculty Room:</span>
                                <span class="font-medium text-stone-700 dark:text-stone-300">Building 4, Office 208</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 text-xs text-[#737971] dark:text-stone-400">
                            <svg class="w-8 h-8 mx-auto text-stone-300 dark:text-stone-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            <p class="font-medium">Faculty allocation in progress</p>
                            <p class="text-[11px] text-stone-400 mt-0.5">The department head will review your proposal and assign a research supervisor.</p>
                        </div>
                    @endif
                </div>

                <div class="mt-6 pt-4 border-t border-[#f0eee6] dark:border-stone-800 text-center">
                    <span class="text-[11px] text-[#737971] dark:text-stone-500">
                        MUSAGRICA Academic Supervision Portal &bull; Clearance Level 1
                    </span>
                </div>
            </div>
        </div>

        <!-- Dynamic Milestone Action Zone -->
        <div class="mt-8">
            <livewire:student.milestone-submitter :project="$project" :key="$project->id" />
        </div>

        <!-- Manuscript Download & Version History Card -->
        <div class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-6">
            <div class="flex items-center justify-between pb-4 border-b border-[#f0eee6] dark:border-stone-800 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-[#181a18] dark:text-white">Manuscript Versions &amp; Archive Iterations</h3>
                    <p class="text-xs text-[#737971] dark:text-stone-400 mt-0.5">Chronological record of submitted drafts with secure signed download access.</p>
                </div>
                <a href="{{ route('student.manuscripts') }}" class="text-xs text-[#1a3529] dark:text-emerald-400 hover:underline font-medium">
                    Full Submission Log &rarr;
                </a>
            </div>

            @if ($manuscripts->isNotEmpty())
                <div class="divide-y divide-[#f0eee6] dark:divide-stone-800">
                    @foreach ($manuscripts->take(4) as $manuscript)
                        <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-3">
                                <span class="px-2 py-0.5 rounded font-mono font-bold text-[11px] bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950 dark:text-emerald-300 border border-[#c5dacf]/60">
                                    {{ $manuscript->version }}
                                </span>
                                <div>
                                    <div class="font-semibold text-[#181a18] dark:text-white flex items-center gap-2">
                                        <span>{{ $manuscript->milestone_stage }}</span>
                                        <span class="text-[11px] font-normal text-stone-400 font-mono">({{ $manuscript->formatted_file_size }})</span>
                                    </div>
                                    <p class="text-[11px] text-[#737971] dark:text-stone-400 mt-0.5">
                                        Uploaded: {{ $manuscript->created_at->format('M d, Y &bull; H:i') }}
                                        @if ($manuscript->submission_notes)
                                            &mdash; <span class="italic text-[#575c56] dark:text-stone-400">"{{ Str::limit($manuscript->submission_notes, 60) }}"</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2.5">
                                <a
                                    href="{{ route('projects.manuscripts.download', ['project' => $project->id, 'manuscript' => $manuscript->id]) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded text-xs font-medium text-[#1a3529] bg-[#f2f7f4] hover:bg-[#e3ede6] dark:bg-emerald-950/60 dark:text-emerald-300 dark:hover:bg-emerald-900/50 border border-[#c5dacf]/60 transition"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    <span>Download {{ $manuscript->version }}</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif ($project->full_document_path)
                <!-- Fallback when legacy full_document_path exists without manuscript rows -->
                <div class="py-3 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-0.5 rounded font-mono font-bold text-[11px] bg-[#f2f7f4] text-[#1a3529] border border-[#c5dacf]/60">
                            v1.0
                        </span>
                        <div>
                            <span class="font-semibold text-[#181a18] dark:text-white">Active Research Manuscript</span>
                            <p class="text-[11px] text-[#737971] dark:text-stone-400 mt-0.5">Primary submission stored on secure vault disk.</p>
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
                <div class="py-6 text-center text-xs text-[#737971] dark:text-stone-500">
                    No manuscript files uploaded yet. Submit your first milestone above.
                </div>
            @endif
        </div>

        <!-- Integrated Supervisor Feedback & Revision Timeline (In-layout) -->
        <div id="feedback-timeline" class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-6 scroll-mt-20">
            <div class="flex items-center justify-between pb-4 border-b border-[#f0eee6] dark:border-stone-800 mb-6">
                <div>
                    <h3 class="text-sm font-bold text-[#181a18] dark:text-white">Supervisor Evaluation &amp; Feedback Timeline</h3>
                    <p class="text-xs text-[#737971] dark:text-stone-400 mt-0.5">Chronological faculty assessments, rubric scoring, and downloadable marked-up drafts.</p>
                </div>
                <span class="text-xs font-mono font-medium text-[#737971] dark:text-stone-400">
                    {{ $evaluations->count() }} {{ Str::plural('Evaluation', $evaluations->count()) }}
                </span>
            </div>

            @if ($evaluations->isNotEmpty())
                <div class="space-y-6 relative before:absolute before:inset-0 before:left-3.5 before:w-0.5 before:bg-[#e6e3da] dark:before:bg-stone-800">
                    @foreach ($evaluations as $evaluation)
                        <div class="relative flex items-start gap-4">
                            <!-- Timeline Dot Indicator -->
                            <div class="w-7 h-7 rounded-full bg-white dark:bg-[#181b18] border-2 border-[#1a3529] dark:border-emerald-500 flex items-center justify-center shrink-0 z-10 shadow-xs">
                                <span class="w-2 h-2 rounded-full bg-[#1a3529] dark:bg-emerald-400"></span>
                            </div>

                            <!-- Evaluation Card -->
                            <div class="flex-1 p-5 rounded-lg border border-[#e6e3da] dark:border-stone-800 bg-[#faf8f5] dark:bg-[#121412] space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-xs text-[#181a18] dark:text-white">
                                                {{ $evaluation->supervisor?->name ?? 'Faculty Supervisor' }}
                                            </span>
                                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-stone-200 dark:bg-stone-800 text-stone-600 dark:text-stone-300">
                                                Supervisor
                                            </span>
                                        </div>
                                        <span class="text-[11px] font-mono text-[#737971] dark:text-stone-400 mt-0.5 block">
                                            Reviewed on {{ $evaluation->created_at->format('F d, Y · H:i') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded font-mono text-xs font-bold
                                            {{ (float)$evaluation->rubric_score >= 50.0 ? 'bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950 dark:text-emerald-300 border border-[#c5dacf]/60' : 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300 border border-rose-200' }}">
                                            Rubric Score: {{ number_format((float) $evaluation->rubric_score, 1) }}%
                                        </span>
                                    </div>
                                </div>

                                <div class="text-xs text-[#3d423b] dark:text-stone-300 leading-relaxed p-4 bg-white dark:bg-[#181b18] rounded border border-[#e6e3da] dark:border-stone-800 shadow-xs">
                                    <span class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider block mb-1">Written Feedback &amp; Critique:</span>
                                    {{ $evaluation->feedback ?: 'Milestone reviewed without explicit written remarks.' }}
                                </div>

                                @if ($evaluation->document_markup_path)
                                    <div class="pt-1">
                                        <a
                                            href="{{ route('projects.download.marked-up', $project) }}"
                                            class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-[#1a3529] dark:text-emerald-400 bg-white dark:bg-stone-800 border border-[#c5dacf] dark:border-stone-700 hover:bg-[#f2f7f4] rounded transition shadow-xs"
                                        >
                                            <svg class="w-3.5 h-3.5 text-[#1a3529] dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            <span>Download Annotated Supervisor Markup File (.pdf / .docx)</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-xs text-[#737971] dark:text-stone-500 bg-[#faf8f5] dark:bg-[#121412] rounded border border-[#e6e3da] dark:border-stone-800">
                    <svg class="w-7 h-7 mx-auto text-stone-300 dark:text-stone-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                    <p class="font-medium text-stone-600 dark:text-stone-400">No Supervisor Evaluations Yet</p>
                    <p class="text-[11px] text-stone-400 mt-0.5">When your supervisor grades milestone submissions or uploads annotated drafts, feedback will appear here.</p>
                </div>
            @endif
        </div>

    @endif

</div>
