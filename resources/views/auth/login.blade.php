<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#faf8f5] dark:bg-[#121412]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Institutional Sign In | {{ config('app.name', 'AgriHub') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

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
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full font-sans antialiased text-[#1c1e21] dark:text-[#f0f2f0] bg-[#faf8f5] dark:bg-[#121412] flex flex-col justify-center py-12 sm:px-6 lg:px-8">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 mb-3 group focus:outline-none">
            <span class="w-9 h-9 rounded bg-[#1a3529] flex items-center justify-center text-[#faf8f5] font-semibold text-xs tracking-wider border border-[#2a523f]">
                AG
            </span>
            <span class="text-xl font-bold tracking-tight text-[#181a18] dark:text-white">
                AgriHub
            </span>
        </a>
        <h1 class="text-lg font-bold text-[#181a18] dark:text-stone-200">
            Institutional Access Portal
        </h1>
        <p class="text-xs text-[#737971] dark:text-stone-400 mt-0.5">
            School of Agriculture and Natural Resources (MUSAGRICA)
        </p>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white dark:bg-[#181b18] py-8 px-6 rounded-lg border border-[#e6e3da] dark:border-stone-800 sm:px-8">

            @if (session('status'))
                <div class="mb-5 p-3 rounded bg-[#f2f7f4] text-[#1a3529] dark:bg-emerald-950/40 dark:text-emerald-300 text-xs font-medium border border-[#c5dacf]">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 p-3 rounded bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300 text-xs font-medium border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.attempt') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-[#737971] dark:text-stone-400 mb-1">
                        Institutional Email Address
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        required
                        value="{{ old('email') }}"
                        placeholder="e.g. kelvin@agrihub.edu"
                        class="w-full rounded border border-[#e6e3da] dark:border-stone-700 dark:bg-[#121412] text-xs sm:text-sm p-2.5 focus:outline-none focus:border-[#1a3529] focus:ring-1 focus:ring-[#1a3529] transition placeholder-[#8c928a]"
                    />
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#737971] dark:text-stone-400">
                            Password
                        </label>
                        <span class="text-[11px] text-[#8c928a]">Default: password</span>
                    </div>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        placeholder="••••••••"
                        class="w-full rounded border border-[#e6e3da] dark:border-stone-700 dark:bg-[#121412] text-xs sm:text-sm p-2.5 focus:outline-none focus:border-[#1a3529] focus:ring-1 focus:ring-[#1a3529] transition placeholder-[#8c928a]"
                    />
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-[#636862] dark:text-stone-400">
                        <input type="checkbox" name="remember" class="rounded text-[#1a3529] focus:ring-[#1a3529] border-[#e6e3da]">
                        <span>Remember credentials</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full py-2.5 px-4 rounded text-xs font-semibold text-[#faf8f5] bg-[#1a3529] hover:bg-[#224233] transition"
                    >
                        Authenticate to Workspace
                    </button>
                </div>
            </form>

            <!-- Quick Demo One-Click Role Switcher -->
            <div class="mt-8 pt-6 border-t border-[#f0eee6] dark:border-stone-800">
                <div class="text-center mb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#8c928a]">
                        Institutional Role Directory (One-Click Demo Access)
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <a
                        href="{{ route('login.quick', ['email' => 'kelvin@agrihub.edu']) }}"
                        class="p-2.5 rounded border border-[#e6e3da] dark:border-stone-800 hover:border-[#1a3529] bg-[#faf8f5] dark:bg-[#121412] hover:bg-[#f2f7f4] transition text-left group"
                    >
                        <div class="text-xs font-bold text-[#181a18] dark:text-stone-200 group-hover:text-[#1a3529]">Student</div>
                        <div class="text-[10px] text-[#737971] truncate">Kelvin Mwape</div>
                    </a>

                    <a
                        href="{{ route('login.quick', ['email' => 'peter@agrihub.edu']) }}"
                        class="p-2.5 rounded border border-[#e6e3da] dark:border-stone-800 hover:border-[#1a3529] bg-[#faf8f5] dark:bg-[#121412] hover:bg-[#f2f7f4] transition text-left group"
                    >
                        <div class="text-xs font-bold text-[#181a18] dark:text-stone-200 group-hover:text-[#1a3529]">Researcher</div>
                        <div class="text-[10px] text-[#737971] truncate">Peter Mwaba</div>
                    </a>

                    <a
                        href="{{ route('login.quick', ['email' => 'cmulenga@agrihub.edu']) }}"
                        class="p-2.5 rounded border border-[#e6e3da] dark:border-stone-800 hover:border-[#1a3529] bg-[#faf8f5] dark:bg-[#121412] hover:bg-[#f2f7f4] transition text-left group"
                    >
                        <div class="text-xs font-bold text-[#181a18] dark:text-stone-200 group-hover:text-[#1a3529]">Supervisor</div>
                        <div class="text-[10px] text-[#737971] truncate">Dr. C. Mulenga</div>
                    </a>

                    <a
                        href="{{ route('login.quick', ['email' => 'admin@agrihub.edu']) }}"
                        class="p-2.5 rounded border border-[#e6e3da] dark:border-stone-800 hover:border-[#1a3529] bg-[#faf8f5] dark:bg-[#121412] hover:bg-[#f2f7f4] transition text-left group"
                    >
                        <div class="text-xs font-bold text-[#181a18] dark:text-stone-200 group-hover:text-[#1a3529]">System Admin</div>
                        <div class="text-[10px] text-[#737971] truncate">Admin User</div>
                    </a>
                </div>
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="text-xs font-medium text-[#737971] hover:text-[#1a3529] transition">
                    &larr; Return to Public Research Repository
                </a>
            </div>

        </div>
    </div>

</body>
</html>
