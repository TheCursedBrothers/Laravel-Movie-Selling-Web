<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Movies App</title>
    {{-- Nhúng CSS từ Vite --}}
    @vite('resources/css/app.css')
    {{-- Thêm Livewire Styles --}}
    @livewireStyles
    {{-- Thêm Alpine.js để cải thiện trải nghiệm người dùng --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- Thêm CSS Spinner trực tiếp trong trường hợp file CSS chưa load kịp --}}
    <style>
        .spinner {
            position: absolute;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* CSS cho modal trailer */
        [x-cloak] { display: none !important; }

        .trailer-modal {
            background-color: rgba(0, 0, 0, 0.9);
        }

        .trailer-container {
            max-width: 768px; /* Giảm kích thước tối đa từ 900px xuống 768px */
            width: 100%;
            margin: 0 auto;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .trailer-header {
            background-color: rgb(31, 41, 55);
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .trailer-body {
            position: relative;
            padding-top: 56.25%; /* 16:9 Aspect Ratio */
        }

        .trailer-iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* Thêm CSS mới cho responsive iframe */
        .responsive-container {
            max-width: 100%;
            width: 100%;
            margin: 0 auto;
        }

        @media (min-width: 768px) {
            .responsive-container {
                max-width: 85%; /* Giảm chiều rộng trên màn hình lớn */
            }
        }

        /* CSS cho buttons */
        .movie-btn {
            display: inline-flex;
            align-items: center;
            background-color: rgb(249, 115, 22);
            color: rgb(17, 24, 39);
            font-weight: 600;
            padding: 1rem 1.25rem;
            border-radius: 0.375rem;
            transition: all 0.15s ease-in-out;
            box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.3);
        }

        .movie-btn:hover {
            background-color: rgb(234, 88, 12);
            box-shadow: 0 15px 20px -3px rgba(249, 115, 22, 0.4);
            transform: translateY(-1px);
        }

        .movie-btn svg {
            width: 1.5rem;
            height: 1.5rem;
            margin-right: 0.5rem;
        }

        /* CSS cho giỏ hàng */
        .bg-gray-750 {
            background-color: rgba(55, 65, 81, 0.5);
        }

        .custom-number-input input::-webkit-outer-spin-button,
        .custom-number-input input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .custom-number-input input[type=number] {
            -moz-appearance: textfield;
        }

        /* Grid layout cho các thẻ phim */
        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        @media (max-width: 640px) {
            .movie-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }

        /* CSS cho line-clamp nếu không dùng Tailwind JIT */
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* CSS cho trang kết quả tìm kiếm */
        .search-results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        /* CSS bổ sung cho trang tìm kiếm */
        .search-results-container {
            background-image: linear-gradient(to bottom, rgba(17, 24, 39, 0.9), rgba(17, 24, 39, 1)),
                              url('/images/cinema-background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        /* CSS cho header cố định */
        .fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background-color: rgb(17, 24, 39); /* bg-gray-900 */
            transition: all 0.3s ease;
        }

        .fixed-header.scrolled {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        /* Thêm padding cho phần nội dung chính để tránh bị che khi header cố định */
        body {
            padding-top: 78px; /* Điều chỉnh theo chiều cao của header */
        }

        /* CSS cho animation khi scroll */
        .scroll-transition {
            transition: all 0.3s ease;
        }

        /* Animation for search results */
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(10px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .movie-card {
            animation: fadeIn 0.5s ease forwards;
        }

        .movie-card:nth-child(odd) {
            animation-delay: 0.1s;
        }

        .movie-card:nth-child(even) {
            animation-delay: 0.2s;
        }

        /* Mobile Menu Styling - Enhanced version */
        .mobile-menu {
            transform: translateX(100%); /* Thay đổi từ -100% sang 100% để menu hiển thị từ phải */
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        }

        .mobile-menu.open {
            transform: translateX(0);
        }

        /* Mobile styles */
        @media (max-width: 768px) {
            body {
                padding-top: 64px; /* Adjust for smaller header on mobile */
            }

            .fixed-header .container {
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
            }

            .mobile-menu {
                height: 100vh; /* Thay đổi để chiếm toàn bộ chiều cao màn hình */
                overflow-y: auto;
                border-radius: 0; /* Loại bỏ border-radius để sát góc */
                background-image: linear-gradient(to bottom, rgb(31, 41, 55), rgb(17, 24, 39));
                top: 0; /* Đặt vị trí sát góc trên */
                right: 0;
                left: auto;
                z-index: 60; /* Tăng z-index để hiển thị trên tất cả */
            }

            .mobile-menu-overlay {
                background-color: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(3px);
            }

            /* Make dropdowns full width on mobile */
            .mobile-dropdown {
                position: static !important;
                width: 100% !important;
                box-shadow: none !important;
                border: none;
                margin: 0 !important;
                background-color: rgba(30, 41, 59, 0.4) !important;
                border-radius: 6px;
            }

            /* Mobile profile menu adjustments */
            .profile-dropdown-mobile {
                position: fixed !important;
                top: 64px;
                right: 0;
                width: 80% !important;
                max-width: 300px;
                z-index: 50;
            }

            /* User info on mobile */
            .user-info-mobile {
                padding: 1rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                margin-bottom: 0.75rem;
                background-color: rgba(30, 41, 59, 0.3);
                border-radius: 6px;
            }

            /* Better touch targets for mobile */
            .mobile-menu a,
            .mobile-menu button {
                padding: 0.85rem 1.25rem;
                margin: 0.25rem 0;
                display: block;
                width: 100%;
                text-align: left;
                border-radius: 6px;
                transition: all 0.2s ease;
            }

            .mobile-menu a:active,
            .mobile-menu button:active {
                transform: scale(0.98);
            }

            /* Enhanced dropdown menu style */
            .mobile-dropdown-menu {
                background-color: rgba(30, 41, 59, 0.3);
                border-radius: 6px;
                margin: 0.5rem 0;
                overflow: hidden;
            }

            .mobile-dropdown-menu-header {
                padding: 0.85rem 1.25rem;
                font-weight: 500;
                border-radius: 6px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                cursor: pointer;
                transition: background-color 0.2s ease;
            }

            .mobile-dropdown-menu-header:hover {
                background-color: rgba(55, 65, 81, 0.5);
            }

            .mobile-dropdown-menu-content {
                max-height: 40vh;
                overflow-y: auto;
                padding: 0.5rem 0;
            }

            .mobile-dropdown-menu-content a {
                padding: 0.7rem 1.5rem;
                margin: 0.15rem 0.5rem;
                background-color: rgba(17, 24, 39, 0.2);
            }

            .mobile-dropdown-menu-content a:hover {
                background-color: rgba(55, 65, 81, 0.5);
            }

            /* Mobile section headers */
            .mobile-section-header {
                padding: 1rem 1.25rem 0.5rem;
                font-size: 0.875rem;
                color: rgba(209, 213, 219, 0.7);
                font-weight: 500;
                letter-spacing: 0.025em;
                text-transform: uppercase;
            }

            /* Mobile search optimization */
            .search-container-mobile {
                position: fixed;
                top: 64px;
                left: 0;
                right: 0;
                padding: 0.75rem 1rem;
                background-color: rgb(24, 31, 41);
                z-index: 30;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                transform: translateY(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .search-container-mobile.open {
                transform: translateY(0);
            }

            /* Special buttons styling */
            .mobile-menu .admin-button {
                background-color: rgba(249, 115, 22, 0.15);
                color: rgb(249, 115, 22);
                font-weight: 500;
            }

            .mobile-menu .admin-button:hover {
                background-color: rgba(249, 115, 22, 0.25);
            }

            .mobile-menu .logout-button {
                background-color: rgba(220, 38, 38, 0.15);
                color: rgb(248, 113, 113);
            }

            .mobile-menu .logout-button:hover {
                background-color: rgba(220, 38, 38, 0.25);
            }

            /* Action groups */
            .mobile-action-group {
                padding: 0.5rem 0.75rem;
                margin: 0.5rem 0;
            }

            /* Adjust movie grid for mobile */
            .movie-grid,
            .search-results-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            /* Thêm style cho nút đóng menu */
            .close-menu-btn {
                position: absolute;
                top: 1rem;
                right: 1rem;
                width: 2rem;
                height: 2rem;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                background-color: rgba(55, 65, 81, 0.5);
                color: white;
                transition: all 0.2s ease;
            }

            .close-menu-btn:hover {
                background-color: rgba(220, 38, 38, 0.3);
                transform: rotate(90deg);
            }

            .close-menu-btn:active {
                transform: rotate(90deg) scale(0.95);
            }

            /* Điều chỉnh khoảng cách cho phần content mobile menu */
            .mobile-menu-content {
                padding-top: 3.5rem;
                height: 100%;
            }
        }
    </style>
</head>

<body class="font-sans bg-gray-900 text-white">
    {{-- Notification component --}}
    @livewire('notification')

    {{-- Thanh điều hướng - Thêm class fixed-header --}}
    <nav class="fixed-header border-b border-gray-800"
         x-data="{
            scrolled: false,
            mobileMenuOpen: false,
            searchMobileOpen: false
         }"
         x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 10 })"
         :class="{ 'scrolled': scrolled }">
        <div class="container mx-auto flex flex-col md:flex-row items-center justify-between px-4 py-6 md:py-6">
            {{-- Desktop and mobile display --}}
            <div class="flex w-full md:w-auto justify-between items-center">
                {{-- Logo và tên ứng dụng --}}
                <a href="{{ route('movie.index') }}" class="flex items-center">
                    {{-- Logo film --}}
                    <svg class="w-8 my-0 py-0" width="100" height="50" viewBox="0 15 100 50"
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
                    <span class="ml-2">Top Movies</span>
                </a>

                {{-- Mobile menu toggle and search button --}}
                <div class="flex items-center md:hidden">
                    {{-- Mobile search toggle --}}
                    <button @click="searchMobileOpen = !searchMobileOpen" class="p-2 mr-2 text-gray-300 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>

                    {{-- Mobile menu hamburger --}}
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-gray-300 hover:text-white">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile search container --}}
            <div x-show="searchMobileOpen"
                 x-transition
                 @click.away="searchMobileOpen = false"
                 class="search-container-mobile">
                <div class="w-full">
                    @livewire('search-dropdown', ['isMobile' => true])
                </div>
            </div>

            {{-- Mobile menu --}}
            <div x-show="mobileMenuOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click.away="mobileMenuOpen = false"
                 class="fixed inset-0 z-40 md:hidden">

                {{-- Background overlay --}}
                <div class="absolute inset-0 mobile-menu-overlay" @click="mobileMenuOpen = false"></div>

                {{-- Mobile menu content --}}
                <div x-show="mobileMenuOpen"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full"
                     class="fixed top-0 right-0 bottom-0 w-3/4 max-w-sm bg-gray-900 border-l border-gray-800 mobile-menu open">

                    {{-- Nút đóng menu --}}
                    <button @click="mobileMenuOpen = false" class="close-menu-btn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <div class="h-full flex flex-col mobile-menu-content">
                        <div class="flex-1 overflow-y-auto">
                            {{-- Main navigation items --}}
                            <div class="px-3 space-y-1.5">
                                {{-- Movies link --}}
                                <a href="{{ route('movie.index') }}" class="flex items-center rounded-md text-base font-medium text-white hover:bg-gray-800">
                                    <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
                                    </svg>
                                    Phim
                                </a>
                            </div>

                            <div class="px-3 mt-5">
                                <h3 class="mobile-section-header">Tìm kiếm phim theo</h3>

                                {{-- Year dropdown --}}
                                <div x-data="{ yearOpen: false }" class="mobile-dropdown-menu">
                                    <div @click="yearOpen = !yearOpen" class="mobile-dropdown-menu-header">
                                        <div class="flex items-center">
                                            <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span>Năm</span>
                                        </div>
                                        <svg :class="{'rotate-180': yearOpen}" class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                    <div x-show="yearOpen"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 -translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-200"
                                         x-transition:leave-start="opacity-100 translate-y-0"
                                         x-transition:leave-end="opacity-0 -translate-y-2"
                                         class="mobile-dropdown-menu-content">
                                        @php
                                            $currentYear = date('Y');
                                            $years = range($currentYear, $currentYear - 20);
                                        @endphp
                                        @foreach($years as $year)
                                            <a href="{{ route('movies.filter', ['year' => $year]) }}" class="block rounded-md text-sm text-gray-300 hover:bg-gray-800 hover:text-white">
                                                {{ $year }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Genre dropdown --}}
                                <div x-data="{ genreOpen: false }" class="mobile-dropdown-menu mt-3">
                                    <div @click="genreOpen = !genreOpen" class="mobile-dropdown-menu-header">
                                        <div class="flex items-center">
                                            <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            <span>Thể loại</span>
                                        </div>
                                        <svg :class="{'rotate-180': genreOpen}" class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                    <div x-show="genreOpen"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 -translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-200"
                                         x-transition:leave-start="opacity-100 translate-y-0"
                                         x-transition:leave-end="opacity-0 -translate-y-2"
                                         class="mobile-dropdown-menu-content">
                                        @php
                                            $commonGenres = [
                                                'Action' => 28,
                                                'Adventure' => 12,
                                                'Animation' => 16,
                                                'Comedy' => 35,
                                                'Crime' => 80,
                                                'Documentary' => 99,
                                                'Drama' => 18,
                                                'Family' => 10751,
                                                'Fantasy' => 14,
                                                'History' => 36,
                                                'Horror' => 27,
                                                'Music' => 10402,
                                                'Mystery' => 9648,
                                                'Romance' => 10749,
                                                'Science Fiction' => 878,
                                                'Thriller' => 53,
                                                'War' => 10752,
                                                'Western' => 37
                                            ];
                                        @endphp
                                        @foreach($commonGenres as $name => $id)
                                            <a href="{{ route('movies.filter', ['genre' => $id, 'genre_name' => $name]) }}" class="block rounded-md text-sm text-gray-300 hover:bg-gray-800 hover:text-white">
                                                {{ $name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Country dropdown --}}
                                <div x-data="{ countryOpen: false }" class="mobile-dropdown-menu mt-3">
                                    <div @click="countryOpen = !countryOpen" class="mobile-dropdown-menu-header">
                                        <div class="flex items-center">
                                            <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Quốc gia</span>
                                        </div>
                                        <svg :class="{'rotate-180': countryOpen}" class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                    <div x-show="countryOpen"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 -translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-200"
                                         x-transition:leave-start="opacity-100 translate-y-0"
                                         x-transition:leave-end="opacity-0 -translate-y-2"
                                         class="mobile-dropdown-menu-content">
                                        @php
                                            $countries = [
                                                'Mỹ' => 'US',
                                                'Anh' => 'GB',
                                                'Pháp' => 'FR',
                                                'Hàn Quốc' => 'KR',
                                                'Nhật Bản' => 'JP',
                                                'Trung Quốc' => 'CN',
                                                'Ấn Độ' => 'IN',
                                                'Tây Ban Nha' => 'ES',
                                                'Đức' => 'DE',
                                                'Úc' => 'AU',
                                                'Canada' => 'CA',
                                                'Ý' => 'IT',
                                                'Thái Lan' => 'TH',
                                                'Việt Nam' => 'VN',
                                                'Brazil' => 'BR',
                                                'Mexico' => 'MX',
                                                'Nga' => 'RU',
                                                'Thụy Điển' => 'SE'
                                            ];
                                        @endphp
                                        @foreach($countries as $name => $code)
                                            <a href="{{ route('movies.filter', ['country' => $code, 'country_name' => $name]) }}" class="block rounded-md text-sm text-gray-300 hover:bg-gray-800 hover:text-white">
                                                {{ $name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- User section - Hiển thị phần người dùng --}}
                            <div class="px-3 mt-6">
                                <h3 class="mobile-section-header">Tài khoản</h3>

                                <div class="space-y-2">
                                    @auth
                                        <div class="user-info-mobile flex items-center mb-4">
                                            <img src="{{ asset('images/Avatar/avatar.jpg') }}" alt="{{ Auth::user()->name }}" class="rounded-full w-10 h-10 border-2 border-gray-700">
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                                                <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                                            </div>
                                        </div>

                                        {{-- Giỏ hàng mobile --}}
                                        <a href="{{ route('cart.index') }}" class="flex items-center rounded-md text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white">
                                            <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span>Giỏ hàng</span>
                                            @php
                                                $cartDropdown = app(\App\Livewire\CartDropdown::class);
                                                $cartCount = $cartDropdown ? $cartDropdown->cartCount : 0;
                                            @endphp
                                            @if($cartCount > 0)
                                                <span class="ml-auto bg-orange-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                                    {{ $cartCount }}
                                                </span>
                                            @endif
                                        </a>

                                        {{-- Tài khoản mobile --}}
                                        <a href="{{ route('profile.edit') }}" class="flex items-center rounded-md text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white">
                                            <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span>Tài khoản</span>
                                        </a>

                                        {{-- Đơn hàng mobile --}}
                                        <a href="{{ route('orders.index') }}" class="flex items-center rounded-md text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white">
                                            <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 3v4a1 1 0 001 1h4"></path>
                                            </svg>
                                            <span>Đơn hàng</span>
                                        </a>

                                        {{-- Admin Panel for mobile --}}
                                        @if(Auth::user()->is_admin)
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center rounded-md text-sm font-medium admin-button">
                                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span>Admin Panel</span>
                                        </a>
                                        @endif

                                        <div class="border-t border-gray-800 my-3"></div>

                                        {{-- Đăng xuất mobile --}}
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center rounded-md text-sm font-medium logout-button">
                                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                </svg>
                                                <span>Đăng xuất</span>
                                            </button>
                                        </form>
                                    @else
                                        <div class="space-y-2 mt-3">
                                            <a href="{{ route('login') }}" class="flex items-center rounded-md text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800">
                                                <svg class="mr-3 h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                                </svg>
                                                <span>Đăng nhập</span>
                                            </a>
                                            <a href="{{ route('register') }}" class="flex items-center rounded-md text-sm font-medium text-gray-300 border border-gray-700 hover:bg-gray-800 hover:text-white">
                                                <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                                </svg>
                                                <span>Đăng ký</span>
                                            </a>

                                            {{-- Giỏ hàng cho người chưa đăng nhập --}}
                                            <a href="{{ route('login') }}" class="flex items-center rounded-md text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white">
                                                <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                <span>Giỏ hàng</span>
                                                <span class="ml-1 text-xs text-gray-500">(Đăng nhập để xem)</span>
                                            </a>
                                        </div>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Desktop Menu (hidden on mobile) --}}
            <ul class="hidden md:flex items-center">
                {{-- Các liên kết điều hướng --}}
                <li class="md:ml-16">
                    <a href="{{ route('movie.index') }}" class="hover:text-gray-300">Movies</a>
                </li>

                {{-- Filter by Year --}}
                <li class="md:ml-10 relative" x-data="{ yearOpen: false }">
                    <button @click="yearOpen = !yearOpen" @click.away="yearOpen = false" class="flex items-center hover:text-gray-300">
                        <span>Năm</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="yearOpen" x-transition.opacity class="absolute z-40 mt-2 w-48 rounded-md shadow-lg bg-gray-800 border border-gray-700">
                        <div class="py-1 max-h-64 overflow-y-auto">
                            @php
                                $currentYear = date('Y');
                                $years = range($currentYear, $currentYear - 20);
                            @endphp

                            @foreach($years as $year)
                                <a href="{{ route('movies.filter', ['year' => $year]) }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">{{ $year }}</a>
                            @endforeach
                        </div>
                    </div>
                </li>

                {{-- Filter by Genre --}}
                <li class="md:ml-10 relative" x-data="{ genreOpen: false }">
                    <button @click="genreOpen = !genreOpen" @click.away="genreOpen = false" class="flex items-center hover:text-gray-300">
                        <span>Thể loại</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="genreOpen" x-transition.opacity class="absolute z-40 mt-2 w-48 rounded-md shadow-lg bg-gray-800 border border-gray-700">
                        <div class="py-1 max-h-64 overflow-y-auto">
                            @php
                                $commonGenres = [
                                    'Action' => 28,
                                    'Adventure' => 12,
                                    'Animation' => 16,
                                    'Comedy' => 35,
                                    'Crime' => 80,
                                    'Documentary' => 99,
                                    'Drama' => 18,
                                    'Family' => 10751,
                                    'Fantasy' => 14,
                                    'History' => 36,
                                    'Horror' => 27,
                                    'Music' => 10402,
                                    'Mystery' => 9648,
                                    'Romance' => 10749,
                                    'Science Fiction' => 878,
                                    'Thriller' => 53,
                                    'War' => 10752,
                                    'Western' => 37
                                ];
                            @endphp

                            @foreach($commonGenres as $name => $id)
                                <a href="{{ route('movies.filter', ['genre' => $id, 'genre_name' => $name]) }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">{{ $name }}</a>
                            @endforeach
                        </div>
                    </div>
                </li>

                {{-- Filter by Country --}}
                <li class="md:ml-10 relative" x-data="{ countryOpen: false }">
                    <button @click="countryOpen = !countryOpen" @click.away="countryOpen = false" class="flex items-center hover:text-gray-300">
                        <span>Quốc gia</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="countryOpen" x-transition.opacity class="absolute z-40 mt-2 w-48 rounded-md shadow-lg bg-gray-800 border border-gray-700">
                        <div class="py-1 max-h-64 overflow-y-auto">
                            @php
                                $countries = [
                                    'Mỹ' => 'US',
                                    'Anh' => 'GB',
                                    'Pháp' => 'FR',
                                    'Hàn Quốc' => 'KR',
                                    'Nhật Bản' => 'JP',
                                    'Trung Quốc' => 'CN',
                                    'Ấn Độ' => 'IN',
                                    'Tây Ban Nha' => 'ES',
                                    'Đức' => 'DE',
                                    'Úc' => 'AU',
                                    'Canada' => 'CA',
                                    'Ý' => 'IT',
                                    'Thái Lan' => 'TH',
                                    'Việt Nam' => 'VN',
                                    'Brazil' => 'BR',
                                    'Mexico' => 'MX',
                                    'Nga' => 'RU',
                                    'Thụy Điển' => 'SE'
                                ];
                            @endphp

                            @foreach($countries as $name => $code)
                                <a href="{{ route('movies.filter', ['country' => $code, 'country_name' => $name]) }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">{{ $name }}</a>
                            @endforeach
                        </div>
                    </div>
                </li>
            </ul>

            {{-- Phần bên phải: tìm kiếm, giỏ hàng và avatar (Desktop only) --}}
            <div class="hidden md:flex items-center">
                {{-- Ô tìm kiếm --}}
                @livewire('search-dropdown')

                {{-- Admin Panel button cho admin --}}
                {{-- @auth
                    @if(Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="md:ml-4 px-4 py-2 bg-orange-500 hover:bg-orange-600 rounded-lg text-sm font-semibold text-gray-900 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Admin Panel
                    </a>
                    @endif
                @endauth --}}

                {{-- Đăng nhập/Đăng ký hoặc Menu người dùng với giỏ hàng --}}
                @auth
                    <div class="md:ml-4 relative" x-data="{ isOpen: false }">
                        <div @click="isOpen = !isOpen" class="flex items-center cursor-pointer">
                            <img src="{{ asset('images/Avatar/avatar.jpg') }}" alt="{{ Auth::user()->name }}" class="rounded-full w-8 h-8">
                            <span class="ml-2">{{ Auth::user()->name }}</span>
                            <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div x-show="isOpen" @click.away="isOpen = false" class="absolute right-0 mt-2 py-2 w-48 bg-gray-800 rounded-md shadow-lg z-20">
                            {{-- Giỏ hàng trong dropdown --}}
                            <a href="{{ route('cart.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span>Giỏ hàng</span>
                                @php
                                    $cartDropdown = app(\App\Livewire\CartDropdown::class);
                                    $cartCount = $cartDropdown ? $cartDropdown->cartCount : 0;
                                @endphp
                                @if($cartCount > 0)
                                    <span class="ml-auto bg-orange-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                        {{ $cartCount }}
                                    </span>
                                @endif
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Tài khoản</span>
                            </a>
                            <a href="{{ route('orders.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 3v4a1 1 0 001 1h4"></path>
                                </svg>
                                <span>Đơn hàng</span>
                            </a>
                            {{-- Thêm liên kết Admin Panel nếu người dùng là admin --}}
                            @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-orange-400 font-medium">Admin Panel</span>
                            </a>
                            @endif
                            <div class="border-t border-gray-700 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span>Đăng xuất</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    {{-- Hiển thị biểu tượng giỏ hàng mini bên ngoài dropdown khi đã đăng nhập --}}
                    {{-- <div class="md:ml-2 mt-3 md:mt-0">
                        @livewire('cart-dropdown')
                    </div> --}}
                @else
                    <div class="md:ml-4 flex items-center">
                        <a href="{{ route('login') }}" class="mr-4 text-sm text-gray-300 hover:text-white">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="text-sm text-gray-300 hover:text-white">Đăng ký</a>
                        {{-- Thêm link trực tiếp đến trang Admin --}}
                        @auth
                            @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="ml-4 text-sm text-orange-400 hover:text-orange-300 font-medium">
                                Admin Panel
                            </a>
                            @endif
                        @endauth
                        {{-- Hiện biểu tượng giỏ hàng mờ khi chưa đăng nhập --}}
                        <a href="{{ route('login') }}" class="ml-4" title="Đăng nhập để xem giỏ hàng">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>
    {{-- Nội dung chính sẽ được đưa vào đây từ các view con --}}
    @yield('content')
    {{-- Scripts được thêm từ các view con --}}
    @yield('scripts')
    {{-- Nhúng livewire - make sure these are at the end of body --}}
    @livewireScripts
    @stack('scripts')
    <script>
        // Script theo dõi scroll để thêm hiệu ứng cho header
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.querySelector('.fixed-header');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 10) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });

            // Fix for search form submission
            const searchForms = document.querySelectorAll('.search-form');
            searchForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const searchInput = this.querySelector('input[name="query"]');
                    const searchValue = searchInput.value.trim();
                    if (searchValue.length >= 2) {
                        window.location.href = '/search?query=' + encodeURIComponent(searchValue);
                    }
                });
            });

            // Ensure mobile menu closes when clicking a link
            const mobileMenuLinks = document.querySelectorAll('.mobile-menu a');
            mobileMenuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const mobileMenu = document.querySelector('.mobile-menu');
                    if (mobileMenu) {
                        Alpine.store('navStore').closeMobileMenu();
                    }
                });
            });
        });
    </script>
</body>
</html>
