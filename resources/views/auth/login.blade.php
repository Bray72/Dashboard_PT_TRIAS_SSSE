<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Safety Metrics Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen relative overflow-y-auto flex items-start justify-center p-4">
    <!-- Background Image -->
    <div class="absolute inset-0 bg-cover bg-center"
        style="background-image: url('https://trias-sentosa.com/images/about3.webp');">
    </div>

    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/35"></div>

    <!-- Content -->
    <div class="relative z-10 w-full max-w-md my-10">
        <div class="bg-white backdrop-blur-md rounded-2xl shadow-2xl p-8">
            <img class="mx-auto" width="100px" src="https://trias-sentosa.com/images/ts.jpg" alt="">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Welcome Back</h1>
                <p class="text-sm text-gray-600 mt-1">Sign in to your Safety Metrics Dashboard</p>
            </div>

            <form action="#" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" placeholder="Masukkan email"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" placeholder="Masukkan password"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="submit"
                    class="w-full py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                    Login
                </button>
            </form>
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

            <p class="text-xs text-gray-500 text-center mt-6">
                © {{ date('Y') }} - All Rights Reserved
            </p>
        </div>
        <p class="text-center text-whitey-600 text-sm mt-6">
            <a href="/" class="hover:text-white-800">Back to Home</a>
        </p>
    </div>
</body>

</html>
