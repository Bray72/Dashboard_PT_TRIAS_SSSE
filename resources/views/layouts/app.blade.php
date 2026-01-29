<!DOCTYPE html>
<html lang="en" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // Restore dark mode preference on page load
        (function() {
            const isDark = localStorage.getItem('darkMode') === 'true';
            const htmlElement = document.documentElement;
            if (isDark) {
                htmlElement.classList.add('dark');
            }
        })();
    </script>
    <link rel="icon" type="image/png" href="{{ asset('https://media.licdn.com/dms/image/v2/D560BAQEupOn406TVUw/company-logo_200_200/company-logo_200_200/0/1690511883379/pt_trias_sentosa_tbk_logo?e=2147483647&v=beta&t=H5jupd3fgQtrgyLZBBwu_2sDuWX5sYHYzhvlffGAdBY') }}">
</head>
<body class="bg-gray-50 dark:bg-gray-950 dark:text-gray-50">
    <nav class="bg-white border-b border-gray-200 shadow-sm dark:bg-gray-900 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                {{-- Logo + Brand --}}
                <div class="flex items-center gap-3">
                    <a href="{{ url('/') }}" class="flex items-center gap-2">
                        <img src="{{ asset('https://trias-sentosa.com/images/logo.webp') }}"
                            alt="Logo"
                            style="width:250px; height:auto;"
                            class="object-contain"
                            onerror="this.style.display='none'">
                    </a>
                </div>

                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center gap-6">
                    <div class="relative group">
                        <button class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                            Dashboards
                        </button>
                        <div class="absolute left-0 mt-0 w-48 bg-white rounded-md shadow-lg hidden group-hover:block z-50 dark:bg-gray-800">
                            <a href="{{ route('dashboard.safety') }}"
                            class="block px-4 py-2
                            {{ request()->routeIs('dashboard.safety') 
                                    ? 'bg-blue-600 text-white' 
                                    : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                                Safety Metrics
                            </a>

                            <a href="{{ route('dashboard.work-permit') }}"
                            class="block px-4 py-2
                            {{ request()->routeIs('dashboard.work-permit') 
                                    ? 'bg-blue-600 text-white' 
                                    : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                                Work Permit
                            </a>

                            <a href="{{ route('near-miss.dashboard') }}"
                            class="block px-4 py-2
                            {{ request()->routeIs('near-miss.dashboard') 
                                    ? 'bg-blue-600 text-white' 
                                    : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                                Near Miss
                            </a>

                            <a href="{{ route('dashbaord.activity') }}"
                            class="block px-4 py-2
                            {{ request()->routeIs('dashbaord.activity') 
                                    ? 'bg-blue-600 text-white' 
                                    : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                                Activity
                            </a>
                        </div>
                    </div>

                    <div class="relative group">
                        <button class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                            Import Data
                        </button>
                        <div class="absolute left-0 mt-0 w-48 bg-white rounded-md shadow-lg hidden group-hover:block z-50 dark:bg-gray-800">
                            <a href="{{ route('import.safety-metrics') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">Safety Metrics</a>
                            <a href="{{ route('import.work-permit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">Work Permit</a>
                            <a href="{{ route('import.near-miss') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">Near Miss</a>
                        </div>
                    </div>
                </div>

                {{-- User + Logout + Dark Mode Toggle --}}
                <div class="hidden md:flex items-center gap-4">
                    {{-- Dark Mode Toggle --}}
                    <button id="darkModeToggle" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 transition" title="Toggle dark mode">
                        <svg id="sunIcon" class="w-5 h-5 text-yellow-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v2a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.536l1.414 1.414a1 1 0 001.414-1.414l-1.414-1.414a1 1 0 00-1.414 1.414zm2.828-2.828l1.414-1.414a1 1 0 00-1.414-1.414l-1.414 1.414a1 1 0 001.414 1.414zm0-5.656l1.414 1.414a1 1 0 101.414-1.414l-1.414-1.414a1 1 0 10-1.414 1.414zM5.464 5.464a1 1 0 001.414-1.414L5.464 2.636a1 1 0 00-1.414 1.414l1.414 1.414zm0 9.172l-1.414 1.414a1 1 0 101.414 1.414l1.414-1.414a1 1 0 00-1.414-1.414zM2.636 5.464l1.414-1.414a1 1 0 00-1.414-1.414L2.636 4.05a1 1 0 001.414 1.414zM2 10a1 1 0 11-2 0 1 1 0 012 0zm16 0a1 1 0 110-2 1 1 0 010 2z" clip-rule="evenodd"/>
                        </svg>
                        <svg id="moonIcon" class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                        </svg>
                    </button>

                    <div class="relative group">
                        <button class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                            <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                <div class="h-8 w-8 rounded-full bg-green-600 text-white flex items-center justify-center font-semibold">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="text-sm font-medium">
                                    {{ Auth::user()->name ?? 'User' }}
                                </span>
                            </div>
                        </button>
                        <div class="absolute left-0 mt-0 w-48 bg-white rounded-md shadow-lg hidden group-hover:block z-50 dark:bg-gray-800">
                            <a href="{{ route('admin.users') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">User Approval</a>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 rounded-md bg-red-500 text-white text-sm font-medium hover:bg-red-600 transition">
                            Logout
                        </button>
                    </form>
                </div>

                {{-- Mobile button --}}
                <button id="mobileMenuBtn"
                    class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:bg-gray-100">
                    ☰
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobileMenu" class="md:hidden hidden border-t border-gray-200 dark:border-gray-800 dark:bg-gray-900">
            <div class="px-4 py-3 space-y-2">

                <a href="{{ route('dashboard.safety') }}"
                class="block px-3 py-2 rounded-md text-sm font-medium
                {{ request()->routeIs('dashboard.index') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                    Safety Metrics
                </a>

                <a href="{{ route('dashboard.work-permit') }}"
                class="block px-3 py-2 rounded-md text-sm font-medium
                {{ request()->routeIs('dashboard.work-permit') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                    Work Permit
                </a>

                <a href="{{ route('near-miss.dashboard') }}"
                class="block px-3 py-2 rounded-md text-sm font-medium
                {{ request()->routeIs('dashboard.near-miss') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                    Near Miss
                </a>

                <a href="{{ route('admin.users') }}"
                class="block px-3 py-2 rounded-md text-sm font-medium
                {{ request()->routeIs('admin.users') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                    User Approval
                </a>

                <div class="pt-3 border-t border-gray-200 dark:border-gray-800 flex items-center justify-between">
                    <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                        {{ Auth::user()->name ?? 'User' }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 rounded-md bg-red-500 text-white text-sm font-medium hover:bg-red-600 transition">
                            Logout
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </nav>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Mobile menu toggle
            const mobileBtn = document.getElementById("mobileMenuBtn");
            const mobileMenu = document.getElementById("mobileMenu");

            if (mobileBtn) {
                mobileBtn.addEventListener("click", function () {
                    mobileMenu.classList.toggle("hidden");
                });
            }

            // Dark mode toggle
            const darkModeToggle = document.getElementById("darkModeToggle");
            const htmlRoot = document.getElementById("html-root");
            const sunIcon = document.getElementById("sunIcon");
            const moonIcon = document.getElementById("moonIcon");

            if (darkModeToggle) {
                darkModeToggle.addEventListener("click", function () {
                    const isDark = htmlRoot.classList.toggle("dark");
                    localStorage.setItem("darkMode", isDark);
                    
                    // Toggle icon visibility
                    if (isDark) {
                        sunIcon.classList.remove("hidden");
                        moonIcon.classList.add("hidden");
                    } else {
                        sunIcon.classList.add("hidden");
                        moonIcon.classList.remove("hidden");
                    }
                });

                // Set initial icon based on current mode
                const isDarkMode = htmlRoot.classList.contains("dark");
                if (isDarkMode) {
                    sunIcon.classList.remove("hidden");
                    moonIcon.classList.add("hidden");
                } else {
                    sunIcon.classList.add("hidden");
                    moonIcon.classList.remove("hidden");
                }
            }
        });
    </script>

    @if($message = Session::get('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 dark:bg-green-900 dark:border-green-600 dark:text-green-200">
            <p class="font-bold">Success!</p>
            <p>{{ $message }}</p>
        </div>
    @endif

    @yield('content')

    @stack('scripts')
    @yield('scripts')
</body>
</html>
