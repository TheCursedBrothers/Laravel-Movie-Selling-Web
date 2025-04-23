<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Giải thích về Laravel Component:
 * 1. Component là một cách để tái sử dụng mã giao diện và logic
 * 2. Class MovieCard kế thừa từ Illuminate\View\Component
 * 3. Constructor nhận dữ liệu đầu vào: phim và thể loại
 * 4. Phương thức render() trả về view cho component
 * 5. Sử dụng trong view với cú pháp <x-movie-card :movie="$movie" :genres="$genres" />
 * 6. Tất cả thuộc tính public của class tự động khả dụng trong view
 */
class MovieCard extends Component
{
    /**
     * Dữ liệu phim từ API.
     * Đây là một mảng chứa thông tin của phim như title, poster_path, vote_average...
     *
     * @var array
     */
    public $movie;

    /**
     * Danh sách thể loại phim.
     * Đây là một collection ánh xạ ID thể loại đến tên thể loại.
     *
     * @var \Illuminate\Support\Collection
     */
    public $genres;

    /**
     * Khởi tạo component mới.
     *
     * @param array $movie Mảng chứa thông tin chi tiết của phim
     * @param \Illuminate\Support\Collection $genres Collection ánh xạ ID thể loại đến tên thể loại
     *
     * Lưu ý: Khi <x-movie-card :movie="$movie" :genres="$genres" /> được gọi,
     * Laravel sẽ truyền các giá trị này vào constructor
     */
    public function __construct($movie, $genres)
    {
        $this->movie = $movie;
        $this->genres = $genres;
    }

    /**
     * Lấy view hiển thị component này.
     *
     * Phương thức này sẽ trả về view blade thể hiện HTML của component.
     * File view này nằm tại resources/views/components/movie-card.blade.php
     *
     * Các thuộc tính $movie và $genres có thể được sử dụng trực tiếp trong view.
     */
    public function render(): View|Closure|string
    {
        return view('components.movie-card');
    }
}
