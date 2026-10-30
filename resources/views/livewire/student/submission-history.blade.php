<div>
    <section class="border-b border-line bg-canvas">
        <div class="container-page py-6 sm:py-8">
            <nav class="text-sm text-muted" aria-label="Breadcrumb">
                <a href="{{ route('student.dashboard') }}" class="hover:text-forest-800">My research</a>
                <span class="mx-1.5" aria-hidden="true">/</span>
                <span class="text-ink">Submissions</span>
            </nav>
            <div class="mt-4 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Submission history</h1>
                    <p class="mt-2 text-ink-soft">Every version you've uploaded, and every assessment you've received.</p>
                </div>
                @if ($project)
                    <x-status-badge :status="$project->status" />
                @endif
            </div>
        </div>
    </section>

    <div class="container-page space-y-6 py-6 sm:space-y-8 sm:py-8">
        @if (! $project)
            <div class="py-16 text-center">
                <x-glyph name="document-duplicate" class="mx-auto h-10 w-10 text-subtle" />
                <h2 class="mt-4 text-base font-semibold">No submissions yet</h2>
                <p class="mt-2 text-sm text-ink-soft">Register a research proposal to start your submission history.</p>
                <a href="{{ route('student.proposal.create') }}" class="btn-primary mt-6">Register Research Proposal</a>
            </div>
        @else
            <div class="panel flex flex-col gap-2 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="eyebrow">Study</p>
                    <h2 class="record-title mt-1 text-lg">{{ $project->title }}</h2>
                    <p class="mt-1 text-sm text-muted">{{ $project->category->label() }} &middot; Supervisor: <span class="text-ink-soft">{{ $project->supervisor?->name ?? 'Allocation pending' }}</span></p>
                </div>
            </div>

            <section class="panel overflow-hidden" aria-labelledby="versions-heading">
                <div class="flex items-center justify-between border-b border-line px-6 py-4">
                    <h2 id="versions-heading" class="font-semibold">Uploaded versions</h2>
                    <span class="text-sm text-muted">{{ $manuscripts->count() }} {{ Str::plural('file', $manuscripts->count()) }}</span>
                </div>

                @if ($manuscripts->isNotEmpty())
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-line bg-canvas text-xs font-semibold text-muted">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Version</th>
                                    <th scope="col" class="px-6 py-3">Milestone</th>
                                    <th scope="col" class="px-6 py-3">File</th>
                                    <th scope="col" class="px-6 py-3">Uploaded</th>
                                    <th scope="col" class="px-6 py-3">Notes</th>
                                    <th scope="col" class="px-6 py-3 text-right"><span class="sr-only">Download</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line">
                                @foreach ($manuscripts as $manuscript)
                                    <tr class="hover:bg-paper">
                                        <td class="px-6 py-4"><span class="tag bg-forest-50 font-mono text-forest-800">{{ $manuscript->version }}</span></td>
                                        <td class="px-6 py-4 font-medium">{{ $manuscript->milestone_stage }}</td>
                                        <td class="px-6 py-4">
                                            <p class="font-medium text-ink">{{ $manuscript->file_name }}</p>
                                            <p class="text-xs text-muted">{{ $manuscript->formatted_file_size }}</p>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-ink-soft">{{ $manuscript->created_at->format('j M Y, H:i') }}</td>
                                        <td class="max-w-xs px-6 py-4 text-ink-soft"><span class="line-clamp-2">{{ $manuscript->submission_notes ?: '—' }}</span></td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('projects.manuscripts.download', ['project' => $project->id, 'manuscript' => $manuscript->id]) }}" class="inline-flex items-center gap-1.5 font-medium text-forest-700 hover:underline">
                                                <x-glyph name="arrow-down-tray" class="h-4 w-4" /> Download
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif ($project->full_document_path)
                    <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-5 text-sm">
                        <p><span class="tag bg-forest-50 font-mono text-forest-800">v1.0</span> <span class="ml-2 font-medium">Current manuscript</span></p>
                        <a href="{{ route('projects.download', $project) }}" class="inline-flex items-center gap-1.5 font-medium text-forest-700 hover:underline"><x-glyph name="arrow-down-tray" class="h-4 w-4" /> Download</a>
                    </div>
                @else
                    <p class="px-6 py-12 text-center text-sm text-muted">No files uploaded for this study yet.</p>
                @endif
            </section>

            <section class="panel" aria-labelledby="assessments-heading">
                <div class="flex items-center justify-between border-b border-line px-6 py-4">
                    <h2 id="assessments-heading" class="font-semibold">Supervisor assessments</h2>
                    <span class="text-sm text-muted">{{ $evaluations->count() }} {{ Str::plural('record', $evaluations->count()) }}</span>
                </div>
                @forelse ($evaluations as $eval)
                    <article class="flex flex-col gap-3 border-b border-line px-6 py-5 last:border-b-0 sm:flex-row sm:justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm"><span class="font-semibold">{{ $eval->supervisor?->name ?? 'Supervisor' }}</span> <span class="text-muted">&middot; {{ $eval->created_at->format('j M Y, H:i') }}</span></p>
                            <p class="mt-2 text-sm leading-relaxed whitespace-pre-line text-ink-soft">{{ $eval->feedback ?: 'No written feedback provided.' }}</p>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-3 sm:flex-col sm:items-end sm:gap-2">
                            <span @class(['tag text-xs', 'bg-forest-50 text-forest-800' => (float) $eval->rubric_score >= 50, 'bg-red-50 text-red-800' => (float) $eval->rubric_score < 50])>Rubric {{ number_format((float) $eval->rubric_score, 1) }} / 100</span>
                            @if ($eval->document_markup_path)
                                <a href="{{ route('projects.download.marked-up', $project) }}" class="inline-flex items-center gap-1 text-sm font-medium text-forest-700 hover:underline"><x-glyph name="arrow-down-tray" class="h-4 w-4" /> Annotated draft</a>
                            @endif
                        </div>
                    </article>
                @empty
                    <p class="px-6 py-10 text-center text-sm text-muted">No assessments recorded yet.</p>
                @endforelse
            </section>
        @endif
    </div>
</div>
