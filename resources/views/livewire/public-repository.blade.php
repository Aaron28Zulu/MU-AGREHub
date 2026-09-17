<div
    x-data="{
        mobileFiltersOpen: false,
        activeModalProject: null,
        citationFormat: 'apa',
        copiedCitation: false,

        openModal(project) {
            this.activeModalProject = project;
            this.copiedCitation = false;
            document.body.classList.add('overflow-hidden');
        },
        closeModal() {
            this.activeModalProject = null;
            document.body.classList.remove('overflow-hidden');
        },
        copyCitation(text) {
            navigator.clipboard.writeText(text).then(() => {
                this.copiedCitation = true;
                setTimeout(() => { this.copiedCitation = false; }, 2000);
            });
        },
        generateCitation(format) {
            if (!this.activeModalProject) return '';
            const p = this.activeModalProject;
            const author = p.author || 'Author';
            const year = p.year || '{{ date('Y') }}';
            const title = p.title || 'Research Project';

            if (format === 'apa') {
                return `${author} (${year}). ${title}. School of Agriculture and Natural Resources, MUSAGRICA Research Repository.`;
            } else if (format === 'harvard') {
                return `${author}, ${year}. ${title}. MUSAGRICA Research Archive, School of Agriculture and Natural Resources.`;
            } else if (format === 'chicago') {
                return `${author}. \"${title}.\" School of Agriculture and Natural Resources, MUSAGRICA, ${year}.`;
            }
            return `${author} (${year}). ${title}.`;
        }
    }"
    @keydown.escape.window="closeModal()"
    class="min-h-screen py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6"
