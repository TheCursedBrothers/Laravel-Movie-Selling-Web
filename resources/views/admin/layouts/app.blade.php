<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - TopMovies</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }

        /* Cải thiện hiệu ứng active cho sidebar */
        .sidebar-active {
            background-color: rgba(59, 130, 246, 0.1);
            border-left: 4px solid #3b82f6;
        }

        /* Style cho submenu active */
        .submenu-active {
            color: #60a5fa !important; /* text-blue-400 */
            font-weight: 500;
            border-left: 2px solid #60a5fa;
            padding-left: 0.5rem !important;
            background-color: rgba(59, 130, 246, 0.05);
        }

        /* CSS cho sidebar cố định */
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 64px; /* Kích thước thu gọn trên mobile */
            z-index: 50;
            transition: width 0.3s ease;
            overflow-y: auto;
        }

        .admin-sidebar.expanded {
            width: 250px;
        }

        .admin-content {
            flex: 1;
            margin-left: 64px; /* Kích thước thu gọn trên mobile */
            transition: margin-left 0.3s ease;
        }

        @media (min-width: 768px) {
            .admin-sidebar {
                width: 250px; /* Kích thước mở rộng trên desktop */
            }
            .admin-content {
                margin-left: 250px; /* Kích thước mở rộng trên desktop */
            }
        }

        /* Ẩn thanh cuộn của sidebar nhưng vẫn cho phép cuộn */
        .sidebar-inner::-webkit-scrollbar {
            width: 0px;
        }

        /* CSS cho main content scrollable */
        .main-content-scrollable {
            height: calc(100vh - 64px); /* Chiều cao viewport trừ đi header */
            overflow-y: auto;
        }

        /* Cải thiện responsive cho màn hình dưới 768px */
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 0;
                z-index: 60;
            }

            .admin-sidebar.expanded {
                width: 250px;
            }

            .admin-content {
                margin-left: 0;
                width: 100%;
            }

            .main-content-scrollable {
                padding: 1rem !important;
            }

            .card, .shadow-md, .bg-white {
                border-radius: 0.5rem !important;
            }

            /* Cải thiện header trên mobile */
            header.h-16 {
                height: 3.5rem !important;
            }

            header .px-6 {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            /* Nút về client */
            .back-to-client-button {
                padding: 0.375rem 0.75rem !important;
                font-size: 0.75rem !important;
            }

            /* Fix overflow trên các bảng */
            .overflow-x-auto {
                margin: 0 -1rem;
                padding: 0 1rem;
                width: calc(100% + 2rem);
            }

            /* Giảm padding các phần chính */
            .px-6 {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .py-8 {
                padding-top: 1.5rem !important;
                padding-bottom: 1.5rem !important;
            }

            /* Fix layout grid trên mobile */
            .md\:grid-cols-2, .md\:grid-cols-3, .md\:grid-cols-4 {
                grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
            }

            .md\:grid-cols-2.stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }

            /* Fix các item form */
            .sm\:grid-cols-3 {
                grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
            }

            .md\:flex-row {
                flex-direction: column !important;
            }

            .mt-4.md\:mt-0 {
                margin-top: 1rem !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="admin-layout">
        <!-- Sidebar -->
        <div x-data="{ open: true }" class="admin-sidebar bg-gray-800 text-white" :class="{ 'expanded': open }">
            <!-- Mobile Menu Button -->
            <div class="md:hidden p-4 flex justify-between items-center">
                <div class="flex items-center">
                    <img src="{{ asset('images/Avatar/avatar.jpg') }}" alt="Logo" class="h-8 w-8 rounded-full">
                    <span x-show="open" class="text-white font-bold ml-2">TopMovies</span>
                </div>
                <button @click="open = !open" class="p-2 rounded-md hover:bg-gray-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Sidebar content -->
            <div class="sidebar-inner h-full flex flex-col overflow-y-auto">
                <div class="p-4 hidden md:flex items-center">
                    <img src="{{ asset('images/Avatar/avatar.jpg') }}" alt="Logo" class="h-10 w-10 rounded-full">
                    <span class="text-white font-bold ml-2">TopMovies Admin</span>
                </div>

                <div class="p-2 flex-1">
                    <div class="mb-2 px-4 py-3 text-sm text-gray-400 uppercase font-bold">Main</div>
                    <nav>
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center px-4 py-2 text-gray-300 rounded-md hover:bg-gray-700 transition-colors mb-1
                                {{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            <span>Dashboard</span>
                        </a>

                        <!-- Quản lý phim -->
                        @php
                            $isMoviesActive = request()->routeIs('admin.movies.*');
                        @endphp
                        <div x-data="{ moviesOpen: {{ $isMoviesActive ? 'true' : 'false' }} }">
                            <button @click="moviesOpen = !moviesOpen"
                                    class="w-full flex items-center px-4 py-2 text-gray-300 rounded-md hover:bg-gray-700 transition-colors mb-1
                                           {{ $isMoviesActive ? 'sidebar-active' : '' }}">
                                <i class="fas fa-film mr-3"></i>
                                <span>Quản lý phim</span>
                                <svg class="w-4 h-4 ml-auto" :class="{'rotate-90': moviesOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                            <div x-show="moviesOpen" class="pl-10 pr-4 py-1 space-y-1">
                                <a href="{{ route('admin.movies.index') }}"
                                   class="flex items-center py-2 text-sm text-gray-400 hover:text-white rounded-md
                                          {{ request()->routeIs('admin.movies.index') && !request()->query('view') ? 'submenu-active' : '' }}">
                                    <i class="fas fa-list-ul mr-2 text-xs"></i>
                                    <span>Danh sách phim</span>
                                </a>
                                <a href="{{ route('admin.movies.create') }}"
                                   class="flex items-center py-2 text-sm text-gray-400 hover:text-white rounded-md
                                          {{ request()->routeIs('admin.movies.create') ? 'submenu-active' : '' }}">
                                    <i class="fas fa-plus mr-2 text-xs"></i>
                                    <span>Thêm phim mới</span>
                                </a>
                                <a href="{{ route('admin.movies.search') }}"
                                   class="flex items-center py-2 text-sm text-gray-400 hover:text-white rounded-md
                                          {{ request()->routeIs('admin.movies.search') ? 'submenu-active' : '' }}">
                                    <i class="fas fa-search mr-2 text-xs"></i>
                                    <span>Tìm phim từ TMDB</span>
                                </a>
                            </div>
                        </div>

                        <!-- Quản lý người dùng -->
                        @php
                            $isUsersActive = request()->routeIs('admin.users.*');
                        @endphp
                        <div x-data="{ usersOpen: {{ $isUsersActive ? 'true' : 'false' }} }">
                            <button @click="usersOpen = !usersOpen"
                                    class="w-full flex items-center px-4 py-2 text-gray-300 rounded-md hover:bg-gray-700 transition-colors mb-1
                                           {{ $isUsersActive ? 'sidebar-active' : '' }}">
                                <i class="fas fa-users mr-3"></i>
                                <span>Quản lý người dùng</span>
                                <svg class="w-4 h-4 ml-auto" :class="{'rotate-90': usersOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                            <div x-show="usersOpen" class="pl-10 pr-4 py-1 space-y-1">
                                <a href="{{ route('admin.users.index') }}"
                                   class="flex items-center py-2 text-sm text-gray-400 hover:text-white rounded-md
                                          {{ request()->routeIs('admin.users.index') ? 'submenu-active' : '' }}">
                                    <i class="fas fa-list-ul mr-2 text-xs"></i>
                                    <span>Danh sách người dùng</span>
                                </a>
                                <a href="{{ route('admin.users.create') }}"
                                   class="flex items-center py-2 text-sm text-gray-400 hover:text-white rounded-md
                                          {{ request()->routeIs('admin.users.create') ? 'submenu-active' : '' }}">
                                    <i class="fas fa-user-plus mr-2 text-xs"></i>
                                    <span>Thêm người dùng mới</span>
                                </a>
                            </div>
                        </div>

                        <!-- Menu quản lý đơn hàng -->
                        @php
                            $isOrdersActive = request()->routeIs('admin.orders.*');
                        @endphp
                        <div x-data="{ ordersOpen: {{ $isOrdersActive ? 'true' : 'false' }} }">
                            <button @click="ordersOpen = !ordersOpen"
                                    class="w-full flex items-center px-4 py-2 text-gray-300 rounded-md hover:bg-gray-700 transition-colors mb-1
                                           {{ $isOrdersActive ? 'sidebar-active' : '' }}">
                                <i class="fas fa-shopping-cart mr-3"></i>
                                <span>Quản lý đơn hàng</span>
                                <svg class="w-4 h-4 ml-auto" :class="{'rotate-90': ordersOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                            <div x-show="ordersOpen" class="pl-10 pr-4 py-1 space-y-1">
                                <a href="{{ route('admin.orders.index') }}"
                                   class="flex items-center py-2 text-sm text-gray-400 hover:text-white rounded-md
                                          {{ request()->routeIs('admin.orders.index') ? 'submenu-active' : '' }}">
                                    <i class="fas fa-list-ul mr-2 text-xs"></i>
                                    <span>Danh sách đơn hàng</span>
                                </a>

                                <!-- Thêm active state cho các trang chi tiết đơn hàng -->
                                <a href="{{ route('admin.orders.index') }}"
                                   class="flex items-center py-2 text-sm text-gray-400 hover:text-white rounded-md
                                          {{ (request()->routeIs('admin.orders.show') || request()->routeIs('admin.orders.edit')) ? 'submenu-active' : '' }}">
                                    <i class="fas fa-clipboard-list mr-2 text-xs"></i>
                                    <span>Chi tiết đơn hàng</span>
                                </a>
                            </div>
                        </div>
                    </nav>
                </div>

                <div class="p-4 border-t border-gray-700">
                    <div class="flex items-center">
                        <img src="{{ asset('images/Avatar/avatar.jpg') }}" alt="{{ Auth::user()->name }}" class="h-8 w-8 rounded-full">
                        <div x-show="open" class="ml-2">
                            <div class="text-sm font-medium text-gray-200">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-400">{{ Auth::user()->email }}</div>
                        </div>
                        <div class="ml-auto">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="p-1 rounded text-gray-400 hover:text-white">
                                    <i class="fas fa-sign-out-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="admin-content flex flex-col">
            <!-- Top header -->
            <header class="bg-white shadow-sm z-10 h-16">
                <div class="flex items-center justify-between h-full px-6">
                    <div class="flex items-center">
                        <div class="flex items-center md:hidden mr-2">
                            <button @click="open = !open" class="p-2 rounded-md hover:bg-gray-200">
                                <svg class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                        </div>
                        <a href="{{ route('movie.index') }}" class="text-gray-800 hover:text-blue-500 flex items-center">
                            <i class="fas fa-home mr-1"></i>
                            <span class="text-sm">Trang chủ</span>
                        </a>
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-500 truncate max-w-[150px] md:max-w-none">@yield('breadcrumb', 'Dashboard')</span>
                        <!-- Nút trở về client -->
                        <a href="{{ route('movie.index') }}"
                           class="ml-2 md:ml-4 inline-flex items-center px-2 py-1 md:px-3 md:py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-md text-xs md:text-sm font-medium transition-colors back-to-client-button"
                           title="Trở về trang người dùng">
                            <i class="fas fa-arrow-left mr-1"></i>
                            <span class="hidden md:inline">Về trang người dùng</span>
                            <span class="inline md:hidden">Client</span>
                        </a>
                    </div>
                    <div class="flex items-center">
                        <div x-data="{ userMenuOpen: false }" class="relative">
                            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center focus:outline-none">
                                <img src="{{ asset('images/Avatar/avatar.jpg') }}" alt="User avatar" class="h-8 w-8 rounded-full">
                                <span class="ml-2 text-sm font-medium text-gray-800 hidden md:block">{{ Auth::user()->name }}</span>
                                <svg class="h-4 w-4 ml-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-cloak
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Hồ sơ cá nhân</a>
                                <div class="border-t border-gray-200 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main content area -->
            <main class="main-content-scrollable px-6 py-8 bg-gray-100">
                @if (session('success'))
                    <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-4 px-6 flex items-center justify-between mt-auto">
                <div class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} TopMovies Admin. All rights reserved.
                </div>
                <div class="text-sm text-gray-500">
                    Version 1.0.0
                </div>
            </footer>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
