@extends('layouts.main')

@section('content')
<div class="search-results-container bg-gray-900 min-h-screen">
    <div class="container mx-auto px-4 py-10">
        <!-- Hero search section -->
        <div class="mb-10 relative rounded-xl overflow-hidden bg-gradient-to-r from-blue-900 to-indigo-900 shadow-2xl">
            <!-- Background pattern -->
            <div class="absolute inset-0 opacity-20">
                <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
                    <defs>
                        <pattern id="grid-pattern" width="20" height="20" patternUnits="userSpaceOnUse">
                            <path d="M 20 0 L 0 0 0 20" fill="none" stroke="white" stroke-width="0.5" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid-pattern)" />
                </svg>
            </div>

            <div class="relative z-10 py-10 px-8 md:py-16 md:px-12">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-4 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Kết quả tìm kiếm
                </h1>

                @if(isset($query))
                    <p class="text-xl text-gray-200 mb-6">Kết quả cho: <span class="font-semibold text-blue-300">"{{ $query }}"</span></p>
                @endif

                <!-- Enhanced search form -->
                <form action="{{ route('movies.search') }}" method="GET" class="search-form relative max-w-3xl">
                    <div class="flex flex-col md:flex-row">
                        <div class="relative flex-1">
                            <input type="text" name="query" value="{{ $query ?? '' }}"
                                class="w-full px-5 py-4 pr-12 rounded-l-lg bg-white/10 backdrop-blur-sm border border-blue-500/50 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent text-lg"
                                placeholder="Tìm kiếm phim..." required>
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                <svg class="w-5 h-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <button type="submit"
                            class="mt-3 md:mt-0 md:ml-2 px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white font-medium rounded-r-lg transition duration-300 shadow-lg hover:shadow-blue-500/30 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-gray-900">
                            Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results section -->
        <div class="bg-gray-800/60 rounded-xl p-6 shadow-lg border border-gray-700">
            @if(isset($results) && count($results) > 0)
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl text-white font-semibold flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Tìm thấy {{ count($results) }} kết quả
                    </h2>
                    <div class="flex items-center text-sm text-gray-400">
                        <span>Sắp xếp theo: </span>
                        <select id="sort-results" class="ml-2 bg-gray-700 border border-gray-600 text-white rounded-md px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="relevance">Độ phù hợp</option>
                            <option value="year-desc">Năm (Mới → Cũ)</option>
                            <option value="year-asc">Năm (Cũ → Mới)</option>
                            <option value="rating-desc">Xếp hạng (Cao → Thấp)</option>
                        </select>
                    </div>
                </div>

                <div class="movie-grid grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
                    @foreach($results as $movie)
                        <div class="movie-card group bg-gray-800 rounded-lg overflow-hidden border border-gray-700 shadow-md hover:shadow-blue-500/10 hover:border-blue-500/50 transition duration-300">
                            <a href="{{ route('movies.show', $movie['id']) }}" class="block">
                                <div class="relative overflow-hidden">
                                    @if(isset($movie['poster_path']) && $movie['poster_path'])
                                        <img class="w-full h-64 object-cover transform group-hover:scale-105 transition duration-300"
                                             src="https://image.tmdb.org/t/p/w500{{ $movie['poster_path'] }}"
                                             alt="{{ $movie['title'] }}">
                                    @else
                                        <div class="w-full h-64 bg-gray-700 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif

                                    <!-- Rating badge -->
                                    @if(isset($movie['vote_average']))
                                        <div class="absolute top-2 right-2 bg-blue-900/80 text-white text-sm font-bold px-2.5 py-1 rounded-md backdrop-blur-sm flex items-center">
                                            <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            {{ number_format($movie['vote_average'], 1) }}
                                        </div>
                                    @endif

                                    <!-- Year badge -->
                                    @if(isset($movie['release_date']))
                                        <div class="absolute top-2 left-2 bg-gray-900/80 text-gray-200 text-xs font-medium px-2.5 py-0.5 rounded backdrop-blur-sm">
                                            {{ \Carbon\Carbon::parse($movie['release_date'])->format('Y') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="p-4">
                                    <h3 class="text-white font-semibold text-lg group-hover:text-blue-400 transition duration-300 line-clamp-1">{{ $movie['title'] }}</h3>

                                    @if(isset($movie['genre_ids']) && is_array($movie['genre_ids']))
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @php
                                                $genreMap = [
                                                    28 => 'Hành động',
                                                    12 => 'Phiêu lưu',
                                                    16 => 'Hoạt hình',
                                                    35 => 'Hài',
                                                    80 => 'Hình sự',
                                                    99 => 'Tài liệu',
                                                    18 => 'Chính kịch',
                                                    10751 => 'Gia đình',
                                                    14 => 'Giả tưởng',
                                                    36 => 'Lịch sử',
                                                    27 => 'Kinh dị',
                                                    10402 => 'Âm nhạc',
                                                    9648 => 'Bí ẩn',
                                                    10749 => 'Lãng mạn',
                                                    878 => 'Khoa học viễn tưởng',
                                                    10770 => 'TV Movie',
                                                    53 => 'Gây cấn',
                                                    10752 => 'Chiến tranh',
                                                    37 => 'Miền Tây'
                                                ];
                                                $displayedGenres = 0;
                                            @endphp

                                            @foreach($movie['genre_ids'] as $genreId)
                                                @if(isset($genreMap[$genreId]) && $displayedGenres < 2)
                                                    <span class="text-xs text-gray-300 bg-gray-700 px-2 py-1 rounded">{{ $genreMap[$genreId] }}</span>
                                                    @php $displayedGenres++; @endphp
                                                @endif
                                            @endforeach

                                            @if(count($movie['genre_ids']) > 2)
                                                <span class="text-xs text-gray-400">+{{ count($movie['genre_ids']) - 2 }}</span>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between mt-4">
                                        <span class="text-orange-500 font-bold">120.000đ</span>

                                        <button
                                            class="add-to-cart-button flex items-center bg-blue-600 hover:bg-blue-700 transition-colors text-white rounded-full p-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-gray-800"
                                            data-tmdb-id="{{ $movie['id'] }}"
                                            data-title="{{ $movie['title'] }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @elseif(isset($query))
                <div class="py-10 flex flex-col items-center justify-center text-center">
                    <svg class="w-16 h-16 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-white mb-2">Không tìm thấy kết quả nào</h3>
                    <p class="text-gray-400 max-w-md mx-auto">Không tìm thấy phim nào khớp với "{{ $query }}". Vui lòng thử các từ khóa khác.</p>

                    <div class="mt-8">
                        <h4 class="text-lg text-gray-300 mb-3">Bạn có thể thử:</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li>• Kiểm tra lỗi chính tả</li>
                            <li>• Sử dụng các từ khóa ngắn hơn</li>
                            <li>• Sử dụng các từ khóa khác</li>
                        </ul>
                    </div>
                </div>

                <!-- Hiển thị gợi ý phim phổ biến khi không tìm thấy kết quả -->
                @if(isset($alternativeResults) && count($alternativeResults) > 0)
                <div class="mt-8 pt-8 border-t border-gray-700">
                    <h3 class="text-xl font-semibold text-white mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Có thể bạn quan tâm
                    </h3>

                    <div class="movie-grid grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
                        @foreach($alternativeResults as $movie)
                            <div class="movie-card group bg-gray-800 rounded-lg overflow-hidden border border-gray-700 shadow-md hover:shadow-blue-500/10 hover:border-blue-500/50 transition duration-300">
                                <a href="{{ route('movies.show', $movie['id']) }}" class="block">
                                    <!-- Hiển thị poster phim -->
                                    <div class="relative overflow-hidden">
                                        @if(isset($movie['poster_path']) && $movie['poster_path'])
                                            <img class="w-full h-64 object-cover transform group-hover:scale-105 transition duration-300"
                                                src="https://image.tmdb.org/t/p/w500{{ $movie['poster_path'] }}"
                                                alt="{{ $movie['title'] }}">
                                        @else
                                            <div class="w-full h-64 bg-gray-700 flex items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif

                                        <!-- Rating và Year badges -->
                                        @if(isset($movie['vote_average']))
                                            <div class="absolute top-2 right-2 bg-blue-900/80 text-white text-sm font-bold px-2.5 py-1 rounded-md backdrop-blur-sm flex items-center">
                                                <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                {{ number_format($movie['vote_average'], 1) }}
                                            </div>
                                        @endif
                                        @if(isset($movie['release_date']))
                                            <div class="absolute top-2 left-2 bg-gray-900/80 text-gray-200 text-xs font-medium px-2.5 py-0.5 rounded backdrop-blur-sm">
                                                {{ \Carbon\Carbon::parse($movie['release_date'])->format('Y') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="p-4">
                                        <h3 class="text-white font-semibold text-lg group-hover:text-blue-400 transition duration-300 line-clamp-1">
                                            {{ $movie['title'] }}
                                        </h3>

                                        <!-- Hiển thị thể loại phim -->
                                        @if(isset($movie['genre_ids']) && is_array($movie['genre_ids']))
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                @php
                                                    $genreMap = [
                                                        28 => 'Hành động',
                                                        12 => 'Phiêu lưu',
                                                        16 => 'Hoạt hình',
                                                        35 => 'Hài',
                                                        80 => 'Hình sự',
                                                        99 => 'Tài liệu',
                                                        18 => 'Chính kịch',
                                                        10751 => 'Gia đình',
                                                        14 => 'Giả tưởng',
                                                        36 => 'Lịch sử',
                                                        27 => 'Kinh dị',
                                                        10402 => 'Âm nhạc',
                                                        9648 => 'Bí ẩn',
                                                        10749 => 'Lãng mạn',
                                                        878 => 'Khoa học viễn tưởng',
                                                        10770 => 'TV Movie',
                                                        53 => 'Gây cấn',
                                                        10752 => 'Chiến tranh',
                                                        37 => 'Miền Tây'
                                                    ];
                                                    $displayedGenres = 0;
                                                @endphp

                                                @foreach($movie['genre_ids'] as $genreId)
                                                    @if(isset($genreMap[$genreId]) && $displayedGenres < 2)
                                                        <span class="text-xs text-gray-300 bg-gray-700 px-2 py-1 rounded">{{ $genreMap[$genreId] }}</span>
                                                        @php $displayedGenres++; @endphp
                                                    @endif
                                                @endforeach

                                                @if(count($movie['genre_ids']) > 2)
                                                    <span class="text-xs text-gray-400">+{{ count($movie['genre_ids']) - 2 }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @else
                <div class="py-10 flex flex-col items-center justify-center text-center">
                    <svg class="w-16 h-16 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-white mb-2">Tìm kiếm phim yêu thích</h3>
                    <p class="text-gray-400 max-w-md mx-auto">Nhập từ khóa tìm kiếm vào ô bên trên để tìm phim bạn yêu thích.</p>
                </div>
            @endif
        </div>

        <!-- Popular search terms -->
        <div class="mt-10 bg-gray-800/60 rounded-xl p-6 shadow-lg border border-gray-700">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                Từ khóa phổ biến
            </h3>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('movies.search', ['query' => 'Avengers']) }}" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 transition-colors text-gray-200 rounded-lg text-sm">Avengers</a>
                <a href="{{ route('movies.search', ['query' => 'Star Wars']) }}" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 transition-colors text-gray-200 rounded-lg text-sm">Star Wars</a>
                <a href="{{ route('movies.search', ['query' => 'Marvel']) }}" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 transition-colors text-gray-200 rounded-lg text-sm">Marvel</a>
                <a href="{{ route('movies.search', ['query' => 'DC']) }}" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 transition-colors text-gray-200 rounded-lg text-sm">DC</a>
                <a href="{{ route('movies.search', ['query' => 'Harry Potter']) }}" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 transition-colors text-gray-200 rounded-lg text-sm">Harry Potter</a>
                <a href="{{ route('movies.search', ['query' => 'Spider-Man']) }}" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 transition-colors text-gray-200 rounded-lg text-sm">Spider-Man</a>
                <a href="{{ route('movies.search', ['query' => 'Fast & Furious']) }}" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 transition-colors text-gray-200 rounded-lg text-sm">Fast & Furious</a>
                <a href="{{ route('movies.search', ['query' => 'Jurassic Park']) }}" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 transition-colors text-gray-200 rounded-lg text-sm">Jurassic Park</a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Thêm chức năng sắp xếp kết quả tìm kiếm
        const sortSelect = document.getElementById('sort-results');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const movieCards = document.querySelectorAll('.movie-card');
                const movieGrid = document.querySelector('.movie-grid');
                const cardsArray = Array.from(movieCards);

                switch(this.value) {
                    case 'year-desc':
                        cardsArray.sort((a, b) => {
                            const yearA = a.querySelector('.bg-gray-900\\/80')?.textContent.trim() || '0';
                            const yearB = b.querySelector('.bg-gray-900\\/80')?.textContent.trim() || '0';
                            return parseInt(yearB) - parseInt(yearA);
                        });
                        break;
                    case 'year-asc':
                        cardsArray.sort((a, b) => {
                            const yearA = a.querySelector('.bg-gray-900\\/80')?.textContent.trim() || '0';
                            const yearB = b.querySelector('.bg-gray-900\\/80')?.textContent.trim() || '0';
                            return parseInt(yearA) - parseInt(yearB);
                        });
                        break;
                    case 'rating-desc':
                        cardsArray.sort((a, b) => {
                            const ratingA = parseFloat(a.querySelector('.bg-blue-900\\/80')?.textContent.trim() || '0');
                            const ratingB = parseFloat(b.querySelector('.bg-blue-900\\/80')?.textContent.trim() || '0');
                            return ratingB - ratingA;
                        });
                        break;
                    default: // 'relevance', Original order
                        // Will use the existing order from the backend
                        return;
                }

                // Clear the grid and append sorted items
                movieGrid.innerHTML = '';
                cardsArray.forEach(card => {
                    movieGrid.appendChild(card);
                });
            });
        }

        // Thêm chức năng cho các nút "Thêm vào giỏ hàng"
        const addToCartButtons = document.querySelectorAll('.add-to-cart-button');

        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const tmdbId = this.dataset.tmdbId;
                const title = this.dataset.title;

                // Hiệu ứng loading
                this.innerHTML = `<div class="spinner"></div>`;
                this.disabled = true;

                // Gửi request thêm vào giỏ hàng
                fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        tmdbId: tmdbId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Restore button with check icon
                        this.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>`;
                        this.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                        this.classList.add('bg-green-600', 'hover:bg-green-700');

                        // Update cart count if available
                        if (data.cart_count && window.updateCartCount) {
                            window.updateCartCount(data.cart_count);
                        }

                        // Show notification
                        if (window.showNotification) {
                            window.showNotification('success', `Đã thêm "${title}" vào giỏ hàng`);
                        }
                    } else {
                        // Restore original button
                        this.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>`;

                        // Show error notification
                        if (window.showNotification) {
                            window.showNotification('error', data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng');
                        }
                    }

                    this.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);

                    // Restore original button
                    this.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>`;
                    this.disabled = false;

                    // Show error notification
                    if (window.showNotification) {
                        window.showNotification('error', 'Có lỗi xảy ra khi thêm vào giỏ hàng');
                    }
                });
            });
        });
    });
</script>
@endsection