>

    <!-- 1. Page Header & Live Stats Strip -->
    <div class="border-b border-[#e6e3da] dark:border-stone-800 pb-5">
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs text-[#737971] dark:text-stone-400 mb-2" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-[#1a3529] dark:hover:text-emerald-400 transition">Home</a>
            <span>/</span>
            <span class="text-[#1c1e21] dark:text-stone-200 font-medium">Public Research Repository</span>
        </nav>

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#181a18] dark:text-[#faf8f5]">
                    Institutional Research Archive
                </h1>
                <div class="flex items-center gap-3 mt-1 text-xs text-[#575c56] dark:text-stone-400">
                    <span>
                        Showing <strong class="text-[#1c1e21] dark:text-stone-200">{{ $projects->total() }}</strong> of <strong class="text-[#1c1e21] dark:text-stone-200">{{ $totalCatalogCount }}</strong> Peer-Reviewed Theses
                    </span>
                    <span>&bull;</span>
                    <span class="inline-flex items-center gap-1 text-[#1a3529] dark:text-emerald-400 font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#1a3529] dark:bg-emerald-400"></span>
                        Verified Metadata Boundary
                    </span>
                </div>
            </div>

            <!-- Header Controls: Sort & View Toggle -->
            <div class="flex items-center flex-wrap gap-2.5">
                <!-- Mobile Filter Trigger -->
                <button
                    @click="mobileFiltersOpen = !mobileFiltersOpen"
                    type="button"
                    class="lg:hidden inline-flex items-center gap-1.5 px-3 py-1.5 rounded text-xs font-medium bg-white dark:bg-[#181b18] border border-[#e6e3da] dark:border-stone-700 text-[#1c1e21] dark:text-stone-200"
                >
                    <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Faceted Filters</span>
                    @if (count($selectedCategories) + count($selectedYears) + ($onlyWithGaps ? 1 : 0) + ($search ? 1 : 0) > 0)
                        <span class="w-4 h-4 rounded-full bg-[#1a3529] text-white text-[10px] flex items-center justify-center font-mono">
                            {{ count($selectedCategories) + count($selectedYears) + ($onlyWithGaps ? 1 : 0) + ($search ? 1 : 0) }}
                        </span>
                    @endif
                </button>

                <!-- Sort Dropdown -->
                <div class="flex items-center gap-1.5 text-xs text-[#737971] dark:text-stone-400 bg-white dark:bg-[#181b18] border border-[#e6e3da] dark:border-stone-700 rounded px-2.5 py-1.5">
                    <label for="repo-sort" class="font-medium shrink-0">Sort:</label>
                    <select
                        id="repo-sort"
                        wire:model.live="sort"
                        class="bg-transparent border-0 text-xs font-semibold text-[#1c1e21] dark:text-white p-0 focus:ring-0 cursor-pointer"
                    >
                        <option value="latest">Latest Submissions</option>
                        <option value="oldest">Oldest Submissions</option>
                        <option value="title_asc">Title (A &ndash; Z)</option>
                        <option value="year_desc">Academic Year (Desc)</option>
                    </select>
                </div>

                <!-- View Switcher (Grid vs Compact List) -->
                <div class="inline-flex rounded border border-[#e6e3da] dark:border-stone-700 bg-white dark:bg-[#181b18] p-0.5">
                    <button
                        type="button"
                        wire:click="setViewMode('grid')"
                        class="px-2.5 py-1 rounded text-xs font-medium transition inline-flex items-center gap-1 {{ $viewMode === 'grid' ? 'bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950 dark:text-emerald-300 font-semibold' : 'text-[#737971] hover:text-[#1c1e21] dark:text-stone-400' }}"
                        title="Grid View"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span class="hidden sm:inline">Grid</span>
                    </button>
                    <button
                        type="button"
                        wire:click="setViewMode('list')"
                        class="px-2.5 py-1 rounded text-xs font-medium transition inline-flex items-center gap-1 {{ $viewMode === 'list' ? 'bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950 dark:text-emerald-300 font-semibold' : 'text-[#737971] hover:text-[#1c1e21] dark:text-stone-400' }}"
                        title="Compact List View"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <span class="hidden sm:inline">List</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Active Filter Tags Row -->
        @php
            $hasActiveFilters = !empty($search) || !empty($selectedCategories) || !empty($selectedYears) || $onlyWithGaps;
        @endphp

        @if ($hasActiveFilters)
            <div class="flex items-center flex-wrap gap-2 pt-3 mt-3 border-t border-[#f0eee6] dark:border-stone-800 text-xs">
                <span class="text-[11px] uppercase tracking-wider text-[#737971] font-semibold">Active Facets:</span>

                @if (!empty($search))
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-stone-100 dark:bg-stone-800 text-[#1c1e21] dark:text-stone-200 border border-stone-200 dark:border-stone-700">
                        <span>Query: &ldquo;{{ $search }}&rdquo;</span>
                        <button type="button" wire:click="$set('search', '')" class="text-stone-400 hover:text-red-500 font-bold ml-0.5">&times;</button>
                    </span>
                @endif

                @foreach ($selectedCategories as $catVal)
                    @php
                        $matchedCategory = collect($categories)->firstWhere('value', $catVal);
                    @endphp
                    @if ($matchedCategory)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950 dark:text-emerald-300 border border-[#c5dacf]/60">
                            <span>{{ $matchedCategory->label() }}</span>
                            <button type="button" wire:click="removeCategory('{{ $catVal }}')" class="text-[#1a3529] hover:text-red-500 font-bold ml-0.5">&times;</button>
                        </span>
                    @endif
                @endforeach

                @foreach ($selectedYears as $yearVal)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-stone-100 dark:bg-stone-800 text-[#1c1e21] dark:text-stone-200 border border-stone-200 dark:border-stone-700">
                        <span>Year: {{ $yearVal }}</span>
                        <button type="button" wire:click="removeYear('{{ $yearVal }}')" class="text-stone-400 hover:text-red-500 font-bold ml-0.5">&times;</button>
                    </span>
                @endforeach

                @if ($onlyWithGaps)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-zinc-100 dark:bg-stone-800 text-zinc-700 dark:text-stone-300 border border-zinc-200 dark:border-stone-700">
                        <span>Open Research Gaps Only</span>
                        <button type="button" wire:click="toggleOnlyWithGaps" class="text-zinc-400 hover:text-red-500 font-bold ml-0.5">&times;</button>
                    </span>
                @endif

                <button
                    type="button"
                    wire:click="resetFilters"
                    class="text-xs font-semibold text-[#991b1b] dark:text-red-400 hover:underline ml-2"
                >
                    Clear All Filters
                </button>
            </div>
        @endif
    </div>

    <!-- 2. Main 2-Column Faceted Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">

        <!-- Left Sidebar: Faceted Filters (Sticky) -->
        <aside
            :class="mobileFiltersOpen ? 'block' : 'hidden lg:block'"
            class="lg:col-span-1 bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-5 space-y-6 lg:sticky lg:top-20 shadow-xs"
        >
            <div class="flex items-center justify-between pb-3 border-b border-[#f0eee6] dark:border-stone-800">
                <h2 class="text-xs font-bold uppercase tracking-wider text-[#181a18] dark:text-white flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#1a3529] dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Faceted Filter Index</span>
                </h2>
                @if ($hasActiveFilters)
                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="text-[11px] font-medium text-[#737971] hover:text-[#991b1b] transition"
                    >
                        Reset
                    </button>
                @endif
            </div>

            <!-- Real-time Search Affordance -->
            <div class="space-y-1.5">
                <label for="sidebar-search" class="block text-[11px] font-bold uppercase tracking-wider text-[#737971] dark:text-stone-400">
                    Search Topics &amp; Abstract
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input
                        type="text"
                        id="sidebar-search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Keyword, student, supervisor..."
                        class="w-full pl-9 pr-7 py-2 text-xs bg-[#faf8f5] dark:bg-[#121412] text-[#1c1e21] dark:text-white rounded border border-[#e6e3da] dark:border-stone-700 focus:outline-none focus:border-[#1a3529] focus:ring-1 focus:ring-[#1a3529] transition placeholder-[#8c928a]"
                    />
                    @if ($search)
                        <button
                            type="button"
                            wire:click="$set('search', '')"
                            class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-stone-400 hover:text-stone-600"
                        >
                            &times;
                        </button>
                    @endif
                </div>
            </div>

            <!-- Thematic Category Facets -->
            <div class="space-y-2 pt-4 border-t border-[#f0eee6] dark:border-stone-800">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-[#737971] dark:text-stone-400">
                    Thematic Disciplines
                </label>
                <div class="space-y-2">
                    @foreach ($categories as $cat)
                        @php
                            $catCount = $categoryCounts[$cat->value] ?? 0;
                            $isChecked = in_array($cat->value, $selectedCategories, true);

                            // Category badge colors
                            $dotColor = match($cat->value) {
                                'crop_science' => 'bg-emerald-600',
                                'agribusiness' => 'bg-amber-600',
                                'animal_science' => 'bg-sky-600',
                                'plant_protection' => 'bg-teal-600',
                                default => 'bg-stone-600',
                            };
                        @endphp
                        <label class="flex items-center justify-between gap-2 p-1.5 rounded hover:bg-zinc-50 dark:hover:bg-[#121412] cursor-pointer transition text-xs group">
                            <div class="flex items-center gap-2 min-w-0">
                                <input
                                    type="checkbox"
                                    value="{{ $cat->value }}"
                                    wire:model.live="selectedCategories"
                                    class="rounded border-zinc-300 dark:border-stone-700 text-emerald-700 focus:ring-emerald-600 dark:bg-stone-900"
                                />
                                <span class="w-2 h-2 rounded-full {{ $dotColor }} shrink-0"></span>
                                <span class="truncate {{ $isChecked ? 'font-semibold text-emerald-900 dark:text-emerald-400' : 'text-zinc-600 dark:text-stone-300' }}">
                                    {{ $cat->label() }}
                                </span>
                            </div>
                            <span class="text-[11px] font-mono text-zinc-400 dark:text-stone-400 bg-zinc-100 dark:bg-stone-800 px-1.5 py-0.5 rounded">
                                {{ $catCount }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Academic Year Multi-Select Facet -->
            <div class="space-y-2 pt-4 border-t border-zinc-100 dark:border-stone-800">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-stone-400">
                    Academic Year
                </label>
                <div class="grid grid-cols-2 gap-1.5">
                    @foreach ($availableYears as $year)
                        @php
                            $isYearChecked = in_array((string) $year, array_map('strval', $selectedYears), true);
                        @endphp
                        <label class="flex items-center gap-2 p-1.5 rounded hover:bg-zinc-50 dark:hover:bg-[#121412] cursor-pointer text-xs">
                            <input
                                type="checkbox"
                                value="{{ $year }}"
                                wire:model.live="selectedYears"
                                class="rounded border-zinc-300 dark:border-stone-700 text-emerald-700 focus:ring-emerald-600 dark:bg-stone-900"
                            />
                            <span class="font-mono {{ $isYearChecked ? 'font-bold text-emerald-900 dark:text-emerald-400' : 'text-zinc-600 dark:text-stone-300' }}">
                                {{ $year }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Research Gap Toggle Filter -->
            <div class="pt-4 border-t border-zinc-100 dark:border-stone-800">
                <label class="flex items-start gap-2.5 p-2.5 rounded-lg bg-zinc-50/70 dark:bg-stone-900/40 border border-zinc-200/70 dark:border-stone-800 hover:bg-zinc-100/60 dark:hover:bg-stone-800/60 transition cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model.live="onlyWithGaps"
                        class="mt-0.5 rounded border-zinc-300 dark:border-stone-700 text-emerald-700 focus:ring-emerald-600 dark:bg-stone-900"
                    />
                    <div class="text-xs">
                        <span class="font-medium text-zinc-800 dark:text-stone-200 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-zinc-400 dark:text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.516 0c.85.493 1.508 1.333 1.508 2.316V18" />
                            </svg>
                            <span>Open Gaps Only</span>
                        </span>
                        <p class="text-[11px] text-zinc-500 dark:text-stone-400 mt-0.5 leading-normal">
                            Filter to dissertations with indexed future directions.
                        </p>
                    </div>
                </label>
            </div>

            <!-- Repository Clearance Info Box -->
            <div class="pt-4 border-t border-[#f0eee6] dark:border-stone-800 text-[11px] text-[#737971] dark:text-stone-400 space-y-1">
                <div class="font-semibold text-[#181a18] dark:text-stone-300 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Ethics &amp; IP Protection</span>
                </div>
                <p class="leading-relaxed">
                    Public metadata is openly discoverable. Full manuscript downloads require supervisor credentials.
                </p>
            </div>
        </aside>

        <!-- Right Area: Results Section & Research Cards -->
        <main class="lg:col-span-3 space-y-5">

            <!-- Skeleton Loading State (wire:loading.delay) -->
            <div wire:loading.delay class="w-full space-y-4">
                <div class="animate-pulse space-y-4">
                    @for ($i = 0; $i < 3; $i++)
                        <div class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-6 space-y-3">
                            <div class="flex justify-between">
                                <div class="h-4 bg-stone-200 dark:bg-stone-700 rounded w-1/4"></div>
                                <div class="h-4 bg-stone-200 dark:bg-stone-700 rounded w-1/6"></div>
                            </div>
                            <div class="h-6 bg-stone-200 dark:bg-stone-700 rounded w-3/4"></div>
                            <div class="h-3 bg-stone-200 dark:bg-stone-700 rounded w-1/2"></div>
                            <div class="space-y-1.5 pt-2">
                                <div class="h-3 bg-stone-200 dark:bg-stone-700 rounded w-full"></div>
                                <div class="h-3 bg-stone-200 dark:bg-stone-700 rounded w-5/6"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Results Output -->
            <div wire:loading.delay.remove>
                @if ($projects->isEmpty())
                    <!-- Empty State -->
                    <div class="text-center py-16 px-6 bg-white dark:bg-[#181b18] rounded-lg border border-dashed border-[#e6e3da] dark:border-stone-800">
                        <div class="w-12 h-12 rounded bg-[#f2f7f4] dark:bg-emerald-950/60 text-[#1a3529] dark:text-emerald-400 mx-auto flex items-center justify-center mb-3 border border-[#c5dacf]/60">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-[#181a18] dark:text-white">No Matching Dissertations Found</h3>
                        <p class="text-xs text-[#737971] dark:text-stone-400 mt-1 max-w-sm mx-auto">
                            Try adjusting your keyword query, unchecking restrictive disciplines, or clearing the academic year filter.
                        </p>
                        <div class="mt-5">
                            <button
                                type="button"
                                wire:click="resetFilters"
                                class="px-4 py-2 rounded text-xs font-semibold text-white bg-[#1a3529] hover:bg-[#224233] transition"
                            >
                                Reset All Filters
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Research Cards Container: Grid vs List -->
                    <div class="{{ $viewMode === 'grid' ? 'grid grid-cols-1 xl:grid-cols-2 gap-5' : 'space-y-4' }}">
                        @foreach ($projects as $project)
                            @php
                                $catClass = match($project->category->value ?? '') {
                                    'crop_science' => 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-600/20 dark:bg-emerald-950/60 dark:text-emerald-300',
                                    'agribusiness' => 'bg-amber-50 text-amber-800 ring-1 ring-amber-600/20 dark:bg-amber-950/60 dark:text-amber-300',
                                    'animal_science' => 'bg-sky-50 text-sky-800 ring-1 ring-sky-600/20 dark:bg-sky-950/60 dark:text-sky-300',
                                    'plant_protection' => 'bg-teal-50 text-teal-800 ring-1 ring-teal-600/20 dark:bg-teal-950/60 dark:text-teal-300',
                                    default => 'bg-stone-100 text-stone-700 ring-1 ring-stone-600/20 dark:bg-stone-800 dark:text-stone-300',
                                };
                            @endphp

                            <article class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-6 flex flex-col justify-between hover:border-[#1a3529]/40 dark:hover:border-emerald-700/60 transition group shadow-xs">
                                <div>
                                    <!-- Header Row: Category Badge + Year Tag + Access Badge -->
                                    <div class="flex items-center justify-between gap-2 mb-3">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold {{ $catClass }}">
                                                {{ $project->category->label() }}
                                            </span>

                                            @if ($project->completion_year)
                                                <span class="text-xs font-mono text-[#737971] dark:text-stone-400 bg-stone-100 dark:bg-stone-800 px-2 py-0.5 rounded">
                                                    Class {{ $project->completion_year }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Right-aligned Access Badge -->
                                        <div
                                            class="inline-flex items-center gap-1 text-[11px] font-medium text-stone-500 dark:text-stone-400 bg-stone-50 dark:bg-stone-900 px-2 py-0.5 rounded border border-stone-200 dark:border-stone-700 shrink-0"
                                            title="Public abstract verified; manuscript protected under institutional governance."
                                        >
                                            <svg class="w-3 h-3 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            <span>Metadata Verified</span>
                                        </div>
                                    </div>

                                    <!-- Research Title -->
                                    <h2
                                        @click="openModal({
                                            title: {{ json_encode($project->title) }},
                                            category: {{ json_encode($project->category->label()) }},
                                            year: {{ json_encode($project->completion_year) }},
                                            author: {{ json_encode($project->student?->name ?? 'Student Researcher') }},
                                            department: {{ json_encode($project->student?->department ?? 'School of Agriculture') }},
                                            supervisor: {{ json_encode($project->supervisor?->name ?? 'Faculty Supervisor') }},
                                            abstract: {{ json_encode($project->abstract) }},
                                            gaps: {{ json_encode($project->researchGaps->pluck('extracted_gap_text')->toArray()) }}
                                        })"
                                        class="font-semibold text-zinc-900 dark:text-white text-base leading-snug hover:text-emerald-700 dark:hover:text-emerald-400 transition cursor-pointer mb-2"
                                    >
                                        {{ $project->title }}
                                    </h2>

                                    <!-- Inline Metadata Row with Two-Tone Treatment -->
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs mb-3 pb-3 border-b border-zinc-100 dark:border-stone-800">
                                        <!-- Author -->
                                        <div class="inline-flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-zinc-400 dark:text-stone-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <span class="text-zinc-400 dark:text-stone-500 font-normal">Author:</span>
                                            <span class="text-zinc-700 dark:text-stone-200 font-medium">{{ $project->student?->name ?? 'Student Researcher' }}</span>
                                        </div>

                                        <!-- Supervisor -->
                                        @if ($project->supervisor)
                                            <span class="text-zinc-300 dark:text-stone-700 hidden sm:inline">&bull;</span>
                                            <div class="inline-flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-zinc-400 dark:text-stone-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                                <span class="text-zinc-400 dark:text-stone-500 font-normal">Supervisor:</span>
                                                <span class="text-zinc-700 dark:text-stone-200 font-medium">{{ $project->supervisor->name }}</span>
                                            </div>
                                        @endif

                                        <!-- Department -->
                                        @if ($project->student?->department)
                                            <span class="text-zinc-300 dark:text-stone-700 hidden sm:inline">&bull;</span>
                                            <div class="inline-flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-zinc-400 dark:text-stone-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                <span class="text-zinc-400 dark:text-stone-500 font-normal">Dept:</span>
                                                <span class="text-zinc-700 dark:text-stone-200 font-medium">{{ $project->student->department }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Truncated Abstract Preview (max 3 lines) -->
                                    <div class="text-xs text-zinc-600 dark:text-stone-300 leading-relaxed mb-3">
                                        <p class="line-clamp-3">
                                            {{ $project->abstract }}
                                        </p>
                                        <button
                                            type="button"
                                            @click="openModal({
                                                title: {{ json_encode($project->title) }},
                                                category: {{ json_encode($project->category->label()) }},
                                                year: {{ json_encode($project->completion_year) }},
                                                author: {{ json_encode($project->student?->name ?? 'Student Researcher') }},
                                                department: {{ json_encode($project->student?->department ?? 'School of Agriculture') }},
                                                supervisor: {{ json_encode($project->supervisor?->name ?? 'Faculty Supervisor') }},
                                                abstract: {{ json_encode($project->abstract) }},
                                                gaps: {{ json_encode($project->researchGaps->pluck('extracted_gap_text')->toArray()) }}
                                            })"
                                            class="text-zinc-500 hover:text-zinc-900 dark:text-stone-400 dark:hover:text-stone-100 font-medium text-xs inline-flex items-center gap-1 mt-1.5 transition cursor-pointer"
                                        >
                                            <span>Read Full Abstract &amp; Gaps</span>
                                            <svg class="w-3 h-3 text-zinc-400 dark:text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <!-- Understated Future Research Directions & Gaps Section -->
                                    @if ($project->researchGaps->isNotEmpty())
                                        <div class="bg-zinc-50/70 dark:bg-stone-900/40 border border-zinc-200/70 dark:border-stone-800 rounded-lg p-3 mt-3">
                                            <div class="text-xs font-medium text-zinc-500 dark:text-stone-400 flex items-center gap-1.5 mb-1.5">
                                                <svg class="w-3.5 h-3.5 text-zinc-400 dark:text-stone-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="9" stroke-width="1.5"></circle>
                                                    <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" stroke-width="1.5"></polygon>
                                                </svg>
                                                <span>Future Research Directions &amp; Gaps</span>
                                            </div>
                                            <ul class="list-disc list-inside marker:text-zinc-300 dark:marker:text-stone-600 space-y-1 text-xs text-zinc-600 dark:text-stone-300 leading-relaxed">
                                                @foreach ($project->researchGaps->take(2) as $gap)
                                                    <li class="line-clamp-2">
                                                        <span>{{ $gap->extracted_gap_text ?? $gap->description ?? 'Identified thematic gap' }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <!-- Actions Row -->
                                    <div class="flex items-center justify-between pt-4 mt-3 border-t border-zinc-100 dark:border-stone-800 gap-2">
                                        <button
                                            type="button"
                                            @click="openModal({
                                                title: {{ json_encode($project->title) }},
                                                category: {{ json_encode($project->category->label()) }},
                                                year: {{ json_encode($project->completion_year) }},
                                                author: {{ json_encode($project->student?->name ?? 'Student Researcher') }},
                                                department: {{ json_encode($project->student?->department ?? 'School of Agriculture') }},
                                                supervisor: {{ json_encode($project->supervisor?->name ?? 'Faculty Supervisor') }},
                                                abstract: {{ json_encode($project->abstract) }},
                                                gaps: {{ json_encode($project->researchGaps->pluck('extracted_gap_text')->toArray()) }}
                                            })"
                                            class="bg-white hover:bg-zinc-50 text-zinc-700 dark:bg-stone-800 dark:hover:bg-stone-700 dark:text-stone-200 border border-zinc-200 dark:border-stone-700 text-xs font-medium py-1.5 px-3 rounded-lg shadow-xs inline-flex items-center gap-1.5 transition"
                                        >
                                            <svg class="w-3.5 h-3.5 text-zinc-400 dark:text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>View Metadata Summary</span>
                                        </button>

                                        <a
                                            href="{{ route('login') }}"
                                            class="text-zinc-500 hover:text-zinc-800 dark:text-stone-400 dark:hover:text-stone-200 text-xs font-medium py-1.5 px-2.5 inline-flex items-center gap-1 transition"
                                            title="Sign in with institutional credentials to request full manuscript clearance"
                                        >
                                            <svg class="w-3 h-3 text-zinc-400 dark:text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            <span>Request Clearance</span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <!-- Archival Pagination -->
                    <div class="pt-6 border-t border-[#e6e3da] dark:border-stone-800">
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>

        </main>
    </div>

    <!-- 4. Metadata Detail Modal Drawer (Alpine.js) -->
    <div
        x-show="activeModalProject !== null"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <!-- Backdrop -->
        <div
            @click="closeModal()"
            class="fixed inset-0 bg-stone-900/60 backdrop-blur-xs transition-opacity"
        ></div>

        <!-- Modal Box -->
        <div class="min-h-full flex items-center justify-center p-4 sm:p-6">
            <div
                x-show="activeModalProject !== null"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                class="relative bg-white dark:bg-[#181b18] border border-[#e6e3da] dark:border-stone-800 rounded-lg max-w-3xl w-full p-6 sm:p-8 text-left shadow-2xl space-y-5"
                @click.stop
            >
                <!-- Modal Top Header -->
                <div class="flex items-start justify-between gap-4 pb-4 border-b border-[#e6e3da] dark:border-stone-800">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950 dark:text-emerald-300 border border-[#c5dacf]/60" x-text="activeModalProject?.category"></span>
                            <span class="text-xs font-mono text-[#737971]" x-show="activeModalProject?.year" x-text="'Academic Class ' + activeModalProject?.year"></span>
                            <span class="inline-flex items-center gap-1 text-[11px] text-stone-500 bg-stone-100 dark:bg-stone-800 px-2 py-0.5 rounded border border-stone-200 dark:border-stone-700">
                                🔒 Restricted Manuscript
                            </span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-[#181a18] dark:text-white leading-snug" x-text="activeModalProject?.title"></h3>
                    </div>
                    <button
                        @click="closeModal()"
                        type="button"
                        class="text-stone-400 hover:text-stone-600 dark:hover:text-stone-200 p-1.5 rounded hover:bg-stone-100 dark:hover:bg-stone-800"
                        aria-label="Close modal"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Content Scrollable Body -->
                <div class="space-y-5 max-h-[65vh] overflow-y-auto pr-1">

                    <!-- Bibliographic Metadata Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs bg-[#faf8f5] dark:bg-[#121412] p-3.5 rounded border border-[#e6e3da] dark:border-stone-800">
                        <div>
                            <span class="text-[#737971] block">Student Researcher:</span>
                            <strong class="text-[#181a18] dark:text-stone-200 font-medium" x-text="activeModalProject?.author"></strong>
                        </div>
                        <div>
                            <span class="text-[#737971] block">Faculty Supervisor:</span>
                            <strong class="text-[#181a18] dark:text-stone-200 font-medium" x-text="activeModalProject?.supervisor"></strong>
                        </div>
                        <div>
                            <span class="text-[#737971] block">Academic Department:</span>
                            <span class="text-[#181a18] dark:text-stone-200" x-text="activeModalProject?.department"></span>
                        </div>
                    </div>

                    <!-- Unclipped Abstract -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#737971] dark:text-stone-400 mb-1.5">
                            Comprehensive Abstract &amp; Study Scope
                        </h4>
                        <p class="text-xs sm:text-sm text-[#495046] dark:text-stone-300 leading-relaxed whitespace-pre-wrap" x-text="activeModalProject?.abstract"></p>
                    </div>

                    <!-- Identified Research Gaps & Recommendations -->
                    <template x-if="activeModalProject?.gaps && activeModalProject?.gaps.length > 0">
                        <div class="p-4 rounded-lg border border-zinc-200/70 bg-zinc-50/70 dark:bg-stone-900/40 dark:border-stone-800">
                            <h4 class="text-xs font-medium text-zinc-500 dark:text-stone-400 mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-zinc-400 dark:text-stone-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke-width="1.5"></circle>
                                    <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" stroke-width="1.5"></polygon>
                                </svg>
                                <span>Future Research Directions &amp; Gaps</span>
                            </h4>
                            <ul class="list-disc list-inside marker:text-zinc-300 dark:marker:text-stone-600 space-y-1.5 text-xs text-zinc-600 dark:text-stone-300 leading-relaxed">
                                <template x-for="gap in activeModalProject?.gaps" :key="gap">
                                    <li class="leading-relaxed">
                                        <span x-text="gap"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>

                    <!-- "How to Cite" Citation Generation Snippet Box -->
                    <div class="bg-[#faf8f5] dark:bg-[#121412] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-4 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-[#181a18] dark:text-white">Cite This Research</span>
                                <div class="inline-flex rounded border border-[#e6e3da] dark:border-stone-700 bg-white dark:bg-[#181b18] p-0.5 text-[10px]">
                                    <button
                                        type="button"
                                        @click="citationFormat = 'apa'"
                                        :class="citationFormat === 'apa' ? 'bg-[#1a3529] text-white font-bold' : 'text-[#737971] hover:text-black'"
                                        class="px-2 py-0.5 rounded transition"
                                    >
                                        APA
                                    </button>
                                    <button
                                        type="button"
                                        @click="citationFormat = 'harvard'"
                                        :class="citationFormat === 'harvard' ? 'bg-[#1a3529] text-white font-bold' : 'text-[#737971] hover:text-black'"
                                        class="px-2 py-0.5 rounded transition"
                                    >
                                        Harvard
                                    </button>
                                    <button
                                        type="button"
                                        @click="citationFormat = 'chicago'"
                                        :class="citationFormat === 'chicago' ? 'bg-[#1a3529] text-white font-bold' : 'text-[#737971] hover:text-black'"
                                        class="px-2 py-0.5 rounded transition"
                                    >
                                        Chicago
                                    </button>
                                </div>
                            </div>

                            <button
                                type="button"
                                @click="copyCitation(generateCitation(citationFormat))"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-[#1a3529] dark:text-emerald-400 hover:underline"
                            >
                                <svg x-show="!copiedCitation" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <svg x-show="copiedCitation" class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="copiedCitation ? 'Copied to Clipboard!' : 'Copy Citation'"></span>
                            </button>
                        </div>

                        <div
                            class="p-2.5 bg-white dark:bg-[#181b18] rounded border border-[#e6e3da] dark:border-stone-700 text-xs font-mono text-[#495046] dark:text-stone-300 select-all leading-relaxed"
                            x-text="generateCitation(citationFormat)"
                        ></div>
                    </div>

                    <!-- Academic Clearance Governance Notice -->
                    <div class="text-[11px] text-[#737971] dark:text-stone-400 bg-stone-50 dark:bg-stone-900 p-3 rounded border border-stone-200 dark:border-stone-700 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-stone-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>
                            <strong>Institutional Policy Reminder:</strong> Full manuscript downloads, methodologies, and raw trial datasets are strictly restricted under MUSAGRICA institutional ethics governance. Verified academic supervisors and directors may sign in to request clearance.
                        </span>
                    </div>

                </div>

                <!-- Modal Footer Actions -->
                <div class="pt-4 border-t border-[#e6e3da] dark:border-stone-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <a
                        href="{{ route('login') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded text-xs font-semibold text-white bg-[#1a3529] hover:bg-[#224233] transition"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Request Manuscript Clearance (Institutional Sign In)</span>
                    </a>

                    <button
                        @click="closeModal()"
                        type="button"
                        class="w-full sm:w-auto px-4 py-2 rounded text-xs font-medium text-[#1c1e21] dark:text-stone-200 bg-stone-100 dark:bg-stone-800 hover:bg-stone-200 dark:hover:bg-stone-700 transition"
                    >
                        Close Summary
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
