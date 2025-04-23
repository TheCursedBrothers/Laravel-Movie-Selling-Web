<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'tmdb_id', 'title', 'overview', 'poster_path',
        'backdrop_path', 'release_date', 'vote_average',
        'vote_count', 'price', 'stock', 'genres', 'active'
    ];

    protected $casts = [
        'release_date' => 'date',
        'genres' => 'array',
        'active' => 'boolean',
    ];

    // Lấy phim từ API nếu không có trong database
    public static function findOrCreateFromApi($tmdbId)
    {
        $movie = self::where('tmdb_id', $tmdbId)->first();

        if (!$movie) {
            $apiMovie = app('App\Services\MovieService')->getMovieDetails($tmdbId);

            if ($apiMovie) {
                $movie = self::create([
                    'tmdb_id' => $tmdbId,
                    'title' => $apiMovie['title'],
                    'overview' => $apiMovie['overview'] ?? null,
                    'poster_path' => $apiMovie['poster_path'] ?? null,
                    'backdrop_path' => $apiMovie['backdrop_path'] ?? null,
                    'release_date' => $apiMovie['release_date'] ?? null,
                    'vote_average' => $apiMovie['vote_average'] ?? null,
                    'vote_count' => $apiMovie['vote_count'] ?? null,
                    'price' => 120000, // Giá mặc định 120.000đ
                    'stock' => 100,                      'genres' => isset($apiMovie['genres']) ? collect($apiMovie['genres'])->pluck('name')->toArray() : null,                  ]);
            }
        }

        return $movie;
    }

    // Cập nhật thông tin phim
    public static function updateMovieDetails($movie, $data)
    {
        try {
            $movie->update([
                'title' => $data['title'] ?? $movie->title,
                'overview' => $data['overview'] ?? $movie->overview,
                'price' => $data['price'] ?? $movie->price,
                'stock' => $data['stock'] ?? $movie->stock,
                'active' => isset($data['active']) ? (bool)$data['active'] : $movie->active,
            ]);

            return $movie;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error updating movie: ' . $e->getMessage());
            throw $e;
        }
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
