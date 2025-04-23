<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - Tài khoản</title>
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
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #ffffff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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

        /* CSS cho background màu gray-750 */
        .bg-gray-750 {
            background-color: rgba(55, 65, 81, 0.5);
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
    </style>
</head>

<body class="font-sans bg-gray-900 text-white antialiased">
    {{-- Notification component --}}
    @livewire('notification')

    {{-- Thanh điều hướng - Thêm class fixed-header --}}
    <nav class="fixed-header border-b border-gray-800" x-data="{ scrolled: false }" x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 10 })" :class="{ 'scrolled': scrolled }">
        <div class="container mx-auto flex flex-col md:flex-row items-center justify-between px-4 py-6">
            {{-- Menu bên trái --}}
            <ul class="flex items-center">
                {{-- Logo và tên ứng dụng --}}
                <li>
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
                </li>
                {{-- Các liên kết điều hướng --}}
                <li class="md:ml-16 mt-3 md:mt-0">
                    <a href="{{ route('movie.index') }}" class="hover:text-gray-300">Movies</a>
                </li>

                {{-- Filter by Year --}}
                <li class="md:ml-10 mt-3 md:mt-0 relative" x-data="{ yearOpen: false }">
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
                <li class="md:ml-10 mt-3 md:mt-0 relative" x-data="{ genreOpen: false }">
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
                <li class="md:ml-10 mt-3 md:mt-0 relative" x-data="{ countryOpen: false }">
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

            {{-- Phần bên phải: tìm kiếm, giỏ hàng và avatar --}}
            <div class="flex flex-col md:flex-row items-center">
                {{-- Ô tìm kiếm --}}
                @livewire('search-dropdown')

                {{-- Menu người dùng với giỏ hàng --}}
                <div class="md:ml-4 mt-3 md:mt-0 relative" x-data="{ isOpen: false }">
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

                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Tài khoản</span>
                        </a>

                        {{-- Thêm liên kết Admin Panel nếu người dùng là admin --}}
                        @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span class="text-orange-500 font-medium">Admin Panel</span>
                        </a>
                        @endif

                        <a href="{{ route('orders.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            <span>Đơn hàng</span>
                        </a>

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
            </div>
        </div>
    </nav>

    {{-- Nội dung chính --}}
    <main>
        {{ $slot }}
    </main>

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
        });
    </script>
</body>
</html>
