<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#faf8f5] dark:bg-[#121412]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AgriHub') }} | Research Information System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

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
                        primary: {
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
    @livewireStyles
</head>
<body class="h-full font-sans antialiased text-[#1c1e21] dark:text-[#f0f2f0] bg-[#faf8f5] dark:bg-[#121412] flex flex-col min-h-screen">
    <!-- Institutional Top Masthead / Navigation Bar -->
    <header class="bg-white/95 dark:bg-[#181b18]/95 backdrop-blur-sm border-b border-[#e6e3da] dark:border-stone-800 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Brand & Institutional Identity -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group focus:outline-none">
                        <div class="w-8 h-8 rounded bg-[#1a3529] dark:bg-emerald-900/80 flex items-center justify-center text-[#faf8f5] font-semibold text-xs tracking-wider border border-[#2a523f]">
                            AG
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-base tracking-tight text-[#1a3529] dark:text-emerald-300 group-hover:text-black dark:group-hover:text-white transition">
                                AgriHub
                            </span>
                            <span class="text-[10px] uppercase tracking-wider text-[#636862] dark:text-stone-400 font-medium">
                                MUSAGRICA Research Archive
                            </span>
                        </div>
                    </a>

                    <!-- Navigation Links -->
                    <nav class="hidden md:flex items-center space-x-1 pl-4 border-l border-[#e6e3da] dark:border-stone-800" aria-label="Primary Navigation">
                        <a
                            href="{{ route('repository') }}"
                            class="px-3 py-1.5 rounded-md text-xs font-medium transition {{ request()->routeIs('catalog', 'repository') ? 'bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950/50 dark:text-emerald-300 font-semibold' : 'text-[#636862] hover:text-[#1c1e21] hover:bg-stone-50 dark:text-stone-400 dark:hover:text-white dark:hover:bg-stone-800/50' }}"
                        >
                            Public Repository
                        </a>

                        @auth
                            @if (auth()->user()->hasAnyRole(['student', 'researcher']))
                                <a
                                    href="{{ route('student.dashboard') }}"
                                    class="px-3 py-1.5 rounded-md text-xs font-medium transition {{ request()->routeIs('student.dashboard', 'student.proposal.*') ? 'bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950/50 dark:text-emerald-300 font-semibold' : 'text-[#636862] hover:text-[#1c1e21] hover:bg-stone-50 dark:text-stone-400 dark:hover:text-white dark:hover:bg-stone-800/50' }}"
                                >
                                    My Workspace
                                </a>

                                <a
                                    href="{{ route('student.manuscripts') }}"
                                    class="px-3 py-1.5 rounded-md text-xs font-medium transition {{ request()->routeIs('student.manuscripts') ? 'bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950/50 dark:text-emerald-300 font-semibold' : 'text-[#636862] hover:text-[#1c1e21] hover:bg-stone-50 dark:text-stone-400 dark:hover:text-white dark:hover:bg-stone-800/50' }}"
                                >
                                    Submission History
                                </a>
                            @endif

                            @if (auth()->user()->hasAnyRole(['supervisor', 'admin', 'dean', 'research_director']))
                                <a
                                    href="/supervisor"
                                    class="px-3 py-1.5 rounded-md text-xs font-medium text-[#636862] hover:text-[#1c1e21] hover:bg-stone-50 dark:text-stone-400 dark:hover:text-white dark:hover:bg-stone-800/50 transition"
                                >
                                    Supervisor Portal
                                </a>
                            @endif
                        @endauth
                    </nav>
                </div>

                <!-- Right Side Account & Clearance Actions -->
                <div class="flex items-center gap-3">
                    @auth
                        <div class="flex items-center gap-2.5 pr-3 border-r border-[#e6e3da] dark:border-stone-800">
                            <div class="flex flex-col text-right">
                                <span class="text-xs font-medium text-[#1c1e21] dark:text-stone-200">
                                    {{ auth()->user()->name }}
                                </span>
                                <span class="text-[10px] text-[#737971] dark:text-stone-400">
                                    {{ auth()->user()->department ?? 'School of Agriculture' }}
                                </span>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded font-mono font-medium bg-[#f4f1ea] dark:bg-stone-800 text-[#495046] dark:text-stone-300 border border-[#e6e3da] dark:border-stone-700">
                                {{ auth()->user()->role->label() }}
                            </span>
                        </div>

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button
                                type="submit"
                                class="text-xs font-medium text-[#636862] hover:text-[#991b1b] dark:text-stone-400 dark:hover:text-red-400 transition"
                            >
                                Sign Out
                            </button>
                        </form>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-[#faf8f5] bg-[#1a3529] hover:bg-[#224233] dark:bg-emerald-800 dark:hover:bg-emerald-700 rounded transition border border-[#1a3529]"
                        >
                            <span>Institutional Sign In</span>
                            <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Page Content Slot -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Institutional Footer -->
    <footer class="mt-auto border-t border-[#e6e3da] dark:border-stone-800 bg-white/70 dark:bg-[#181b18]/60 py-6 text-xs text-[#737971] dark:text-stone-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <span class="font-medium text-[#1c1e21] dark:text-stone-300">AgriHub Information System</span> &mdash; School of Agriculture &amp; Natural Resources (MUSAGRICA)
            </div>
            <div class="flex items-center gap-4 text-[11px]">
                <span>Role-Governed Research Archive</span>
                <span>•</span>
                <span>Strict Academic Integrity Boundary</span>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
