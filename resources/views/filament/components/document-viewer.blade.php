@php
    $record = $getRecord();
    $documentUrl = $record?->getDocumentUrl();
@endphp

<div class="w-full">
    @if ($documentUrl)
        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 bg-gray-50/80 dark:bg-gray-900/80 px-4 py-2.5">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                    </span>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                        Manuscript Viewer · Clearance Guarded
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <a
                        href="{{ $documentUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700/60 transition-colors"
                    >
                        <svg class="h-3.5 w-3.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span>Open in New Tab</span>
                    </a>
                </div>
            </div>

            <div class="p-2 bg-gray-100 dark:bg-gray-950">
                <iframe
                    src="{{ $getRecord()->getDocumentUrl() }}"
                    class="h-[600px] w-full rounded-lg border border-gray-700"
                    title="Manuscript Document Preview"
                ></iframe>
            </div>
        </div>
    @else
        <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/30 p-12 text-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 mb-3">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">No Manuscript Attached</h4>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 max-w-sm">
                The advisee author has not uploaded a manuscript file (PDF/DOCX) for this research project yet.
            </p>
        </div>
    @endif
</div>
