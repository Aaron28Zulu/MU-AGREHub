<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-[#e6e3da] dark:border-stone-800">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wider text-[#737971] dark:text-stone-400 mb-1">
                Student Research Workspace
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-[#181a18] dark:text-white">
                Register Dissertation Research Proposal
            </h1>
            <p class="text-xs sm:text-sm text-[#575c56] dark:text-stone-400 mt-1">
                Initiate your postgraduate dissertation by registering your study topic, problem statement, methodology, and supervisor allocation request.
            </p>
        </div>
        <a
            href="{{ route('student.dashboard') }}"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium text-[#1c1e21] dark:text-stone-200 bg-white dark:bg-[#181b18] border border-[#e6e3da] dark:border-stone-700 rounded hover:bg-[#faf8f5] dark:hover:bg-stone-800 transition shadow-xs"
        >
            &larr; Back to Workspace
        </a>
    </div>

    <!-- Duplication Prevention Check Banner -->
    <div class="p-4 rounded-lg bg-emerald-50/80 dark:bg-emerald-950/20 border border-emerald-200/80 dark:border-emerald-900/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300 flex items-center justify-center shrink-0 border border-emerald-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-900 dark:text-emerald-200">
                    Institutional Duplication Prevention &amp; Academic Gap Check
                </h3>
                <p class="text-xs text-emerald-800/90 dark:text-emerald-300/80 mt-0.5">
                    Before registering, inspect the repository to identify open research gaps and ensure your study tackles unique empirical challenges.
                </p>
            </div>
        </div>
        <a
            href="{{ route('repository') }}"
            target="_blank"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded text-white bg-emerald-700 hover:bg-emerald-800 shrink-0 transition"
        >
            <span>Explore Gaps &rarr;</span>
        </a>
    </div>

    @if (session()->has('success'))
        <div class="p-4 rounded bg-[#f2f7f4] border border-[#c5dacf] text-[#1a3529] dark:bg-emerald-950/40 dark:border-emerald-800 dark:text-emerald-300 text-xs font-medium flex items-center gap-2.5">
            <svg class="w-4 h-4 text-[#1a3529] dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form wire:submit="submit" class="space-y-6">

        <!-- 1. Topic & Thematic Category -->
        <div class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-6 sm:p-8 space-y-6">
            <div class="border-b border-[#f0eee6] dark:border-stone-800 pb-3">
                <h3 class="text-sm font-bold text-[#181a18] dark:text-white">1. Study Focus &amp; Classification</h3>
                <p class="text-xs text-[#737971] dark:text-stone-400 mt-0.5">Define your dissertation title and thematic discipline.</p>
            </div>

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-bold uppercase tracking-wider text-[#737971] dark:text-stone-400 mb-1.5">
                    Proposed Research Title <span class="text-red-600">*</span>
                </label>
                <input
                    type="text"
                    id="title"
                    wire:model="title"
                    placeholder="e.g., Evaluating Biochar and Legume Intercropping for Soil Nutrient Dynamics in Sub-Saharan Agrosystems"
                    class="w-full rounded border border-[#e6e3da] dark:border-stone-700 dark:bg-[#121412] dark:text-white text-sm p-3 focus:outline-none focus:border-[#1a3529] focus:ring-1 focus:ring-[#1a3529] transition placeholder-[#8c928a]"
                />
                @error('title')
                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category & Supervisor Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category -->
                <div>
                    <label for="category" class="block text-xs font-bold uppercase tracking-wider text-[#737971] dark:text-stone-400 mb-1.5">
                        Research Thematic Domain <span class="text-red-600">*</span>
                    </label>
                    <select
                        id="category"
                        wire:model="category"
                        class="w-full rounded border border-[#e6e3da] dark:border-stone-700 dark:bg-[#121412] dark:text-white text-sm p-3 focus:outline-none focus:border-[#1a3529] focus:ring-1 focus:ring-[#1a3529] transition"
                    >
                        <option value="">Select Academic Category</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preferred Supervisor -->
                <div>
                    <label for="supervisor_id" class="block text-xs font-bold uppercase tracking-wider text-[#737971] dark:text-stone-400 mb-1.5">
                        Supervisor Allocation Preference <span class="text-stone-400 font-normal normal-case">(Optional)</span>
                    </label>
                    <select
                        id="supervisor_id"
                        wire:model="supervisor_id"
                        class="w-full rounded border border-[#e6e3da] dark:border-stone-700 dark:bg-[#121412] dark:text-white text-sm p-3 focus:outline-none focus:border-[#1a3529] focus:ring-1 focus:ring-[#1a3529] transition"
                    >
                        <option value="">Leave unassigned (Departmental Allocation)</option>
                        @foreach ($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}">{{ $supervisor->name }} ({{ $supervisor->department ?? 'Faculty' }})</option>
                        @endforeach
                    </select>
                    @error('supervisor_id')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- 2. Problem Statement & Methodology -->
        <div class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-6 sm:p-8 space-y-6">
            <div class="border-b border-[#f0eee6] dark:border-stone-800 pb-3">
                <h3 class="text-sm font-bold text-[#181a18] dark:text-white">2. Scope, Problem Statement &amp; Methodology</h3>
                <p class="text-xs text-[#737971] dark:text-stone-400 mt-0.5">Provide academic justification and proposed experimental design.</p>
            </div>

            <!-- Background & Problem Statement -->
            <div>
                <label for="abstract" class="block text-xs font-bold uppercase tracking-wider text-[#737971] dark:text-stone-400 mb-1.5">
                    Background Literature &amp; Problem Statement <span class="text-red-600">*</span>
                </label>
                <textarea
                    id="abstract"
                    wire:model="abstract"
                    rows="5"
                    placeholder="Describe the institutional or agricultural problem, contextual rationale, and relevant literature gaps motivating this research..."
                    class="w-full rounded border border-[#e6e3da] dark:border-stone-700 dark:bg-[#121412] dark:text-white text-sm p-3 focus:outline-none focus:border-[#1a3529] focus:ring-1 focus:ring-[#1a3529] transition leading-relaxed placeholder-[#8c928a]"
                ></textarea>
                @error('abstract')
                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-stone-400 mt-1">Minimum 50 characters required for academic indexing.</p>
            </div>

            <!-- Proposed Objectives & Field Methodology Outline -->
            <div>
                <label for="objectives_methodology" class="block text-xs font-bold uppercase tracking-wider text-[#737971] dark:text-stone-400 mb-1.5">
                    Proposed Objectives &amp; Field Methodology Outline <span class="text-stone-400 font-normal normal-case">(Optional)</span>
                </label>
                <textarea
                    id="objectives_methodology"
                    wire:model="objectives_methodology"
                    rows="4"
                    placeholder="Detail your primary experimental objectives, sampling protocol, data collection instruments, and statistical approaches..."
                    class="w-full rounded border border-[#e6e3da] dark:border-stone-700 dark:bg-[#121412] dark:text-white text-sm p-3 focus:outline-none focus:border-[#1a3529] focus:ring-1 focus:ring-[#1a3529] transition leading-relaxed placeholder-[#8c928a]"
                ></textarea>
                @error('objectives_methodology')
                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- 3. Addressed Research Gaps -->
        <div class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-6 sm:p-8 space-y-4">
            <div class="flex items-center justify-between border-b border-[#f0eee6] dark:border-stone-800 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-[#181a18] dark:text-white">3. Addressed Research Gaps</h3>
                    <p class="text-xs text-[#737971] dark:text-stone-400 mt-0.5">Specify concrete institutional or scientific gaps this study aims to resolve.</p>
                </div>
                <button
                    type="button"
                    wire:click="addGap"
                    class="inline-flex items-center gap-1 text-xs font-semibold text-[#1a3529] dark:text-emerald-400 hover:underline"
                >
                    + Add Another Gap
                </button>
            </div>

            <div class="space-y-3">
                @foreach ($research_gaps as $index => $gap)
                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            wire:model="research_gaps.{{ $index }}"
                            placeholder="e.g., Lack of baseline soil microbiota data across low-altitude rainfed zones"
                            class="flex-1 rounded border border-[#e6e3da] dark:border-stone-700 dark:bg-[#121412] dark:text-white text-xs sm:text-sm p-2.5 focus:outline-none focus:border-[#1a3529] focus:ring-1 focus:ring-[#1a3529] transition placeholder-[#8c928a]"
                        />
                        @if (count($research_gaps) > 1)
                            <button
                                type="button"
                                wire:click="removeGap({{ $index }})"
                                class="p-2 text-stone-400 hover:text-red-600 transition"
                                title="Remove Gap"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 4. Proposal Document Attachment -->
        <div class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-6 sm:p-8 space-y-4">
            <div class="border-b border-[#f0eee6] dark:border-stone-800 pb-3">
                <h3 class="text-sm font-bold text-[#181a18] dark:text-white">4. Proposal Manuscript Attachment</h3>
                <p class="text-xs text-[#737971] dark:text-stone-400 mt-0.5">Attach full proposal draft (.pdf or .docx) stored securely in the private institutional vault.</p>
            </div>

            <div class="p-5 rounded border border-[#e6e3da] dark:border-stone-800 bg-[#faf8f5] dark:bg-[#121412]">
                <input
                    type="file"
                    wire:model="full_document"
                    accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                    class="block w-full text-xs text-[#575c56] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-[#f2f7f4] file:text-[#1a3529] hover:file:bg-[#e3ede6] dark:file:bg-emerald-950 dark:file:text-emerald-300 cursor-pointer"
                />
                @error('full_document')
                    <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-stone-400 mt-2">
                    Accepted: Strict PDF (.pdf) or Microsoft Word (.docx) &bull; Maximum file size: 20MB
                </p>
            </div>
        </div>

        <!-- Submission Actions -->
        <div class="pt-4 flex flex-col sm:flex-row items-center justify-end gap-3">
            <button
                type="button"
                wire:click="$set('is_draft', true); submit();"
                wire:loading.attr="disabled"
                class="w-full sm:w-auto px-4 py-2.5 rounded text-xs font-medium text-[#575c56] dark:text-stone-300 bg-stone-100 dark:bg-stone-800 hover:bg-stone-200 dark:hover:bg-stone-700 transition"
            >
                Save as Incomplete Draft
            </button>

            <button
                type="submit"
                wire:click="$set('is_draft', false);"
                wire:loading.attr="disabled"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded text-xs font-semibold text-[#faf8f5] bg-[#1a3529] hover:bg-[#224233] transition shadow-xs"
            >
                <span wire:loading.remove wire:target="submit">Register Research Proposal</span>
                <span wire:loading wire:target="submit">Encrypting &amp; Submitting...</span>
            </button>
        </div>

    </form>

</div>
