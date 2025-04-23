<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-200 antialiased bg-gray-900">
    <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
        <!-- Logo -->
        <div class="mb-6 mt-10">
            <a href="{{ route('movie.index') }}" class="flex items-center">
                <svg class="w-12 h-12" width="100" height="50" viewBox="0 15 100 50"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Background rectangle -->
                    <rect x="10" y="10" width="80" height="60" fill="#000" stroke="#fff"
                        stroke-width="3" rx="5" />

                    <!-- Film holes (left) -->
                    <circle cx="15" cy="20" r="3" fill="#fff" />
                    <circle cx="15" cy="40" r="3" fill="#fff" />
                    <circle cx="15" cy="60" r="3" fill="#fff" />

                    <!-- Film holes (right) -->
                    <circle cx="85" cy="20" r="3" fill="#fff" />
                    <circle cx="85" cy="40" r="3" fill="#fff" />
                    <circle cx="85" cy="60" r="3" fill="#fff" />

                    <!-- Play button in the middle -->
                    <polygon points="40,25 40,55 65,40" fill="#fff" />
                </svg>
                <span class="ml-2 text-xl font-bold">Top Movies</span>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-gray-800 shadow-md overflow-hidden rounded-lg border border-gray-700">
            {{ $slot }}
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('movie.index') }}" class="text-gray-400 hover:text-orange-500 text-sm">
                Quay lại trang chủ
            </a>
        </div>
    </div>
</body>
</html>
