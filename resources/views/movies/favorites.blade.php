<x-app-layout>
    <div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-white">
                    {{ __('Quản lý tài khoản') }}
                </h2>
                <p class="mt-1 text-sm text-gray-400">
                    {{ __('Quản lý thông tin, mật khẩu và các dữ liệu khác của tài khoản') }}
                </p>
            </div>

            <div class="flex flex-col md:flex-row gap-8">
                <!-- Sidebar Navigation -->
                <div class="md:w-1/4 lg:w-1/5">
                    <div class="bg-gray-800 rounded-lg shadow-md border border-gray-700 sticky top-6">
                        <div class="p-4 border-b border-gray-700">
                            <div class="flex items-center">
                                <img src="{{ asset('images/Avatar/avatar.jpg') }}" alt="{{ Auth::user()->name }}" class="w-12 h-12 rounded-full mr-3">
                                <div>
                                    <h3 class="font-medium text-white">{{ Auth::user()->name }}</h3>
                                    <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>

                        <nav class="p-2">
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('profile.edit') }}"
                                       class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('profile.edit') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Thông tin tài khoản
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('profile.password') }}"
                                       class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('profile.password') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                        </svg>
                                        Đổi mật khẩu
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('orders.index') }}"
                                       class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('orders.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                        Đơn hàng của tôi
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('movies.favorites') }}"
                                       class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('movies.favorites') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                        Phim yêu thích
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                                        @csrf
                                        <button type="submit"
                                           class="flex w-full items-center px-4 py-2.5 rounded-md text-gray-300 hover:bg-red-700 hover:text-white">
                                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="md:w-3/4 lg:w-4/5">
                    <!-- Favorite Movies Section -->
                    <div class="bg-gray-800 p-6 rounded-lg shadow-md border border-gray-700">
                        <div class="text-xl font-semibold text-white mb-4">{{ __('Phim yêu thích') }}</div>

                        <div class="p-4">
                            @if(isset($favoriteMovies) && count($favoriteMovies) > 0)
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                                    @foreach($favoriteMovies as $movie)
                                        <div class="movie-card bg-gray-750 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                                            <a href="{{ route('movies.show', $movie->id) }}">
                                                <img src="https://image.tmdb.org/t/p/w500{{ $movie->poster_path }}"
                                                    alt="{{ $movie->title }}"
                                                    class="w-full h-auto object-cover">
                                            </a>
                                            <div class="p-4">
                                                <h3 class="text-white font-medium text-lg line-clamp-1">{{ $movie->title }}</h3>
                                                <p class="text-gray-400 text-sm mt-1">{{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}</p>

                                                <div class="flex justify-between items-center mt-3">
                                                    <span class="bg-orange-600 text-white text-xs px-2 py-1 rounded-md">{{ number_format($movie->price, 0, ',', '.') }}đ</span>

                                                    <form action="{{ route('movies.favorites.remove', $movie->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-300 transition">
                                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-400 mb-2">Bạn chưa có phim yêu thích nào</h3>
                                    <p class="text-gray-500 mb-6">Hãy thêm phim vào danh sách yêu thích bằng cách nhấn vào biểu tượng trái tim khi xem chi tiết phim</p>
                                    <a href="{{ route('movie.index') }}" class="bg-orange-500 hover:bg-orange-600 text-gray-900 font-medium py-2 px-4 rounded-lg transition">
                                        Khám phá phim ngay
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
