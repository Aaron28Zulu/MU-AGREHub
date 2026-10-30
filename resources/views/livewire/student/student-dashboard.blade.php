<div>
    {{-- Workspace header --}}
    <section class="border-b border-line bg-canvas">
        <div class="container-page flex flex-col gap-5 py-6 sm:flex-row sm:py-8 sm:items-end sm:justify-between">
            <div>
                <p class="eyebrow">My research &middot; {{ auth()->user()->department ?? 'Mulungushi University' }}</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Welcome back, {{ str(auth()->user()->name)->before(' ') }}</h1>
                <p class="mt-2 text-ink-soft">Track your study, submit milestone drafts and read your supervisor's feedback.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($project)
                    <a href="{{ route('student.manuscripts') }}" class="btn-secondary btn-sm">
                        <x-glyph name="document-duplicate" class="h-4 w-4" /> Submission history
                        @if ($manuscripts->isNotEmpty())
                            <span class="rounded bg-forest-50 px-1.5 text-[11px] font-bold text-forest-800">{{ $manuscripts->count() }}</span>
                        @endif
                    </a>
                @endif
                <a href="{{ route('repository') }}" class="btn-secondary btn-sm"><x-glyph name="search" class="h-4 w-4" /> Search archive</a>
            </div>
        </div>
    </section>

    <div class="container-page py-6 sm:py-8">
        @if (! $project)
            {{-- No registered study yet --}}
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="panel p-6 sm:p-10 lg:col-span-2">
                    <x-glyph name="academic-cap" class="h-8 w-8 text-forest-700" />
                    <h2 class="mt-5 text-2xl font-bold tracking-tight">Initiate Your Postgraduate Research Journey</h2>
                    <p class="mt-3 max-w-xl leading-relaxed text-ink-soft">
                        You don't have a research project registered yet. Check the archive for similar work, pick a gap worth closing, then register your proposal to be matched with a supervisor.
                    </p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="{{ route('student.proposal.create') }}" class="btn-primary">
                            <x-glyph name="plus" class="h-4 w-4" /> Register Research Proposal
                        </a>
                        <a href="{{ route('gaps') }}" class="btn-secondary">Explore research gaps</a>
                    </div>
                </div>

                @include('livewire.student.partials.updates')
            </div>

            <section class="mt-10" aria-labelledby="lifecycle-heading">
                <h2 id="lifecycle-heading" class="text-lg font-semibold">Dissertation Lifecycle Overview</h2>
                <ol class="mt-5 grid grid-cols-1 gap-px overflow-hidden rounded-lg border border-line bg-line md:grid-cols-4">
                    @foreach ([
                        ['Register a proposal', 'Background, problem statement and planned methodology, checked against the archive.'],
                        ['Supervisor approval', 'Your supervisor reviews the topic and approves it for supervision.'],
                        ['Milestone drafts', 'Upload progress reports and chapters; receive marks, comments and annotated drafts.'],
                        ['Final approval & archive', 'The approved study is catalogued and its research gaps indexed for others.'],
                    ] as $index => [$heading, $body])
                        <li class="bg-white p-6">
                            <span class="text-sm font-semibold text-forest-700">Step {{ $index + 1 }}</span>
                            <h3 class="mt-2 font-semibold">{{ $heading }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-ink-soft">{{ $body }}</p>
                        </li>
                    @endforeach
                </ol>
            </section>
        @else
            @if ($isCompleted)
                <div class="mb-8 flex flex-col gap-4 rounded-lg border border-forest-200 bg-forest-50 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-3">
                        <x-glyph name="check-badge" class="h-6 w-6 shrink-0 text-forest-700" />
                        <div>
                            <h2 class="font-semibold text-forest-900">Research Study Successfully Completed &amp; Defended</h2>
                            <p class="mt-1 text-sm text-forest-800">Your study has been approved and catalogued. Its abstract and research gaps are now discoverable in the archive.</p>
                        </div>
                    </div>
                    <a href="{{ route('repository.show', $project) }}" class="btn-primary btn-sm shrink-0">View Public Metadata</a>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="min-w-0 space-y-8 lg:col-span-2">
                    {{-- Study summary --}}
                    <section class="panel p-6 sm:p-8">
                        <div class="flex flex-wrap items-center gap-3 text-sm">
                            <span class="text-muted">Current Phase:</span>
                            <x-status-badge :status="$project->status" />
                            <span class="text-muted">&middot; {{ $project->category->label() }}</span>
                        </div>
                        <h2 class="record-title mt-4 text-xl sm:text-2xl">{{ $project->title }}</h2>
                        <p class="mt-4 line-clamp-4 leading-relaxed whitespace-pre-line text-ink-soft">{{ $project->plainAbstract() }}</p>

                        @if ($project->researchGaps->isNotEmpty())
                            <div class="mt-5 border-t border-line pt-4">
                                <p class="text-sm font-medium">Research gaps this study addresses</p>
                                <ul class="mt-2 space-y-1.5">
                                    @foreach ($project->researchGaps as $gap)
                                        <li class="flex gap-2 text-sm text-ink-soft"><x-glyph name="light-bulb" class="mt-0.5 h-4 w-4 shrink-0 text-ochre-500" /> {{ $gap->description }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Pipeline --}}
                        @php
                            $stages = ['Draft', 'Proposal submitted', 'Under supervision', 'Under review', 'Completed'];
                        @endphp
                        <ol class="mt-8 grid grid-cols-1 gap-2.5 sm:grid-cols-5 sm:gap-2" aria-label="Project pipeline">
                            @foreach ($stages as $offset => $label)
                                @php
                                    $position = $offset + 1;
                                    $done = $stepIndex > $position || $isCompleted;
                                    $current = $stepIndex === $position && ! $isCompleted;
                                @endphp
                                <li class="flex items-center gap-3 sm:block">
                                    <span @class(['block h-1.5 w-10 shrink-0 rounded-full sm:w-auto', 'bg-forest-700' => $done, 'bg-forest-400' => $current, 'bg-line' => ! $done && ! $current])></span>
                                    <span @class(['block text-xs leading-tight sm:mt-2', 'font-semibold text-forest-800' => $current, 'text-ink-soft' => $done, 'text-subtle' => ! $done && ! $current])>{{ $label }}</span>
                                </li>
                            @endforeach
                        </ol>

                        <div class="mt-6 flex flex-wrap gap-x-6 gap-y-2 border-t border-line pt-4 text-sm">
                            @if ($project->full_document_path)
                                <a href="{{ route('projects.download', $project) }}" class="inline-flex items-center gap-1.5 font-medium text-forest-700 hover:underline"><x-glyph name="arrow-down-tray" class="h-4 w-4" /> Download current manuscript</a>
                            @endif
                            <a href="{{ route('repository.show', $project) }}" class="inline-flex items-center gap-1.5 font-medium text-ink-soft hover:text-ink"><x-glyph name="eye" class="h-4 w-4" /> Preview catalogue record</a>
                        </div>
                    </section>

                    {{-- Submission zone --}}
                    <livewire:student.milestone-submitter :project="$project" :key="'submitter-'.$project->id" />

                    {{-- Feedback timeline --}}
                    <section id="feedback-timeline" class="panel scroll-mt-24" aria-labelledby="feedback-heading">
                        <div class="flex items-center justify-between border-b border-line px-6 py-4">
                            <h2 id="feedback-heading" class="font-semibold">Supervisor feedback</h2>
                            <span class="text-sm text-muted">{{ $evaluations->count() }} {{ Str::plural('review', $evaluations->count()) }}</span>
                        </div>
                        @forelse ($evaluations as $evaluation)
                            <article class="border-b border-line px-6 py-5 last:border-b-0">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-sm"><span class="font-semibold">{{ $evaluation->supervisor?->name ?? 'Supervisor' }}</span> <span class="text-muted">&middot; {{ $evaluation->created_at->format('j M Y') }}</span></p>
                                    <span @class(['tag text-xs', 'bg-forest-50 text-forest-800' => (float) $evaluation->rubric_score >= 50, 'bg-red-50 text-red-800' => (float) $evaluation->rubric_score < 50])>
                                        Rubric {{ number_format((float) $evaluation->rubric_score, 1) }} / 100
                                    </span>
                                </div>
                                <p class="mt-3 text-sm leading-relaxed whitespace-pre-line text-ink-soft">{{ $evaluation->feedback ?: 'Reviewed without written remarks.' }}</p>
                                @if ($evaluation->document_markup_path)
                                    <a href="{{ route('projects.download.marked-up', $project) }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-forest-700 hover:underline">
                                        <x-glyph name="arrow-down-tray" class="h-4 w-4" /> Download annotated draft
                                    </a>
                                @endif
                            </article>
                        @empty
                            <p class="px-6 py-10 text-center text-sm text-muted">When your supervisor reviews a submission, their marks and comments appear here.</p>
                        @endforelse
                    </section>
                </div>

                <div class="space-y-6">
                    {{-- Supervisor --}}
                    <section class="panel p-6" aria-labelledby="supervisor-heading">
                        <h2 id="supervisor-heading" class="eyebrow">Supervisor</h2>
                        @if ($project->supervisor)
                            <div class="mt-4 flex items-start gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-forest-100 text-sm font-bold text-forest-800">{{ str($project->supervisor->name)->replace(['Dr. ', 'Prof. '], '')->explode(' ')->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('') }}</span>
                                <div class="min-w-0">
                                    <p class="font-semibold">{{ $project->supervisor->name }}</p>
                                    <p class="text-sm text-muted">{{ $project->supervisor->department ?? 'Mulungushi University' }}</p>
                                    <a href="mailto:{{ $project->supervisor->email }}" class="mt-2 flex items-center gap-1.5 text-sm font-medium text-forest-700 hover:underline"><x-glyph name="envelope" class="h-4 w-4 shrink-0" /> <span class="truncate">{{ $project->supervisor->email }}</span></a>
                                </div>
                            </div>
                        @else
                            <p class="mt-3 text-sm text-ink-soft">No supervisor assigned yet. The department will allocate one after reviewing your proposal.</p>
                        @endif
                    </section>

                    @include('livewire.student.partials.updates')

                    {{-- Originality --}}
                    <section class="panel p-6" aria-labelledby="originality-heading">
                        <h2 id="originality-heading" class="eyebrow">Originality check</h2>
                        {{-- Scores and match breakdowns stay with the supervisor (academic integrity policy). --}}
                        @if ($project->similarity_check_status === \App\Enums\SimilarityCheckStatus::PROCESSING)
                            <p class="mt-3 flex items-center gap-2 text-sm text-ink-soft"><x-glyph name="arrow-path" class="h-4 w-4 animate-spin text-muted" /> Your latest upload is being checked.</p>
                        @elseif ($project->similarity_check_status === \App\Enums\SimilarityCheckStatus::COMPLETED)
                            <p class="mt-3 flex items-start gap-2 text-sm text-ink-soft"><x-glyph name="check-circle" class="mt-0.5 h-4 w-4 shrink-0 text-forest-700" /> Your latest draft has been checked against the archive. Your supervisor will raise any concerns with you.</p>
                        @else
                            <p class="mt-3 text-sm text-ink-soft">A check runs automatically each time you upload a draft.</p>
                        @endif
                    </section>
                </div>
            </div>
        @endif
    </div>
</div>
