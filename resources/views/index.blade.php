@extends('layouts.main')

@section('content')
    <!-- Các component phim sẽ sử dụng route 'movies.show' -->
    <div class="container mx-auto px-4 pt-16">
        <div class="popular-movies">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
                <h2 class="uppercase tracking-wider text-orange-500 text-lg font-semibold">
                    Popular Movies
                </h2>

                {{-- Thêm bộ lọc phân loại theo giá --}}
                <div class="mt-4 sm:mt-0 flex items-center">
                    <span class="text-gray-400 mr-2">Sắp xếp theo:</span>
                    <select id="sort-price" class="bg-gray-800 text-white rounded border border-gray-700 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="default">Mặc định</option>
                        <option value="price-asc">Giá: Thấp đến cao</option>
                        <option value="price-desc">Giá: Cao đến thấp</option>
                    </select>
                </div>
            </div>

            <div id="movies-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8">
                {{--
                    Phần danh sách phim phổ biến
                    Sử dụng movie-card component để hiển thị từng phim

                    <x-movie-card> gọi đến component MovieCard trong app/View/Components
                    :movie="$movie" truyền biến $movie vào thuộc tính movie của component
                    :genres="$genres" truyền biến $genres vào thuộc tính genres của component
                --}}
                @foreach ($popularMovies as $movie)
                    <x-movie-card :movie="$movie" :genres="$genres" />
                @endforeach
                {{-- Kết thúc phần danh sách phim --}}
            </div>
        </div>

        {{-- Phần phim đang chiếu --}}
        <div class="now-playing-movies py-16">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
                <h2 class="uppercase tracking-wider text-orange-500 text-lg font-semibold">
                    Now Playing
                </h2>

                {{-- Thêm bộ lọc phân loại theo giá --}}
                <div class="mt-4 sm:mt-0 flex items-center">
                    <span class="text-gray-400 mr-2">Sắp xếp theo:</span>
                    <select id="sort-price-now-playing" class="bg-gray-800 text-white rounded border border-gray-700 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="default">Mặc định</option>
                        <option value="price-asc">Giá: Thấp đến cao</option>
                        <option value="price-desc">Giá: Cao đến thấp</option>
                    </select>
                </div>
            </div>

            <div id="now-playing-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-10">
                {{--
                    Sử dụng cùng movie-card component để hiển thị phim đang chiếu
                    Đây là lợi ích của việc sử dụng component - có thể tái sử dụng ở nhiều nơi
                --}}
                @foreach ($nowPlayingMovies as $movie)
                    <x-movie-card :movie="$movie" :genres="$genres" />
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lưu dữ liệu phim phổ biến
        let popularMovies = @json($popularMovies);

        // Lưu dữ liệu phim đang chiếu
        let nowPlayingMovies = @json($nowPlayingMovies);

        // Xử lý sắp xếp cho phim phổ biến
        document.getElementById('sort-price').addEventListener('change', function() {
            const sortOrder = this.value;
            sortMovies('movies-grid', popularMovies, sortOrder);
        });

        // Xử lý sắp xếp cho phim đang chiếu
        document.getElementById('sort-price-now-playing').addEventListener('change', function() {
            const sortOrder = this.value;
            sortMovies('now-playing-grid', nowPlayingMovies, sortOrder);
        });

        // Tính giá cho mỗi bộ phim (giống phần tính trong Blade)
        function calculatePrice(movie) {
            const basePrice = 120000;
            const maxPrice = 200000;
            const voteAverage = movie.vote_average || 5;
            return Math.min(maxPrice, Math.max(basePrice, parseInt(basePrice + (voteAverage/10) * (maxPrice - basePrice))));
        }

        // Thêm giá vào dữ liệu phim
        popularMovies.forEach(movie => {
            movie.price = calculatePrice(movie);
        });

        nowPlayingMovies.forEach(movie => {
            movie.price = calculatePrice(movie);
        });

        // Hàm sắp xếp và hiển thị lại phim
        function sortMovies(gridId, movies, sortOrder) {
            // Sắp xếp mảng phim theo giá
            let sortedMovies = [...movies];

            if (sortOrder === 'price-asc') {
                sortedMovies.sort((a, b) => a.price - b.price);
            } else if (sortOrder === 'price-desc') {
                sortedMovies.sort((a, b) => b.price - a.price);
            }

            // Lấy grid container
            const grid = document.getElementById(gridId);

            // Lưu trữ vị trí scroll hiện tại
            const scrollPosition = window.scrollY;

            // Xóa tất cả nội dung hiện tại
            grid.innerHTML = '';

            // Tạo HTML cho mỗi phim và thêm vào grid
            sortedMovies.forEach(movie => {
                const genres = movie.genre_ids.map(id => {
                    // Tìm genre name từ ID sử dụng dữ liệu từ page
                    const genreElement = document.querySelector(`[data-genre-id="${id}"]`);
                    return genreElement ? genreElement.textContent : '';
                }).filter(Boolean).join(', ');

                const movieHtml = createMovieCard(movie, genres);
                grid.innerHTML += movieHtml;
            });

            // Khôi phục vị trí scroll
            window.scrollTo(0, scrollPosition);
        }

        // Hàm tạo HTML cho thẻ phim
        function createMovieCard(movie, genres) {
            const releaseDate = new Date(movie.release_date).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });

            const price = movie.price.toLocaleString('vi-VN') + 'đ';

            return `
                <div class="mt-8">
                    <a href="/movies/${movie.id}">
                        <img src="https://image.tmdb.org/t/p/w500${movie.poster_path}" alt="${movie.title}" class="hover:opacity-75 transition ease-in-out duration-150">
                    </a>
                    <div class="mt-2">
                        <a href="/movies/${movie.id}" class="text-lg mt-2 hover:text-gray-300">${movie.title}</a>
                        <div class="flex items-center text-gray-400 text-sm mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <path d="M12 .587l3.668 7.568 8.332 1.151-6.064 5.828 1.48 8.279-7.416-3.967-7.417 3.967 1.481-8.279-6.064-5.828 8.332-1.151z" fill="yellow"/>
                            </svg>
                            <span class="ml-1">${Math.round(movie.vote_average * 10)}%</span>
                            <span class="mx-2">|</span>
                            <span>${releaseDate}</span>
                        </div>
                        <div class="text-gray-400 text-sm">${genres}</div>
                        <div class="mt-2 flex items-center">
                            <span class="text-orange-500 font-medium">${price}</span>
                        </div>
                    </div>
                </div>
            `;
        }

        // Thêm data attribute cho genres để sử dụng trong JavaScript
        document.querySelectorAll('.movie-grid').forEach(grid => {
            const genreElements = grid.querySelectorAll('.text-gray-400');
            genreElements.forEach(el => {
                // Parse genre text và thêm data attribute
                const text = el.textContent.trim();
                if (text.includes(',')) {
                    const genres = text.split(',').map(g => g.trim());
                    genres.forEach(genre => {
                        el.setAttribute(`data-genre-${genre.toLowerCase().replace(' ', '-')}`, true);
                    });
                }
            });
        });
    });
</script>
@endsection
