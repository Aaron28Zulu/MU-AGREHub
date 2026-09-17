<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#faf8f5] dark:bg-[#121412] scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'AgriHub') }} | MUSAGRICA Research Management & Project Database System</title>
    <meta name="description" content="Centralized academic repository and research management platform for the School of Agriculture and Natural Resources (MUSAGRICA). Discover verified dissertations, research gaps, and emerging agricultural innovations.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Scripts & Styles -->
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Instrument Sans"', 'Inter', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                    },
                    colors: {
                        forest: {
                            50: '#f2f7f4',
                            100: '#e3ede6',
                            200: '#c5dacf',
                            300: '#9cbfae',
                            400: '#6d9f87',
                            500: '#498166',
                            600: '#35674f',
                            700: '#2a523f',
                            800: '#224233',
                            900: '#1a3529',
                            950: '#0d1e16',
                        },
                        paper: {
                            base: '#faf8f5',
                            card: '#ffffff',
                            muted: '#f4f1ea',
                            border: '#e6e3da',
                            dark: '#121412',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body
    x-data="{
        mobileMenuOpen: false,
        activeModalProject: null,
        openModal(project) {
            this.activeModalProject = project;
            document.body.classList.add('overflow-hidden');
        },
        closeModal() {
            this.activeModalProject = null;
            document.body.classList.remove('overflow-hidden');
        }
    }"
    @keydown.escape.window="closeModal()"
    class="h-full font-sans antialiased text-[#1c1e21] dark:text-[#f0f2f0] bg-[#faf8f5] dark:bg-[#121412] flex flex-col selection:bg-[#c5dacf] selection:text-[#1a3529]"
>

    <!-- 1. Clean Navigation Bar -->
    <header class="bg-white/95 dark:bg-[#181b18]/95 backdrop-blur-sm border-b border-[#e6e3da] dark:border-stone-800 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Left: Minimal AgriHub Logo with Badge -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group focus:outline-none">
                        <div class="w-8 h-8 rounded bg-[#1a3529] dark:bg-emerald-900/80 flex items-center justify-center text-[#faf8f5] font-semibold text-xs tracking-wider border border-[#2a523f]">
                            AG
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-base tracking-tight text-[#1a3529] dark:text-emerald-300 group-hover:text-black dark:group-hover:text-white transition">
                                AgriHub
                            </span>
                            <span class="text-[10px] uppercase tracking-wider text-[#636862] dark:text-stone-400 font-medium hidden sm:inline-block">
                                MUSAGRICA &bull; School of Agriculture &amp; Natural Resources
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Center Links -->
                <nav class="hidden md:flex items-center space-x-1" aria-label="Primary Navigation">
                    <a href="#about" class="px-3 py-1.5 rounded-md text-xs font-medium text-[#636862] hover:text-[#1c1e21] hover:bg-stone-50 dark:text-stone-400 dark:hover:text-white dark:hover:bg-stone-800/50 transition">
                        About
                    </a>
                    <a href="#themes" class="px-3 py-1.5 rounded-md text-xs font-medium text-[#636862] hover:text-[#1c1e21] hover:bg-stone-50 dark:text-stone-400 dark:hover:text-white dark:hover:bg-stone-800/50 transition">
                        Research Themes
                    </a>
                    <a href="#gaps" class="px-3 py-1.5 rounded-md text-xs font-medium text-[#636862] hover:text-[#1c1e21] hover:bg-stone-50 dark:text-stone-400 dark:hover:text-white dark:hover:bg-stone-800/50 transition">
                        Identified Gaps
                    </a>
                    <a href="{{ route('repository') }}" class="px-3 py-1.5 rounded-md text-xs font-medium text-[#636862] hover:text-[#1c1e21] hover:bg-stone-50 dark:text-stone-400 dark:hover:text-white dark:hover:bg-stone-800/50 transition">
                        Public Repository
                    </a>
                </nav>

                <!-- Right CTAs -->
                <div class="hidden sm:flex items-center gap-3">
                    <a
                        href="{{ route('repository') }}"
                        class="px-3 py-1.5 rounded text-xs font-medium text-[#1c1e21] dark:text-stone-300 border border-[#e6e3da] dark:border-stone-700 hover:bg-[#faf8f5] dark:hover:bg-stone-800 transition"
                    >
                        Explore Archive
                    </a>

                    @auth
                        @if (auth()->user()->hasAnyRole(['student', 'researcher']))
                            <a
                                href="{{ route('student.dashboard') }}"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-medium text-[#faf8f5] bg-[#1a3529] hover:bg-[#224233] dark:bg-emerald-800 dark:hover:bg-emerald-700 rounded transition border border-[#1a3529]"
                            >
                                <span>Workspace</span>
                                <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @else
                            <a
                                href="/supervisor"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-medium text-[#faf8f5] bg-[#1a3529] hover:bg-[#224233] dark:bg-emerald-800 dark:hover:bg-emerald-700 rounded transition border border-[#1a3529]"
                            >
                                <span>Portal Dashboard</span>
                                <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @endif
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-medium text-[#faf8f5] bg-[#1a3529] hover:bg-[#224233] dark:bg-emerald-800 dark:hover:bg-emerald-700 rounded transition border border-[#1a3529]"
                        >
                            <span>Institutional Sign In</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu toggle -->
                <div class="flex sm:hidden">
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        type="button"
                        class="p-2 rounded text-[#636862] hover:text-[#1c1e21] dark:text-stone-400 dark:hover:text-white"
                        aria-label="Toggle Navigation Menu"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu dropdown -->
            <div x-show="mobileMenuOpen" x-transition class="sm:hidden border-t border-[#e6e3da] dark:border-stone-800 py-3 space-y-2">
                <a href="#about" @click="mobileMenuOpen = false" class="block px-3 py-1.5 rounded text-xs font-medium text-[#636862] hover:bg-stone-50 dark:text-stone-400">About</a>
                <a href="#themes" @click="mobileMenuOpen = false" class="block px-3 py-1.5 rounded text-xs font-medium text-[#636862] hover:bg-stone-50 dark:text-stone-400">Research Themes</a>
                <a href="#gaps" @click="mobileMenuOpen = false" class="block px-3 py-1.5 rounded text-xs font-medium text-[#636862] hover:bg-stone-50 dark:text-stone-400">Identified Gaps</a>
                <a href="{{ route('repository') }}" class="block px-3 py-1.5 rounded text-xs font-medium text-[#636862] hover:bg-stone-50 dark:text-stone-400">Public Repository</a>
                <div class="pt-2 border-t border-[#e6e3da] dark:border-stone-800 flex flex-col gap-2">
                    <a href="{{ route('repository') }}" class="w-full text-center px-3 py-2 text-xs font-medium border border-[#e6e3da] rounded">Explore Archive</a>
                    <a href="{{ route('login') }}" class="w-full text-center px-3 py-2 text-xs font-semibold text-white bg-[#1a3529] rounded">Institutional Sign In &rarr;</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Flow -->
    <main class="flex-1">

        <!-- 2. Hero Section (Minimalist & Impactful) -->
        <section class="relative pt-16 pb-20 sm:pt-24 sm:pb-24 border-b border-[#e6e3da] dark:border-stone-800 bg-gradient-to-b from-white to-[#faf8f5] dark:from-[#181b18] dark:to-[#121412]">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8">

                <!-- Status Pill -->
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950/60 dark:text-emerald-300 border border-[#c5dacf]/70">
                    <span class="w-2 h-2 rounded-full bg-[#1a3529] dark:bg-emerald-400 animate-pulse"></span>
                    <span>Academic Year 2025/2026 Repository Active</span>
                </div>

                <!-- Headline -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-[#181a18] dark:text-[#faf8f5] leading-[1.15] max-w-4xl mx-auto">
                    Advancing Agricultural Innovation Through Preserved Research &amp; Discovery
                </h1>

                <!-- Sub-headline -->
                <p class="text-base sm:text-lg text-[#575c56] dark:text-stone-300 leading-relaxed max-w-3xl mx-auto">
                    MUSAGRICA&rsquo;s centralized repository for cataloging student dissertations, eliminating repetitive methodologies, and revealing unaddressed research gaps across agricultural domains.
                </p>

                <!-- Primary Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                    <a
                        href="{{ route('student.proposal.create') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-md text-sm font-semibold text-[#faf8f5] bg-[#1a3529] hover:bg-[#224233] dark:bg-emerald-800 dark:hover:bg-emerald-700 transition shadow-sm"
                    >
                        <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Submit Research Proposal</span>
                    </a>

                    <a
                        href="#recent-discoveries"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-md text-sm font-medium text-[#1c1e21] dark:text-stone-200 bg-white dark:bg-[#181b18] border border-[#e6e3da] dark:border-stone-700 hover:bg-[#faf8f5] dark:hover:bg-stone-800 transition"
                    >
                        <span>Browse Public Catalog</span>
                        <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                </div>

                <!-- Global Search Bar with Quick Category Pills -->
                <div class="pt-8 max-w-3xl mx-auto">
                    <form action="{{ route('repository') }}" method="GET" class="relative">
                        <div class="flex items-center bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-700 p-1.5 shadow-sm focus-within:border-[#1a3529] focus-within:ring-1 focus-within:ring-[#1a3529] transition">
                            <div class="pl-3.5 pr-2 text-[#737971]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="search"
                                placeholder="Search {{ $totalProjects > 0 ? $totalProjects . '+' : '400+' }} verified theses, research gaps, or agronomic topics..."
                                class="w-full py-2.5 px-2 text-sm bg-transparent border-0 focus:outline-none focus:ring-0 text-[#1c1e21] dark:text-white placeholder-[#8c928a]"
                            />
                            <button
                                type="submit"
                                class="px-5 py-2.5 rounded text-xs font-semibold text-white bg-[#1a3529] hover:bg-[#224233] transition shrink-0"
                            >
                                Search
                            </button>
                        </div>
                    </form>

                    <!-- Quick Category Pills -->
                    <div class="flex flex-wrap items-center justify-center gap-2 mt-4 text-xs">
                        <span class="text-[#737971] dark:text-stone-400 text-[11px] uppercase tracking-wider font-medium">Quick Explore:</span>
                        <a
                            href="{{ route('repository', ['category' => 'crop_science']) }}"
                            class="px-2.5 py-1 rounded-full bg-white dark:bg-[#181b18] border border-[#e6e3da] dark:border-stone-700 text-[#575c56] dark:text-stone-300 hover:border-[#1a3529] hover:text-[#1a3529] transition"
                        >
                            Crop Science
                        </a>
                        <a
                            href="{{ route('repository', ['category' => 'agribusiness']) }}"
                            class="px-2.5 py-1 rounded-full bg-white dark:bg-[#181b18] border border-[#e6e3da] dark:border-stone-700 text-[#575c56] dark:text-stone-300 hover:border-[#1a3529] hover:text-[#1a3529] transition"
                        >
                            Agribusiness &amp; Economics
                        </a>
                        <a
                            href="{{ route('repository', ['category' => 'animal_science']) }}"
                            class="px-2.5 py-1 rounded-full bg-white dark:bg-[#181b18] border border-[#e6e3da] dark:border-stone-700 text-[#575c56] dark:text-stone-300 hover:border-[#1a3529] hover:text-[#1a3529] transition"
                        >
                            Animal Nutrition
                        </a>
                        <a
                            href="{{ route('repository', ['category' => 'plant_protection']) }}"
                            class="px-2.5 py-1 rounded-full bg-white dark:bg-[#181b18] border border-[#e6e3da] dark:border-stone-700 text-[#575c56] dark:text-stone-300 hover:border-[#1a3529] hover:text-[#1a3529] transition"
                        >
                            Plant Protection &amp; Resources
                        </a>
                    </div>
                </div>

            </div>
        </section>

        <!-- 3. High-Level Impact Metrics (Clean Stat Strip) -->
        <section class="border-b border-[#e6e3da] dark:border-stone-800 bg-white dark:bg-[#181b18]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 divide-y md:divide-y-0 md:divide-x divide-[#e6e3da] dark:divide-stone-800">
                    <div class="text-center pt-4 md:pt-0">
                        <div class="text-2xl sm:text-3xl font-bold font-mono text-[#1a3529] dark:text-emerald-400">
                            {{ $totalProjects > 0 ? $totalProjects . '+' : '400+' }}
                        </div>
                        <div class="text-xs font-semibold text-[#181a18] dark:text-stone-200 mt-1">Verified Studies</div>
                        <div class="text-[11px] text-[#737971] dark:text-stone-400 mt-0.5">Archived Dissertations</div>
                    </div>

                    <div class="text-center pt-4 md:pt-0 md:pl-6">
                        <div class="text-2xl sm:text-3xl font-bold font-mono text-[#1a3529] dark:text-emerald-400">
                            4 Core
                        </div>
                        <div class="text-xs font-semibold text-[#181a18] dark:text-stone-200 mt-1">Disciplines</div>
                        <div class="text-[11px] text-[#737971] dark:text-stone-400 mt-0.5">Academic Departments</div>
                    </div>

                    <div class="text-center pt-4 md:pt-0 md:pl-6">
                        <div class="text-2xl sm:text-3xl font-bold font-mono text-[#1a3529] dark:text-emerald-400">
                            {{ $totalGaps > 0 ? $totalGaps . '+' : '85+' }}
                        </div>
                        <div class="text-xs font-semibold text-[#181a18] dark:text-stone-200 mt-1">Active Topics</div>
                        <div class="text-[11px] text-[#737971] dark:text-stone-400 mt-0.5">Identified Research Gaps</div>
                    </div>

                    <div class="text-center pt-4 md:pt-0 md:pl-6">
                        <div class="text-2xl sm:text-3xl font-bold font-mono text-[#1a3529] dark:text-emerald-400">
                            100%
                        </div>
                        <div class="text-xs font-semibold text-[#181a18] dark:text-stone-200 mt-1">Academic Integrity</div>
                        <div class="text-[11px] text-[#737971] dark:text-stone-400 mt-0.5">Plagiarism &amp; Similarity Indexed</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Curated Feature Grid: "Intelligent Research Discovery" -->
        <section id="about" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
                <div class="text-xs font-semibold uppercase tracking-wider text-[#1a3529] dark:text-emerald-400">
                    Institutional Capabilities
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#181a18] dark:text-white">
                    Designed for Rigorous Agricultural Inquiry
                </h2>
                <p class="text-sm text-[#575c56] dark:text-stone-400 leading-relaxed">
                    AgriHub provides faculty and student researchers with structured tools to monitor progress, evaluate originality, and target high-impact agronomic challenges.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature Card 1 -->
                <div class="bg-white dark:bg-[#181b18] p-8 rounded-lg border border-[#e6e3da] dark:border-stone-800 flex flex-col justify-between hover:border-[#1a3529]/50 transition">
                    <div>
                        <div class="w-10 h-10 rounded bg-[#f2f7f4] dark:bg-emerald-950/60 text-[#1a3529] dark:text-emerald-400 flex items-center justify-center mb-5 border border-[#c5dacf]/60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-[#181a18] dark:text-white mb-2">
                            Prevent Duplication &amp; Explore Gaps
                        </h3>
                        <p class="text-xs sm:text-sm text-[#575c56] dark:text-stone-400 leading-relaxed">
                            Discover unaddressed agronomic challenges instead of repeating past methodologies. Text analysis surfaces future research directions from completed theses to guide new proposals.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-[#f0eee6] dark:border-stone-800 text-[11px] font-mono text-[#1a3529] dark:text-emerald-400">
                        Gap Analysis &bull; Topic Clustering
                    </div>
                </div>

                <!-- Feature Card 2 -->
                <div class="bg-white dark:bg-[#181b18] p-8 rounded-lg border border-[#e6e3da] dark:border-stone-800 flex flex-col justify-between hover:border-[#1a3529]/50 transition">
                    <div>
                        <div class="w-10 h-10 rounded bg-[#f2f7f4] dark:bg-emerald-950/60 text-[#1a3529] dark:text-emerald-400 flex items-center justify-center mb-5 border border-[#c5dacf]/60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-[#181a18] dark:text-white mb-2">
                            Strict Academic Integrity
                        </h3>
                        <p class="text-xs sm:text-sm text-[#575c56] dark:text-stone-400 leading-relaxed">
                            Enforces strict university access policies. Public metadata and abstract summaries are discoverable, while complete manuscripts, methodologies, and raw datasets remain protected in secure vaults.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-[#f0eee6] dark:border-stone-800 text-[11px] font-mono text-[#1a3529] dark:text-emerald-400">
                        Zero-Exposure &bull; RBAC Protection
                    </div>
                </div>

                <!-- Feature Card 3 -->
                <div class="bg-white dark:bg-[#181b18] p-8 rounded-lg border border-[#e6e3da] dark:border-stone-800 flex flex-col justify-between hover:border-[#1a3529]/50 transition">
                    <div>
                        <div class="w-10 h-10 rounded bg-[#f2f7f4] dark:bg-emerald-950/60 text-[#1a3529] dark:text-emerald-400 flex items-center justify-center mb-5 border border-[#c5dacf]/60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-[#181a18] dark:text-white mb-2">
                            Structured Supervision Pipeline
                        </h3>
                        <p class="text-xs sm:text-sm text-[#575c56] dark:text-stone-400 leading-relaxed">
                            Tracks student milestones from proposal registration through defense. Faculty supervisors review similarity reports, annotate manuscript drafts, and assign rubric evaluation marks electronically.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-[#f0eee6] dark:border-stone-800 text-[11px] font-mono text-[#1a3529] dark:text-emerald-400">
                        Milestone Stepper &bull; Rubric Grading
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. "Recent Discoveries & Active Gaps" (Teaser Section) -->
        <section id="recent-discoveries" class="py-20 border-t border-[#e6e3da] dark:border-stone-800 bg-[#fbfaf7] dark:bg-[#161816]">
            <div id="themes"></div>
            <div id="gaps"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-12">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-[#1a3529] dark:text-emerald-400 mb-1">
                            Curated Archive Preview
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#181a18] dark:text-white">
                            Recent Discoveries &amp; Active Research Gaps
                        </h2>
                        <p class="text-xs sm:text-sm text-[#575c56] dark:text-stone-400 mt-1">
                            Sample of verified student research outputs and emerging thematic directions.
                        </p>
                    </div>

                    <a
                        href="{{ route('repository') }}"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#1a3529] dark:text-emerald-400 hover:underline"
                    >
                        <span>View All Archived Studies in Repository</span>
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>

                <!-- 3 Curated Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse ($recentProjects as $project)
                        <div class="bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800 p-6 flex flex-col justify-between hover:border-[#1a3529]/40 transition group">
                            <div>
                                <!-- Category Pill and Year -->
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950 dark:text-emerald-300 border border-[#c5dacf]/60">
                                        {{ $project->category->label() }}
                                    </span>
                                    @if ($project->completion_year)
                                        <span class="text-xs font-mono text-[#737971] dark:text-stone-500">
                                            Class {{ $project->completion_year }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Research Title -->
                                <h3 class="text-base font-bold text-[#181a18] dark:text-white group-hover:text-[#1a3529] dark:group-hover:text-emerald-400 transition mb-2.5 leading-snug">
                                    {{ $project->title }}
                                </h3>

                                <!-- Findings / Abstract Excerpt (2-3 sentences) -->
                                <p class="text-xs text-[#575c56] dark:text-stone-400 line-clamp-3 leading-relaxed mb-4">
                                    {{ $project->abstract }}
                                </p>
                            </div>

                            <div class="pt-4 border-t border-[#f0eee6] dark:border-stone-800 space-y-3">
                                <!-- Research Gap Indicator -->
                                @if ($project->researchGaps->isNotEmpty())
                                    <div class="flex items-start gap-1.5 text-[11px] text-[#2a523f] dark:text-emerald-300 bg-[#f2f7f4] dark:bg-emerald-950/40 p-2 rounded border border-[#c5dacf]/50">
                                        <svg class="w-3.5 h-3.5 shrink-0 mt-0.5 text-[#1a3529] dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        <span class="line-clamp-2">
                                            <strong>Gap:</strong> {{ $project->researchGaps->first()->extracted_gap_text }}
                                        </span>
                                    </div>
                                @else
                                    <div class="text-[11px] text-stone-400 italic">
                                        Thematic gaps pending clustering
                                    </div>
                                @endif

                                <!-- Subtle Link to Trigger Read-Only Metadata Modal -->
                                <div class="flex items-center justify-between pt-1">
                                    <span class="text-[11px] text-[#737971] dark:text-stone-400">
                                        By {{ $project->student?->name ?? 'Student Researcher' }}
                                    </span>
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
                                        class="text-xs font-semibold text-[#1a3529] dark:text-emerald-400 hover:underline inline-flex items-center gap-1"
                                    >
                                        <span>View Abstract &amp; Gaps</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-12 bg-white dark:bg-[#181b18] rounded-lg border border-[#e6e3da] dark:border-stone-800">
                            <p class="text-xs text-[#737971]">No completed projects cataloged yet. Please run database seeders.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Bottom CTA Button -->
                <div class="mt-12 text-center">
                    <a
                        href="{{ route('repository') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-md text-xs font-semibold text-[#faf8f5] bg-[#1a3529] hover:bg-[#224233] dark:bg-emerald-800 dark:hover:bg-emerald-700 transition shadow-sm"
                    >
                        <span>View All Archived Studies in Repository</span>
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </section>

    </main>

    <!-- 6. Interactive Read-Only Metadata Modal (Alpine.js) -->
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
                class="relative bg-white dark:bg-[#181b18] border border-[#e6e3da] dark:border-stone-800 rounded-lg max-w-2xl w-full p-6 sm:p-8 text-left shadow-xl"
                @click.stop
            >
                <!-- Modal Header -->
                <div class="flex items-start justify-between gap-4 pb-4 border-b border-[#e6e3da] dark:border-stone-800">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950 dark:text-emerald-300 border border-[#c5dacf]/60" x-text="activeModalProject?.category"></span>
                            <span class="text-xs font-mono text-[#737971]" x-show="activeModalProject?.year" x-text="'Academic Year ' + activeModalProject?.year"></span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-[#181a18] dark:text-white leading-snug" x-text="activeModalProject?.title"></h3>
                    </div>
                    <button
                        @click="closeModal()"
                        type="button"
                        class="text-stone-400 hover:text-stone-600 dark:hover:text-stone-200 p-1"
                        aria-label="Close modal"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="py-5 space-y-5 max-h-[65vh] overflow-y-auto">
                    <!-- Citation Metadata -->
                    <div class="grid grid-cols-2 gap-3 text-xs bg-[#faf8f5] dark:bg-[#121412] p-3 rounded border border-[#e6e3da] dark:border-stone-800">
                        <div>
                            <span class="text-[#737971]">Student Researcher:</span>
                            <div class="font-semibold text-[#181a18] dark:text-stone-200" x-text="activeModalProject?.author"></div>
                        </div>
                        <div>
                            <span class="text-[#737971]">Department:</span>
                            <div class="font-semibold text-[#181a18] dark:text-stone-200" x-text="activeModalProject?.department"></div>
                        </div>
                        <div class="col-span-2 pt-1 border-t border-[#f0eee6] dark:border-stone-800">
                            <span class="text-[#737971]">Faculty Supervisor:</span>
                            <div class="font-semibold text-[#181a18] dark:text-stone-200" x-text="activeModalProject?.supervisor"></div>
                        </div>
                    </div>

                    <!-- Abstract -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#737971] dark:text-stone-400 mb-1.5">
                            Abstract &amp; Summary of Findings
                        </h4>
                        <p class="text-xs sm:text-sm text-[#495046] dark:text-stone-300 leading-relaxed" x-text="activeModalProject?.abstract"></p>
                    </div>

                    <!-- Identified Research Gaps -->
                    <template x-if="activeModalProject?.gaps && activeModalProject?.gaps.length > 0">
                        <div class="p-3.5 rounded border border-[#c5dacf]/60 bg-[#f2f7f4] dark:bg-emerald-950/30">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-[#1a3529] dark:text-emerald-300 mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span>Identified Future Research Directions</span>
                            </h4>
                            <ul class="space-y-1.5 text-xs text-[#2a523f] dark:text-stone-300">
                                <template x-for="gap in activeModalProject?.gaps" :key="gap">
                                    <li class="flex items-start gap-2">
                                        <span class="font-bold text-[#1a3529] dark:text-emerald-400">&bull;</span>
                                        <span x-text="gap"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>

                    <!-- Academic Clearance Warning -->
                    <div class="text-[11px] text-[#737971] dark:text-stone-400 bg-amber-50/60 dark:bg-amber-950/20 p-3 rounded border border-amber-200/60 dark:border-amber-900/40 flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-700 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>
                            <strong>Academic Data Boundary:</strong> Full dissertation manuscript, chapter methodologies, and raw field trial datasets are restricted under institutional policy. Access is limited to assigned academic supervisors and university research directors.
                        </span>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="pt-4 border-t border-[#e6e3da] dark:border-stone-800 flex items-center justify-between">
                    <a
                        :href="'{{ route('repository') }}?search=' + encodeURIComponent(activeModalProject?.title || '')"
                        class="text-xs font-semibold text-[#1a3529] dark:text-emerald-400 hover:underline"
                    >
                        Locate in Public Repository &rarr;
                    </a>
                    <button
                        @click="closeModal()"
                        type="button"
                        class="px-4 py-1.5 rounded text-xs font-medium text-[#1c1e21] dark:text-stone-200 bg-stone-100 dark:bg-stone-800 hover:bg-stone-200 dark:hover:bg-stone-700 transition"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 7. Minimalist Academic Footer -->
    <footer class="border-t border-[#e6e3da] dark:border-stone-800 bg-white dark:bg-[#181b18] text-xs text-[#575c56] dark:text-stone-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Col 1: Institutional Authority -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded bg-[#1a3529] flex items-center justify-center text-[#faf8f5] font-semibold text-[10px]">
                            AG
                        </div>
                        <span class="font-bold text-sm text-[#181a18] dark:text-white">AgriHub</span>
                    </div>
                    <p class="text-xs text-[#737971] dark:text-stone-400 leading-relaxed">
                        School of Agriculture and Natural Resources (MUSAGRICA). Research Management and Centralized Project Information System.
                    </p>
                </div>

                <!-- Col 2: Academic Repository -->
                <div>
                    <h4 class="font-bold text-xs uppercase tracking-wider text-[#181a18] dark:text-stone-200 mb-3">Academic Repository</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('repository', ['category' => 'crop_science']) }}" class="hover:text-[#1a3529] dark:hover:text-emerald-400 transition">Crop Science Archive</a></li>
                        <li><a href="{{ route('repository', ['category' => 'agribusiness']) }}" class="hover:text-[#1a3529] dark:hover:text-emerald-400 transition">Agribusiness &amp; Economics</a></li>
                        <li><a href="{{ route('repository', ['category' => 'animal_science']) }}" class="hover:text-[#1a3529] dark:hover:text-emerald-400 transition">Animal Nutrition Studies</a></li>
                        <li><a href="{{ route('repository', ['category' => 'plant_protection']) }}" class="hover:text-[#1a3529] dark:hover:text-emerald-400 transition">Plant Protection Dissertations</a></li>
                    </ul>
                </div>

                <!-- Col 3: Research Community -->
                <div>
                    <h4 class="font-bold text-xs uppercase tracking-wider text-[#181a18] dark:text-stone-200 mb-3">Governance &amp; Access</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('student.proposal.create') }}" class="hover:text-[#1a3529] dark:hover:text-emerald-400 transition">Student Proposal Submissions</a></li>
                        <li><a href="/supervisor" class="hover:text-[#1a3529] dark:hover:text-emerald-400 transition">Supervisor Portal &amp; Rubrics</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-[#1a3529] dark:hover:text-emerald-400 transition">Institutional Portal Login</a></li>
                        <li><a href="#about" class="hover:text-[#1a3529] dark:hover:text-emerald-400 transition">Research Ethics Guidelines</a></li>
                    </ul>
                </div>

                <!-- Col 4: Integrity Notice -->
                <div class="space-y-2">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-[#181a18] dark:text-stone-200 mb-3">Intellectual Property</h4>
                    <p class="text-[11px] text-[#737971] dark:text-stone-400 leading-relaxed">
                        Preserving institutional intellectual property. Unauthorized harvesting or public redistribution of full manuscripts is strictly prohibited under university research bylaws.
                    </p>
                </div>
            </div>

            <div class="pt-8 border-t border-[#f0eee6] dark:border-stone-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-[#737971] dark:text-stone-500">
                <div>
                    &copy; {{ date('Y') }} MUSAGRICA Research Management &bull; AgriHub Information System.
                </div>
                <div class="flex items-center gap-4">
                    <span>Role-Governed Discovery</span>
                    <span>&bull;</span>
                    <span>pgvector &amp; Similarity Indexed</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
