<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Safety Metrics Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card Container -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <img class="mx-auto" width="100px" src="https://trias-sentosa.com/images/ts.jpg" alt="">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h1>
                <p class="text-gray-600">Sign in to your Safety Metrics Dashboard</p>
            </div>

            <!-- Info Messages -->
            @if ($message = Session::get('message'))
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-blue-600 text-sm">{{ $message }}</p>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-600 text-sm font-medium">Login failed. Please check your credentials.</p>
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input 
                        type="email" 
                        id="email"
                        name="email" 
                        value="{{ old('email') }}"
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        placeholder="you@example.com"
                    >
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        placeholder="Enter your password"
                    >
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 ease-in-out mt-6"
                >
                    Sign In
                </button>
            </form>

            <!-- Divider -->
            <div class="my-6 flex items-center">
                <div class="flex-1 border-t border-gray-300"></div>
                <div class="px-3 text-gray-500 text-sm">Don't have an account?</div>
                <div class="flex-1 border-t border-gray-300"></div>
            </div>

            <!-- Register Link -->
            <p class="text-center">
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    Create a new account
                </a>
            </p>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-600 text-sm mt-6">
            <a href="/" class="hover:text-gray-800">Back to Home</a>
        </p>
    </div>
</body>
</html>
