<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Safety Metrics Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <img src="/ppt.webp" width="250px" alt="logo">
                <ul class="flex gap-6">
                    <li><a href="{{ route('dashboard.safety') }}" class="text-gray-700 hover:text-blue-600">Safety Metrics</a></li>
                    <li><a href="{{ route('dashboard.work-permit') }}" class="text-gray-700 hover:text-blue-600">Work-Permit</a></li>
                </ul>
            </div>
        </div>
    </nav>

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
