<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white border-b border-gray-200 shadow-sm">
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
                    <a href="{{ route('dashboard.safety') }}"
                    class="px-3 py-2 rounded-md text-sm font-medium
                    {{ request()->routeIs('dashboard.index') ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        Safety Metrics
                    </a>

                    <a href="{{ route('dashboard.work-permit') }}"
                    class="px-3 py-2 rounded-md text-sm font-medium
                    {{ request()->routeIs('dashboard.work-permit') ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        Work Permit
                    </a>

                    <a href="{{ route('near-miss.dashboard') }}"
                    class="px-3 py-2 rounded-md text-sm font-medium
                    {{ request()->routeIs('dashboard.near-miss') ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        Near Miss
                    </a>
                </div>

                {{-- User + Logout --}}
                <div class="hidden md:flex items-center gap-4">
                    <div class="flex items-center gap-2 text-gray-700">
                        <div class="h-8 w-8 rounded-full bg-green-600 text-white flex items-center justify-center font-semibold">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="text-sm font-medium">
                            {{ Auth::user()->name ?? 'User' }}
                        </span>
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
        <div id="mobileMenu" class="md:hidden hidden border-t border-gray-200">
            <div class="px-4 py-3 space-y-2">

                <a href="{{ route('dashboard.safety') }}"
                class="block px-3 py-2 rounded-md text-sm font-medium
                {{ request()->routeIs('dashboard.index') ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                    Safety Metrics
                </a>

                <a href="{{ route('dashboard.work-permit') }}"
                class="block px-3 py-2 rounded-md text-sm font-medium
                {{ request()->routeIs('dashboard.work-permit') ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                    Work Permit
                </a>

                <a href="{{ route('near-miss.dashboard') }}"
                class="block px-3 py-2 rounded-md text-sm font-medium
                {{ request()->routeIs('dashboard.near-miss') ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                    Near Miss
                </a>

                <div class="pt-3 border-t border-gray-200 flex items-center justify-between">
                    <span class="text-sm text-gray-700 font-medium">
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
            const btn = document.getElementById("mobileMenuBtn");
            const menu = document.getElementById("mobileMenu");

            btn.addEventListener("click", function () {
                menu.classList.toggle("hidden");
            });
        });
    </script>

    @if($message = Session::get('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
            <p class="font-bold">Success!</p>
            <p>{{ $message }}</p>
        </div>
    @endif

    @yield('content')

    @stack('scripts')
    @yield('scripts')
</body>
</html>
