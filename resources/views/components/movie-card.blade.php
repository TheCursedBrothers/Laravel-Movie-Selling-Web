{{--
    Component Movie Card - Template hiển thị thẻ phim

    Nhận vào các biến:
    - $movie: Mảng chứa thông tin phim từ API TMDb
    - $genres: Collection ánh xạ ID thể loại đến tên

    Các biến này được truyền từ MovieCard component class
    thông qua thuộc tính public và tự động khả dụng trong view này.
--}}
<div class="mt-8">
    <a href="{{ route('movies.show', $movie['id']) }}">
        {{-- Hiển thị poster phim từ API TMDb --}}
        <img src="https://image.tmdb.org/t/p/w500{{ $movie['poster_path'] }}" alt="{{ $movie['title'] }}" class="hover:opacity-75 transition ease-in-out duration-150">
    </a>
    <div class="mt-2">
        {{-- Tiêu đề phim --}}
        <a href="{{ route('movies.show', $movie['id']) }}" class="text-lg mt-2 hover:text-gray-300">{{ $movie['title'] }}</a>
        <div class="flex items-center text-gray-400 text-sm mt-1">
            {{-- Biểu tượng sao đánh giá --}}
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                <path d="M12 .587l3.668 7.568 8.332 1.151-6.064 5.828 1.48 8.279-7.416-3.967-7.417 3.967 1.481-8.279-6.064-5.828 8.332-1.151z" fill="yellow"/>
            </svg>
            {{-- Điểm đánh giá (đã chuyển sang phần trăm) --}}
            <span class="ml-1">{{ round($movie['vote_average'] * 10) }}%</span>
            <span class="mx-2">|</span>
            {{-- Ngày phát hành đã được định dạng --}}
            <span>{{ \Carbon\Carbon::parse($movie['release_date'])->format('M d, Y') }}</span>
        </div>
        <div class="text-gray-400 text-sm">
            @php
                // Chuyển đổi danh sách ID thể loại thành tên thể loại
                $movieGenres = collect($movie['genre_ids'])->map(function($id) use ($genres) {
                    return $genres->get($id);
                })->join(', ');
            @endphp
            {{-- Hiển thị thể loại phim --}}
            {{ $movieGenres }}
        </div>

        {{-- Hiển thị giá phim --}}
        <div class="mt-2 flex items-center">
            <span class="text-orange-500 font-medium">
                @php
                    // Tính giá dựa trên vote_average:
                    // Nếu vote_average cao hơn, giá sẽ cao hơn, trong khoảng 120.000đ - 200.000đ
                    $basePrice = 120000; // Giá cơ bản
                    $maxPrice = 200000;  // Giá tối đa
                    $voteAverage = $movie['vote_average'] ?? 5;
                    $price = min($maxPrice, max($basePrice, intval($basePrice + ($voteAverage/10) * ($maxPrice - $basePrice))));

                    // Lưu giá vào dữ liệu để có thể sử dụng cho việc sắp xếp
                    $movie['price'] = $price;
                @endphp
                {{ number_format($price, 0, ',', '.') }}đ
            </span>
        </div>
    </div>
</div>
