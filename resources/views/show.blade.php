@extends('layouts.main')

@section('content')
    {{-- Phần tiêu đề phim --}}
    {{-- Phần thông tin phim --}}
    <div class="movie-info border-b border-gray-800">
        <div class="container mx-auto px-4 py-16 flex flex-col md:flex-row">
            {{-- Poster phim --}}
            <img src="{{ 'https://image.tmdb.org/t/p/w500/'.$movie['poster_path'] }}" alt="{{ $movie['title'] }}" class="w-64 md:w-96 md:h-auto mx-auto md:mx-0" style="max-width: 24rem;">
            <div class="md:ml-24 mt-6 md:mt-0">
                {{-- Tiêu đề và năm phát hành --}}
                <h2 class="text-4xl font-semibold">{{ $movie['title'] }} ({{ \Carbon\Carbon::parse($movie['release_date'])->format('Y') }}) </h2>
                <div class="flex items-center text-gray-400 text-sm mt-1">
                    {{-- Biểu tượng sao đánh giá --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <path
                            d="M12 .587l3.668 7.568 8.332 1.151-6.064 5.828 1.48 8.279-7.416-3.967-7.417 3.967 1.481-8.279-6.064-5.828 8.332-1.151z"
                            fill="yellow" />
                    </svg>
                    <span class="ml-1">{{ round($movie['vote_average'] * 10) }}%</span>
                    <span class="mx-2">|</span>
                    <span>{{ \Carbon\Carbon::parse($movie['release_date'])->format('M d, Y') }}</span>
                    <span class="mx-2">|</span>
                    <span>
                        @foreach($movie['genres'] as $genre)
                            {{ $genre['name'] }}@if(!$loop->last), @endif
                        @endforeach
                    </span>
                </div>

                {{-- Hiển thị giá phim --}}
                <div class="mt-4">
                    <span class="text-white">Giá: </span>
                    <span class="text-orange-500 font-bold text-xl">
                        @php
                            // Tính giá dựa trên vote_average
                            $basePrice = 120000; // Giá cơ bản
                            $maxPrice = 200000;  // Giá tối đa
                            $voteAverage = $movie['vote_average'] ?? 5;
                            $price = min($maxPrice, max($basePrice, intval($basePrice + ($voteAverage/10) * ($maxPrice - $basePrice))));
                        @endphp
                        {{ number_format($price, 0, ',', '.') }}đ
                    </span>
                </div>

                {{-- Mô tả phim --}}
                <p class="text-gray-300 mt-8">
                    {{ $movie['overview'] }}
                </p>
                {{-- Danh sách diễn viên nổi bật --}}
                <div class="mt-12">
                    <h4 class="text-white font-semibold">Featured Cast</h4>
                    <div class="flex mt-4 flex-wrap">
                        @foreach($movie['credits']['crew'] as $crew)
                            @if($crew['job'] === 'Director' || $crew['job'] === 'Producer' || $crew['job'] === 'Screenplay')
                                <div class="mr-8 mb-4">
                                    <div>{{ $crew['name'] }}</div>
                                    <div class="text-sm text-gray-400">{{ $crew['job'] }}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Buttons section - Cả hai nút cạnh nhau --}}
                <div class="flex mt-8 flex-wrap gap-4">
                    {{-- Nút xem trailer - Thêm w-44 để cố định chiều rộng --}}
                    <div x-data="{ isOpen: false }">
                        @if (count($movie['videos']['results']) > 0)
                            <button
                                @click="isOpen = true"
                                class="w-44 h-14 flex items-center justify-center bg-orange-500 text-gray-900 rounded font-semibold px-5 py-4 hover:bg-orange-600 transition ease-in-out duration-150 shadow-lg hover:shadow-orange-500/50"
                            >
                                <svg class="w-6 fill-current" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M10 16.5l6-4.5-6-4.5v9zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                <span class="ml-2">Xem Trailer</span>
                            </button>

                            <template x-if="isOpen">
                                <div
                                    style="background-color: rgba(0, 0, 0, .5);"
                                    class="fixed top-0 left-0 w-full h-full flex items-center shadow-lg overflow-y-auto z-50"
                                >
                                    <div class="container mx-auto lg:px-32 rounded-lg overflow-y-auto">
                                        <div class="bg-gray-900 rounded max-w-3xl mx-auto"> {{-- Thêm max-w-3xl và mx-auto để giới hạn chiều rộng và căn giữa --}}
                                            <div class="flex justify-end pr-4 pt-2">
                                                <button
                                                    @click="isOpen = false"
                                                    @keydown.escape.window="isOpen = false"
                                                    class="text-3xl leading-none hover:text-gray-300">&times;
                                                </button>
                                            </div>
                                            <div class="modal-body px-8 py-8">
                                                <div class="responsive-container overflow-hidden relative" style="padding-top: 56.25%">
                                                    <iframe class="responsive-iframe absolute top-0 left-0 w-full h-full" src="https://www.youtube.com/embed/{{ $movie['videos']['results'][0]['key'] }}" style="border:0;" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        @endif
                    </div>

                    {{-- Nút thêm vào giỏ hàng - Thêm w-44 để cố định chiều rộng --}}
                    <div>
                        @auth
                            <button
                                id="add-to-cart-{{ $movie['id'] }}"
                                onclick="addToCart({{ $movie['id'] }})"
                                class="w-44 h-14 flex items-center justify-center bg-orange-500 text-gray-900 rounded font-semibold px-5 py-4 hover:bg-orange-600 transition ease-in-out duration-150 shadow-lg hover:shadow-orange-500/50"
                                data-movie-id="{{ $movie['id'] }}"
                            >
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M11 9h2V6h3V4h-3V1h-2v3H8v2h3v3zm-4 9c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-9.83-3.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.86-7.01L19.42 4h-.01l-1.1 2-2.76 5H8.53l-.13-.27L6.16 6l-.95-2-.94-2H1v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.13 0-.25-.11-.25-.25z"/>
                                </svg>
                                <span class="ml-2">Thêm vào giỏ</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="w-44 h-14 flex items-center justify-center bg-orange-500 text-gray-900 rounded font-semibold px-5 py-4 hover:bg-orange-600 transition ease-in-out duration-150 shadow-lg hover:shadow-orange-500/50">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M11 9h2V6h3V4h-3V1h-2v3H8v2h3v3zm-4 9c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-9.83-3.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.86-7.01L19.42 4h-.01l-1.1 2-2.76 5H8.53l-.13-.27L6.16 6l-.95-2-.94-2H1v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.13 0-.25-.11-.25-.25z"/>
                                </svg>
                                <span class="ml-2">Đăng nhập để mua</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Phần danh sách diễn viên - Cast Slider --}}
    <div class="movie-cast border-b border-gray-800">
        <div class="container mx-auto px-4 py-16">
            <h2 class="text-4xl font-semibold mb-8">Cast</h2>

            <div class="relative cast-slider">
                {{-- Nút điều hướng trái --}}
                <div class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10">
                    <button class="cast-slider-prev bg-gray-800 rounded-full p-2 focus:outline-none hover:bg-gray-700 transition ease-in-out duration-150">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                </div>

                <div class="cast-slider-container overflow-x-hidden">
                    <div class="cast-slider-wrapper flex transition-all duration-300 ease-in-out">
                        {{-- Lặp qua danh sách diễn viên --}}
                        @foreach($movie['credits']['cast'] as $cast)
                            <div class="cast-slider-slide flex-none px-2">
                                <div class="mt-4">
                                    <a href="#">
                                        @if($cast['profile_path'])
                                            <img src="{{ 'https://image.tmdb.org/t/p/w300/'.$cast['profile_path'] }}" alt="{{ $cast['name'] }}" class="hover:opacity-75 transition ease-in-out duration-150 rounded w-full h-72 object-cover">
                                        @else
                                            <img src="{{ asset('images/Avatar/avatar.jpg') }}" alt="{{ $cast['name'] }}" class="hover:opacity-75 transition ease-in-out duration-150 rounded w-full h-72 object-cover">
                                        @endif
                                    </a>
                                    <div class="mt-2">
                                        <a href="#" class="text-lg mt-2 hover:text-gray-300">{{ $cast['name'] }}</a>
                                        <div class="text-gray-400 text-sm">
                                            {{ $cast['character'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Nút điều hướng phải --}}
                <div class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10">
                    <button class="cast-slider-next bg-gray-800 rounded-full p-2 focus:outline-none hover:bg-gray-700 transition ease-in-out duration-150">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Phần phim tương tự với Slider --}}
    <div class="similar-movies border-b border-gray-800">
        <div class="container mx-auto px-4 py-16">
            <h2 class="uppercase tracking-wider text-orange-500 text-lg font-semibold mb-8">Phim tương tự</h2>

            <div class="relative similar-slider">
                {{-- Nút điều hướng trái --}}
                <div class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10">
                    <button class="similar-slider-prev bg-gray-800 rounded-full p-2 focus:outline-none hover:bg-gray-700 transition ease-in-out duration-150">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                </div>

                <div class="similar-slider-container overflow-x-hidden">
                    <div class="similar-slider-wrapper flex transition-all duration-300 ease-in-out">
                        {{-- Lặp qua danh sách phim tương tự --}}
                        @foreach($similarMovies as $similarMovie)
                            <div class="similar-slider-slide flex-none px-2">
                                <a href="{{ route('movies.show', $similarMovie['id']) }}">
                                    <img src="{{ 'https://image.tmdb.org/t/p/w500/'.$similarMovie['poster_path'] }}" alt="{{ $similarMovie['title'] }}" class="hover:opacity-75 transition ease-in-out duration-150 rounded">
                                </a>
                                <div class="mt-2">
                                    <a href="{{ route('movies.show', $similarMovie['id']) }}" class="text-lg mt-2 hover:text-gray-300">{{ $similarMovie['title'] }}</a>
                                    <div class="flex items-center text-gray-400 text-sm mt-1">
                                        <svg class="fill-current text-orange-500 w-4 h-4" viewBox="0 0 24 24"><g data-name="Layer 2"><path d="M17.56 21a1 1 0 01-.46-.11L12 18.22l-5.1 2.67a1 1 0 01-1.45-1.06l1-5.63-4.12-4a1 1 0 01-.25-1 1 1 0 01.81-.68l5.7-.83 2.51-5.13a1 1 0 011.8 0l2.54 5.12 5.7.83a1 1 0 01.81.68 1 1 0 01-.25 1l-4.12 4 1 5.63a1 1 0 01-.4 1 1 0 01-.62.18z" data-name="star"/></g></svg>
                                        <span class="ml-1">{{ $similarMovie['vote_average'] * 10 }}%</span>
                                        <span class="mx-2">|</span>
                                        <span>{{ \Carbon\Carbon::parse($similarMovie['release_date'])->format('M d, Y') }}</span>
                                    </div>
                                    <div class="text-gray-400 text-sm">
                                        @foreach ($similarMovie['genre_ids'] as $genreId)
                                            {{ $genres->get($genreId) }}@if (!$loop->last), @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Nút điều hướng phải --}}
                <div class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10">
                    <button class="similar-slider-next bg-gray-800 rounded-full p-2 focus:outline-none hover:bg-gray-700 transition ease-in-out duration-150">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Phần phim đề xuất với Slider --}}
    <div class="recommended-movies border-b border-gray-800">
        <div class="container mx-auto px-4 py-16">
            <h2 class="uppercase tracking-wider text-orange-500 text-lg font-semibold mb-8">Phim đề xuất cho bạn</h2>

            <div class="relative recommended-slider">
                {{-- Nút điều hướng trái --}}
                <div class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10">
                    <button class="recommended-slider-prev bg-gray-800 rounded-full p-2 focus:outline-none hover:bg-gray-700 transition ease-in-out duration-150">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                </div>

                <div class="recommended-slider-container overflow-x-hidden">
                    <div class="recommended-slider-wrapper flex transition-all duration-300 ease-in-out">
                        {{-- Lặp qua danh sách phim đề xuất --}}
                        @foreach($recommendedMovies as $recommendedMovie)
                            <div class="recommended-slider-slide flex-none px-2">
                                <a href="{{ route('movies.show', $recommendedMovie['id']) }}">
                                    <img src="{{ 'https://image.tmdb.org/t/p/w500/'.$recommendedMovie['poster_path'] }}" alt="{{ $recommendedMovie['title'] }}" class="hover:opacity-75 transition ease-in-out duration-150 rounded">
                                </a>
                                <div class="mt-2">
                                    <a href="{{ route('movies.show', $recommendedMovie['id']) }}" class="text-lg mt-2 hover:text-gray-300">{{ $recommendedMovie['title'] }}</a>
                                    <div class="flex items-center text-gray-400 text-sm mt-1">
                                        <svg class="fill-current text-orange-500 w-4 h-4" viewBox="0 0 24 24"><g data-name="Layer 2"><path d="M17.56 21a1 1 0 01-.46-.11L12 18.22l-5.1 2.67a1 1 0 01-1.45-1.06l1-5.63-4.12-4a1 1 0 01-.25-1 1 1 0 01.81-.68l5.7-.83 2.51-5.13a1 1 0 011.8 0l2.54 5.12 5.7.83a1 1 0 01.81.68 1 1 0 01-.25 1l-4.12 4 1 5.63a1 1 0 01-.4 1 1 0 01-.62.18z" data-name="star"/></g></svg>
                                        <span class="ml-1">{{ $recommendedMovie['vote_average'] * 10 }}%</span>
                                        <span class="mx-2">|</span>
                                        <span>{{ \Carbon\Carbon::parse($recommendedMovie['release_date'])->format('M d, Y') }}</span>
                                    </div>
                                    <div class="text-gray-400 text-sm">
                                        @foreach ($recommendedMovie['genre_ids'] as $genreId)
                                            {{ $genres->get($genreId) }}@if (!$loop->last), @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Nút điều hướng phải --}}
                <div class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10">
                    <button class="recommended-slider-next bg-gray-800 rounded-full p-2 focus:outline-none hover:bg-gray-700 transition ease-in-out duration-150">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite('resources/js/sliders.js')

    {{-- Script để xử lý đóng modal khi nhấn phím Escape --}}
    <script>
        function addToCart(movieId) {
            // Hiển thị loading
            const button = document.getElementById('add-to-cart-' + movieId);
            const originalText = button.innerHTML;
            button.innerHTML = `
                <svg class="animate-spin w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 21a9 9 0 100-18 9 9 0 000 18zm0-2a7 7 0 110-14 7 7 0 010 14z" opacity="0.25"/>
                    <path d="M12 3a9 9 0 019 9h-2a7 7 0 00-7-7V3z"/>
                </svg>
                <span class="ml-2">Đang thêm...</span>
            `;
            button.disabled = true;

            // Gửi request
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ tmdbId: movieId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật số lượng giỏ hàng
                    Livewire.dispatch('cartUpdated');

                    // Hiển thị thông báo
                    Livewire.dispatch('notify', {
                        message: 'Đã thêm phim vào giỏ hàng!',
                        type: 'success'
                    });
                } else {
                    Livewire.dispatch('notify', {
                        message: data.message || 'Lỗi khi thêm vào giỏ hàng',
                        type: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Livewire.dispatch('notify', {
                    message: 'Lỗi khi thêm vào giỏ hàng',
                    type: 'error'
                });
            })
            .finally(() => {
                // Khôi phục nút
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const trailerModal = document.querySelector('[x-data]')?.__x;
                if (trailerModal && trailerModal.$data.isOpen) {
                    trailerModal.$data.isOpen = false;
                }
            }
        });
    </script>
@endsection
<!--
Chi tiết cách xây dựng trang phim:
1. Hiển thị thông tin cơ bản: tiêu đề, đánh giá, thể loại, tóm tắt
2. Modal xem trailer sử dụng Alpine.js để quản lý trạng thái và hiệu ứng
3. Tối ưu iframe YouTube - chỉ load khi modal được mở, giải phóng tài nguyên khi đóng
4. Xử lý UX: bắt sự kiện escape, click outside để đóng modal
-->

