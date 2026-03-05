<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="antialiased min-h-screen bg-gray-50 dark:bg-zinc-900">
    <div class="relative flex flex-col min-h-screen bg-gradient-to-b from-transparent via-white to-white dark:via-zinc-900 dark:to-zinc-900">
        @if (Route::has('login'))
            <div class="absolute top-0 right-0 flex gap-4 p-6 sm:justify-end sm:pt-5">
                @auth
                    <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:ring-2 focus:ring-[#FF2D20] rounded-lg px-3 py-2">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:ring-2 focus:ring-[#FF2D20] rounded-lg px-3 py-2">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:ring-2 focus:ring-[#FF2D20] rounded-lg px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md">Register</a>
                    @endif
                @endauth
            </div>
        @endif

        <main class="flex flex-col flex-1 justify-center py-16 sm:py-24 lg:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-6xl">
                        Laravel
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Laravel is a web application framework with expressive, elegant syntax. We've already laid the foundation — freeing you to create without sweating the small things.
                    </p>
                </div>

                <div class="mt-16 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 sm:gap-8">
                    <div class="flex flex-col gap-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-none">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Documentation</h2>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            Laravel has wonderful documentation covering every aspect of the framework. Whether you are a newcomer or have prior experience with Laravel, we recommend reading our documentation from beginning to end.
                        </p>
                        <a href="https://laravel.com/docs" target="_blank" rel="noopener" class="text-sm font-medium text-[#FF2D20] hover:underline focus:outline focus:ring-2 focus:ring-[#FF2D20] rounded">
                            Explore the docs →
                        </a>
                    </div>
                    <div class="flex flex-col gap-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-none">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Laracasts</h2>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            Laracasts offers thousands of video tutorials on Laravel, PHP, and JavaScript development. Check them out, see for yourself, and massively level up your development skills in the process.
                        </p>
                        <a href="https://laracasts.com" target="_blank" rel="noopener" class="text-sm font-medium text-[#FF2D20] hover:underline focus:outline focus:ring-2 focus:ring-[#FF2D20] rounded">
                            Start watching →
                        </a>
                    </div>
                    <div class="flex flex-col gap-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-none">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Laravel News</h2>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            Laravel News is a community driven portal and newsletter aggregating all of the latest and most important news in the Laravel ecosystem, including new package releases and tutorials.
                        </p>
                        <a href="https://laravel-news.com" target="_blank" rel="noopener" class="text-sm font-medium text-[#FF2D20] hover:underline focus:outline focus:ring-2 focus:ring-[#FF2D20] rounded">
                            Stay in the loop →
                        </a>
                    </div>
                </div>

                <div class="mt-16 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                    </p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
