@extends('layouts.main')

@section('content')
<div class="container mx-auto px-4 pt-16">
    <div class="flex flex-col md:flex-row items-center justify-between mb-8">
        <h2 class="text-3xl font-semibold">{{ $filterTitle }}</h2>

        <div class="flex flex-col md:flex-row items-center">
            <p class="text-gray-400 mt-2 md:mt-0 md:mr-4">
                Tìm thấy {{ $totalResults }} kết quả
            </p>

            {{-- Thêm bộ lọc phân loại theo giá --}}
            <div class="mt-4 md:mt-0 flex items-center">
                <span class="text-gray-400 mr-2">Sắp xếp theo:</span>
                <select id="sort-filter-results" class="bg-gray-800 text-white rounded border border-gray-700 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="default">Mặc định</option>
                    <option value="price-asc">Giá: Thấp đến cao</option>
                    <option value="price-desc">Giá: Cao đến thấp</option>
                    <option value="vote-desc">Đánh giá: Cao đến thấp</option>
                    <option value="date-desc">Ngày phát hành: Mới nhất</option>
                    <option value="date-asc">Ngày phát hành: Cũ nhất</option>
                </select>
            </div>
        </div>
    </div>

    @if(count($movies) > 0)
        <div id="filter-results-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-8">
            @foreach ($movies as $movie)
                <div class="movie mt-8">
                    <a href="{{ route('movies.show', $movie['id']) }}">
                        @if(isset($movie['poster_path']) && $movie['poster_path'])
                            <img src="{{ 'https://image.tmdb.org/t/p/w500/'.$movie['poster_path'] }}" alt="{{ $movie['title'] }}" class="hover:opacity-75 transition ease-in-out duration-150 rounded h-80 w-full object-cover">
                        @else
                            <div class="w-full h-80 bg-gray-800 flex items-center justify-center rounded">
                                <span class="text-gray-500">No image available</span>
                            </div>
                        @endif
                    </a>
                    <div class="mt-2">
                        <a href="{{ route('movies.show', $movie['id']) }}" class="text-lg mt-2 hover:text-gray-300 line-clamp-1">{{ $movie['title'] }}</a>
                        <div class="flex items-center text-gray-400 text-sm mt-1">
                            <svg class="fill-current text-orange-500 w-4 h-4" viewBox="0 0 24 24"><g data-name="Layer 2"><path d="M17.56 21a1 1 0 01-.46-.11L12 18.22l-5.1 2.67a1 1 0 01-1.45-1.06l1-5.63-4.12-4a1 1 0 01-.25-1 1 1 0 01.81-.68l5.7-.83 2.51-5.13a1 1 0 011.8 0l2.54 5.12 5.7.83a1 1 0 01.81.68 1 1 0 01-.25 1l-4.12 4 1 5.63a1 1 0 01-.4 1 1 1 0 01-.62.18z" data-name="star"/></g></svg>
                            <span class="ml-1">{{ isset($movie['vote_average']) ? round($movie['vote_average'] * 10) . '%' : 'N/A' }}</span>
                            <span class="mx-2">|</span>
                            <span>{{ isset($movie['release_date']) ? \Carbon\Carbon::parse($movie['release_date'])->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="text-gray-400 text-sm line-clamp-1">
                            @if(isset($movie['genre_ids']) && count($movie['genre_ids']) > 0)
                                @foreach($movie['genre_ids'] as $genreId)
                                    {{ $genres->get($genreId) }}@if(!$loop->last), @endif
                                @endforeach
                            @else
                                N/A
                            @endif
                        </div>

                        {{-- Hiển thị giá phim --}}
                        <div class="mt-2 flex items-center">
                            <span class="text-orange-500 font-medium">
                                @php
                                    // Tính giá dựa trên vote_average
                                    $basePrice = 120000; // Giá cơ bản
                                    $maxPrice = 200000;  // Giá tối đa
                                    $voteAverage = $movie['vote_average'] ?? 5;
                                    $price = min($maxPrice, max($basePrice, intval($basePrice + ($voteAverage/10) * ($maxPrice - $basePrice))));

                                    // Lưu giá vào metadata javascript
                                    $movie['price'] = $price;
                                @endphp
                                {{ number_format($price, 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Phân trang --}}
        <div class="flex justify-center items-center mt-16 mb-8">
            @if($currentPage > 1)
                <a href="{{ url('/movies/filter?page=' . ($currentPage - 1) .
                    ($year ? '&year=' . $year : '') .
                    ($genreId ? '&genre=' . $genreId . '&genre_name=' . $genreName : '') .
                    ($countryCode ? '&country=' . $countryCode . '&country_name=' . $countryName : '')) }}"
                    class="bg-gray-700 hover:bg-gray-600 rounded-l-md px-4 py-2 text-white mr-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
            @endif

            <div class="flex">
                @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                    <a href="{{ url('/movies/filter?page=' . $i .
                        ($year ? '&year=' . $year : '') .
                        ($genreId ? '&genre=' . $genreId . '&genre_name=' . $genreName : '') .
                        ($countryCode ? '&country=' . $countryCode . '&country_name=' . $countryName : '')) }}"
                        class="px-4 py-2 mx-1 {{ $currentPage == $i ? 'bg-orange-500 text-white' : 'bg-gray-700 hover:bg-gray-600 text-white' }} rounded">
                        {{ $i }}
                    </a>
                @endfor
            </div>

            @if($currentPage < $totalPages)
                <a href="{{ url('/movies/filter?page=' . ($currentPage + 1) .
                    ($year ? '&year=' . $year : '') .
                    ($genreId ? '&genre=' . $genreId . '&genre_name=' . $genreName : '') .
                    ($countryCode ? '&country=' . $countryCode . '&country_name=' . $countryName : '')) }}"
                    class="bg-gray-700 hover:bg-gray-600 rounded-r-md px-4 py-2 text-white ml-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            @endif
        </div>
    @else
        <div class="flex flex-col items-center justify-center mt-16">
            <svg class="w-16 h-16 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <p class="text-xl text-gray-400 mb-2">Không tìm thấy kết quả phù hợp</p>
            <p class="text-gray-500 mb-6">Vui lòng thử lại với bộ lọc khác</p>
            <a href="{{ route('movie.index') }}" class="bg-gray-700 hover:bg-gray-600 px-6 py-3 rounded text-white">
                Quay lại trang chủ
            </a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lưu dữ liệu kết quả lọc
        let filterResults = @json($movies);

        // Tính giá cho mỗi bộ phim
        function calculatePrice(movie) {
            const basePrice = 120000;
            const maxPrice = 200000;
            const voteAverage = movie.vote_average || 5;
            return Math.min(maxPrice, Math.max(basePrice, parseInt(basePrice + (voteAverage/10) * (maxPrice - basePrice))));
        }

        // Thêm giá vào dữ liệu phim
        filterResults.forEach(movie => {
            movie.price = calculatePrice(movie);
        });

        // Xử lý sắp xếp cho kết quả lọc
        document.getElementById('sort-filter-results')?.addEventListener('change', function() {
            const sortOrder = this.value;
            sortMovies('filter-results-grid', filterResults, sortOrder);
        });

        // Hàm sắp xếp và hiển thị lại phim
        function sortMovies(gridId, movies, sortOrder) {
            // Sắp xếp mảng phim theo tiêu chí
            let sortedMovies = [...movies];

            if (sortOrder === 'price-asc') {
                sortedMovies.sort((a, b) => a.price - b.price);
            } else if (sortOrder === 'price-desc') {
                sortedMovies.sort((a, b) => b.price - a.price);
            } else if (sortOrder === 'vote-desc') {
                sortedMovies.sort((a, b) => (b.vote_average || 0) - (a.vote_average || 0));
            } else if (sortOrder === 'date-desc') {
                sortedMovies.sort((a, b) => {
                    const dateA = a.release_date ? new Date(a.release_date) : new Date(0);
                    const dateB = b.release_date ? new Date(b.release_date) : new Date(0);
                    return dateB - dateA;
                });
            } else if (sortOrder === 'date-asc') {
                sortedMovies.sort((a, b) => {
                    const dateA = a.release_date ? new Date(a.release_date) : new Date(0);
                    const dateB = b.release_date ? new Date(b.release_date) : new Date(0);
                    return dateA - dateB;
                });
            }

            // Lấy grid container
            const grid = document.getElementById(gridId);
            if (!grid) return;

            // Lưu trữ vị trí scroll hiện tại
            const scrollPosition = window.scrollY;

            // Xóa tất cả nội dung hiện tại
            grid.innerHTML = '';

            // Tạo HTML cho mỗi phim và thêm vào grid
            sortedMovies.forEach(movie => {
                // Lấy genre names
                let genreNames = '';
                if (movie.genre_ids && movie.genre_ids.length > 0) {
                    // Tạo map của genre IDs và names từ PHP
                    const genreMap = @json($genres);
                    genreNames = movie.genre_ids.map(id => genreMap[id] || '').filter(Boolean).join(', ');
                }

                const movieHtml = createMovieCard(movie, genreNames);
                grid.innerHTML += movieHtml;
            });

            // Khôi phục vị trí scroll
            window.scrollTo(0, scrollPosition);
        }

        // Hàm tạo HTML cho thẻ phim
        function createMovieCard(movie, genres) {
            const releaseDate = movie.release_date
                ? new Date(movie.release_date).toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                  })
                : 'N/A';

            const posterPath = movie.poster_path
                ? `<img src="https://image.tmdb.org/t/p/w500/${movie.poster_path}" alt="${movie.title}" class="hover:opacity-75 transition ease-in-out duration-150 rounded h-80 w-full object-cover">`
                : `<div class="w-full h-80 bg-gray-800 flex items-center justify-center rounded"><span class="text-gray-500">No image available</span></div>`;

            const price = movie.price.toLocaleString('vi-VN') + 'đ';
            const voteAverage = (movie.vote_average ? Math.round(movie.vote_average * 10) : 'N/A') + '%';

            return `
                <div class="movie mt-8">
                    <a href="/movies/${movie.id}">
                        ${posterPath}
                    </a>
                    <div class="mt-2">
                        <a href="/movies/${movie.id}" class="text-lg mt-2 hover:text-gray-300 line-clamp-1">${movie.title}</a>
                        <div class="flex items-center text-gray-400 text-sm mt-1">
                            <svg class="fill-current text-orange-500 w-4 h-4" viewBox="0 0 24 24"><g data-name="Layer 2"><path d="M17.56 21a1 1 0 01-.46-.11L12 18.22l-5.1 2.67a1 1 0 01-1.45-1.06l1-5.63-4.12-4a1 1 0 01-.25-1 1 1 0 01.81-.68l5.7-.83 2.51-5.13a1 1 0 011.8 0l2.54 5.12 5.7.83a1 1 0 01.81.68 1 1 0 01-.25 1l-4.12 4 1 5.63a1 1 0 01-.4 1 1 1 0 01-.62.18z" data-name="star"/></g></svg>
                            <span class="ml-1">${voteAverage}</span>
                            <span class="mx-2">|</span>
                            <span>${releaseDate}</span>
                        </div>
                        <div class="text-gray-400 text-sm line-clamp-1">${genres || 'N/A'}</div>

                        <div class="mt-2 flex items-center">
                            <span class="text-orange-500 font-medium">${price}</span>
                        </div>
                    </div>
                </div>
            `;
        }
    });
</script>
@endsection
