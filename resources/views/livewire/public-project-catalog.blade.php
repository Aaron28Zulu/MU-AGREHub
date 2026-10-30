<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">

    <!-- Institutional Repository Masthead -->
    <div class="border-b border-[#e6e3da] dark:border-stone-800 pb-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="max-w-3xl space-y-3">
                <div class="inline-flex items-center gap-2 text-xs font-semibold tracking-wider uppercase text-[#1a3529] dark:text-emerald-400">
                    <span class="w-2 h-2 rounded-full bg-[#1a3529] dark:bg-emerald-400"></span>
                    <span>Mulungushi University</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-[#181a18] dark:text-[#faf8f5]">
                    Research Repository Interface System
                </h1>
                <p class="text-sm sm:text-base text-[#575c56] dark:text-stone-400 leading-relaxed">
                    A centralized institutional repository documenting student dissertations, research gaps, and emerging findings from every school, from agriculture and education to health, business, engineering and the social sciences.
                </p>
            </div>

            <!-- Repository Scope / Statistics Summary -->
            <div class="flex items-center gap-6 text-xs text-[#575c56] dark:text-stone-400 border-t md:border-t-0 md:border-l border-[#e6e3da] dark:border-stone-800 pt-4 md:pt-0 md:pl-6 shrink-0">
                <div>
                    <div class="text-2xl font-bold text-[#1a3529] dark:text-emerald-300 font-mono">{{ $projects->total() }}</div>
                    <div class="text-[11px] uppercase tracking-wider text-[#737971]">Archived Studies</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-[#1a3529] dark:text-emerald-300 font-mono">{{ count($categories) }}</div>
                    <div class="text-[11px] uppercase tracking-wider text-[#737971]">Thematic Fields</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Facet Bar -->
    <div class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-4 sm:p-5">
        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            <!-- Search Affordance -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#737971]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by research topic, author, supervisor, or abstract..."
                    class="w-full pl-10 pr-4 py-2 text-sm bg-white dark:bg-[#121412] text-[#1c1e21] dark:text-white rounded border border-[#e6e3da] dark:border-stone-700 focus:outline-none focus:border-[#1a3529] focus:ring-1 focus:ring-[#1a3529] transition placeholder-[#8c928a]"
                />
            </div>

            <!-- Category Filter -->
            <div class="w-full md:w-64">
                <select
                    wire:model.live="category"
                    class="w-full py-2 px-3 text-sm bg-white dark:bg-[#121412] text-[#1c1e21] dark:text-white rounded border border-[#e6e3da] dark:border-stone-700 focus:outline-none focus:border-[#1a3529] focus:ring-1 focus:ring-[#1a3529] transition"
                >
                    <option value="">All Thematic Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Completion Year Filter -->
            <div class="w-full md:w-44">
                <select
                    wire:model.live="year"
                    class="w-full py-2 px-3 text-sm bg-white dark:bg-[#121412] text-[#1c1e21] dark:text-white rounded border border-[#e6e3da] dark:border-stone-700 focus:outline-none focus:border-[#1a3529] focus:ring-1 focus:ring-[#1a3529] transition"
                >
                    <option value="">All Academic Years</option>
                    @for ($y = (int) date('Y'); $y >= 2018; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            @if ($search || $category || $year)
                <button
                    wire:click="$set('search', ''); $set('category', ''); $set('year', null);"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-[#737971] hover:text-[#991b1b] dark:text-stone-400 dark:hover:text-red-400 border border-transparent hover:border-[#e6e3da] rounded transition whitespace-nowrap"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>Reset Filters</span>
                </button>
            @endif
        </div>

        <!-- Filter context summary -->
        <div class="flex items-center justify-between pt-3 mt-3 border-t border-[#f0eee6] dark:border-stone-800/80 text-xs text-[#737971] dark:text-stone-400">
            <div>
                Showing <strong class="text-[#1c1e21] dark:text-stone-200">{{ $projects->total() }}</strong> verified research {{ $projects->total() === 1 ? 'record' : 'records' }}
                @if ($search)
                    matching &ldquo;<span class="font-medium text-[#1a3529] dark:text-emerald-400">{{ $search }}</span>&rdquo;
                @endif
            </div>
            <div class="text-[11px] uppercase tracking-wider font-mono">
                Peer-Reviewed Index
            </div>
        </div>
    </div>

    <!-- Academic Integrity & Restricted Document Notice -->
    <div class="rounded border border-[#e6e3da] dark:border-stone-800 bg-[#f9f8f3] dark:bg-[#181a18] p-4 text-xs text-[#575c56] dark:text-stone-400 flex items-start gap-3">
        <svg class="w-4 h-4 text-[#1a3529] dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="leading-relaxed">
            <strong class="text-[#181a18] dark:text-stone-200">Institutional Access Boundary:</strong> In accordance with university research governance, public access is restricted to verified metadata, research gaps, and summaries. Full manuscript downloads, methodologies, and raw datasets require institutional clearance.
        </div>
    </div>

    <!-- Research Records List (Content-First Scholarly Layout) -->
    @if ($projects->isEmpty())
        <div class="text-center py-16 px-6 bg-white dark:bg-[#181b18] rounded border border-[#e6e3da] dark:border-stone-800">
            <div class="w-12 h-12 rounded bg-[#f2f7f4] dark:bg-emerald-950/60 text-[#1a3529] dark:text-emerald-400 mx-auto flex items-center justify-center mb-3 border border-[#c5dacf]/50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-[#181a18] dark:text-white">No Research Records Found</h3>
            <p class="text-xs text-[#737971] dark:text-stone-400 mt-1 max-w-sm mx-auto">
                No archived studies match your criteria. Try adjusting keywords or clearing category and year filters.
            </p>
            @if ($search || $category || $year)
                <div class="mt-4">
                    <button
                        wire:click="$set('search', ''); $set('category', ''); $set('year', null);"
                        class="px-3.5 py-1.5 rounded text-xs font-medium text-[#1a3529] dark:text-emerald-300 bg-[#f2f7f4] dark:bg-emerald-950/50 border border-[#c5dacf]/60 hover:bg-[#e3ede6] transition"
                    >
                        Clear All Filters
                    </button>
                </div>
            @endif
        </div>
    @else
        <div class="space-y-5">
            @foreach ($projects as $project)
                <article class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-6 sm:p-7 hover:border-[#1a3529]/40 dark:hover:border-emerald-700/50 transition">
                    <!-- Record Header / Classification Bar -->
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-3 text-xs">
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Category Badge -->
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950/60 dark:text-emerald-300 border border-[#c5dacf]/60">
                                {{ $project->category->label() }}
                            </span>

                            <!-- Status Badge -->
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 border border-stone-200 dark:border-stone-700">
                                {{ $project->status->label() }}
                            </span>

                            @if ($project->completion_year)
                                <span class="text-stone-500 dark:text-stone-400 font-mono">
                                    Academic Year {{ $project->completion_year }}
                                </span>
                            @endif
                        </div>

                        <!-- Institutional Clearance Flag -->
                        <div class="inline-flex items-center gap-1.5 text-[11px] font-medium text-[#737971] dark:text-stone-400">
                            <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Restricted Manuscript &bull; Public Metadata Verified</span>
                        </div>
                    </div>

                    <!-- Research Title -->
                    <h2 class="text-lg sm:text-xl font-bold tracking-tight text-[#181a18] dark:text-[#faf8f5] leading-snug mb-2">
                        {{ $project->title }}
                    </h2>

                    <!-- Bibliographic Metadata Citation Row -->
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-[#636862] dark:text-stone-400 pb-4 border-b border-[#f0eee6] dark:border-stone-800/80">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[#8c928a]">Author:</span>
                            <span class="font-medium text-[#1c1e21] dark:text-stone-200">{{ $project->student?->name ?? 'Student Researcher' }}</span>
                        </div>

                        @if ($project->student?->department)
                            <div class="flex items-center gap-1.5">
                                <span class="text-[#8c928a]">Department:</span>
                                <span>{{ $project->student->department }}</span>
                            </div>
                        @endif

                        @if ($project->supervisor)
                            <div class="flex items-center gap-1.5">
                                <span class="text-[#8c928a]">Supervisor:</span>
                                <span>{{ $project->supervisor->name }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Abstract Excerpt -->
                    <div class="pt-4">
                        <h3 class="text-[11px] font-bold uppercase tracking-wider text-[#8c928a] dark:text-stone-500 mb-1.5">
                            Abstract &amp; Summary of Findings
                        </h3>
                        <p class="text-xs sm:text-sm text-[#495046] dark:text-stone-300 leading-relaxed max-w-5xl">
                            {{ $project->abstract }}
                        </p>
                    </div>

                    <!-- Identified Research Directions / Gaps -->
                    @if ($project->researchGaps->isNotEmpty())
                        <div class="mt-4 pt-3 border-l-2 border-[#1a3529] dark:border-emerald-600 pl-4 py-1 bg-[#faf8f5] dark:bg-[#121412] rounded-r">
                            <div class="text-[11px] font-bold uppercase tracking-wider text-[#1a3529] dark:text-emerald-400 mb-1 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span>Identified Future Research Directions &amp; Gaps</span>
                            </div>
                            <ul class="space-y-1 text-xs text-[#495046] dark:text-stone-300">
                                @foreach ($project->researchGaps->take(2) as $gap)
                                    <li class="flex items-start gap-2">
                                        <span class="text-[#1a3529] dark:text-emerald-400 font-bold">&bull;</span>
                                        <span>{{ $gap->extracted_gap_text ?? $gap->description ?? 'Identified thematic gap' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>

        <!-- Archival Pagination -->
        <div class="pt-6 border-t border-[#e6e3da] dark:border-stone-800">
            {{ $projects->links() }}
        </div>
    @endif

</div>
