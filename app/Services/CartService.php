<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Movie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartService
{
    protected $cart;
    protected $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->initCart();
        $this->movieService = $movieService;
    }

    protected function initCart()
    {
        $sessionId = Session::getId();

        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (Auth::check()) {
            // Tìm giỏ hàng của người dùng
            $userCart = Cart::where('user_id', Auth::id())->latest()->first();

            if ($userCart) {
                $this->cart = $userCart;
                return;
            }

            // Tìm giỏ hàng với session ID hiện tại
            $sessionCart = Cart::where('session_id', $sessionId)->first();

            if ($sessionCart) {
                // Liên kết giỏ hàng hiện tại với người dùng
                $sessionCart->user_id = Auth::id();
                $sessionCart->save();
                $this->cart = $sessionCart;
                return;
            }

            // Nếu không có giỏ hàng, tạo mới một giỏ hàng cho người dùng
            $this->cart = Cart::create([
                'session_id' => $sessionId,
                'user_id' => Auth::id()
            ]);
        } else {
            // Người dùng chưa đăng nhập, tìm hoặc tạo giỏ hàng dựa trên session
            $this->cart = Cart::firstOrCreate([
                'session_id' => $sessionId
            ]);
        }
    }

    public function getCart()
    {
        return $this->cart->load('items.movie');
    }

    public function addMovie($tmdbId)
    {
        try {
            // Tìm phim trong database
            $movie = Movie::where('tmdb_id', $tmdbId)->first();

            // Nếu chưa có, lấy từ API và lưu vào database
            if (!$movie) {
                $movieData = $this->movieService->getMovieDetails($tmdbId);

                if (!$movieData) {
                    Log::error("Không thể lấy dữ liệu phim từ API với ID: $tmdbId");
                    return null;
                }

                // Tạo bản ghi phim mới với giá 120.000đ
                $movie = Movie::create([
                    'tmdb_id' => $tmdbId,
                    'title' => $movieData['title'],
                    'overview' => $movieData['overview'] ?? null,
                    'poster_path' => $movieData['poster_path'] ?? null,
                    'backdrop_path' => $movieData['backdrop_path'] ?? null,
                    'release_date' => $movieData['release_date'] ?? null,
                    'vote_average' => $movieData['vote_average'] ?? null,
                    'vote_count' => $movieData['vote_count'] ?? null,
                    'price' => 120000, // Giá mặc định 120.000đ
                    'stock' => 100, // Số lượng mặc định
                    'genres' => isset($movieData['genres']) ? json_encode(collect($movieData['genres'])->pluck('name')) : null,
                    'active' => true
                ]);

                Log::info("Đã tạo phim mới từ API với ID: $tmdbId - {$movie->title}");
            }

            // Kiểm tra phim đã có trong giỏ hàng chưa
            $existingItem = $this->cart->items()
                ->where('movie_id', $movie->id)
                ->first();

            if ($existingItem) {
                // Nếu đã có, tăng số lượng
                $existingItem->increment('quantity');
                Log::info("Đã tăng số lượng cho phim trong giỏ hàng: {$movie->title}");
                return $existingItem->fresh()->load('movie');
            }

            // Thêm phim mới vào giỏ hàng
            $cartItem = $this->cart->items()->create([
                'movie_id' => $movie->id,
                'price' => $movie->price,
                'quantity' => 1
            ]);

            Log::info("Đã thêm phim mới vào giỏ hàng: {$movie->title}");
            return $cartItem->load('movie');

        } catch (\Exception $e) {
            Log::error("Lỗi khi thêm phim vào giỏ hàng: " . $e->getMessage());
            return null;
        }
    }

    public function removeMovie($movieId)
    {
        return $this->cart->items()->where('movie_id', $movieId)->delete();
    }

    public function updateQuantity($itemId, $quantity)
    {
        $item = $this->cart->items()->findOrFail($itemId);
        $item->update(['quantity' => $quantity]);
        return $item;
    }

    public function clear()
    {
        return $this->cart->items()->delete();
    }

    public function count()
    {
        return $this->cart->items->sum('quantity');
    }

    public function total()
    {
        return $this->cart->items->sum(function($item) {
            return $item->price * $item->quantity;
        });
    }
}
