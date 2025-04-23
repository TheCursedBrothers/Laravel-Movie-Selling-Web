<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Panel - {{ config('app.name') }}</title>

    @vite('resources/css/app.css')

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white">
            <div class="p-6">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold flex items-center">
                    <span class="text-blue-400">Admin</span>
                    <span class="ml-2">Panel</span>
                </a>
            </div>

            <nav class="mt-6">
                <div class="px-4 py-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center py-2 px-4 rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : 'hover:bg-gray-700' }} transition">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('admin.movies.index') }}" class="flex items-center mt-1 py-2 px-4 rounded-md {{ request()->routeIs('admin.movies.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }} transition">
                        <i class="fas fa-film mr-3"></i>
                        Quản lý phim
                    </a>

                    <a href="{{ route('admin.users.index') }}" class="flex items-center mt-1 py-2 px-4 rounded-md {{ request()->routeIs('admin.users.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }} transition">
                        <i class="fas fa-users mr-3"></i>
                        Quản lý người dùng
                    </a>

                    <a href="{{ route('admin.orders.index') }}" class="flex items-center mt-1 py-2 px-4 rounded-md {{ request()->routeIs('admin.orders.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }} transition">
                        <i class="fas fa-shopping-cart mr-3"></i>
                        Quản lý đơn hàng
                    </a>
                </div>

                <div class="mt-10 px-4 py-2">
                    <a href="{{ route('movie.index') }}" class="flex items-center py-2 px-4 text-gray-400 hover:text-white transition" target="_blank">
                        <i class="fas fa-external-link-alt mr-3"></i>
                        Xem trang chủ
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit" class="flex w-full items-center py-2 px-4 text-gray-400 hover:text-white rounded-md hover:bg-red-800 transition">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm py-4 px-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-semibold text-gray-800">
                        @if(isset($header))
                            {{ $header }}
                        @else
                            Admin Dashboard
                        @endif
                    </h1>

                    <div class="flex items-center">
                        <span class="text-gray-700 mr-2">{{ Auth::user()->name }}</span>
                        <img src="{{ asset('images/Avatar/avatar.jpg') }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full">
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="p-6">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
