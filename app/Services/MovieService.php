<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MovieService
{
    protected $apiToken;
    protected $baseUrl;
    protected $imageBaseUrl;

    public function __construct()
    {
        $this->apiToken = config('services.tmdb.token');
        $this->baseUrl = 'https://api.themoviedb.org/3';
        $this->imageBaseUrl = 'https://image.tmdb.org/t/p/';
    }

    public function getMovieDetails($movieId)
    {
        try {
            $response = Http::withToken($this->apiToken)
                ->get("{$this->baseUrl}/movie/{$movieId}", [
                    'append_to_response' => 'credits,videos,images',
                    'language' => 'vi',
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching movie details: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tìm kiếm phim theo tên với bộ lọc
     */
    public function searchMovies($query, $page = 1, $filters = [])
    {
        try {
            $url = "https://api.themoviedb.org/3/search/movie";
            $params = [
                'query' => $query,
                'page' => $page,
                'include_adult' => false,
                'language' => 'vi-VN'
            ];

            // Thêm các tham số lọc
            if (!empty($filters['year'])) {
                $params['year'] = $filters['year'];
            }

            if (!empty($filters['genre'])) {
                $params['with_genres'] = $filters['genre'];
            }

            $response = $this->sendRequest($url, $params);

            return $response ?? ['results' => [], 'total_pages' => 0, 'total_results' => 0];
        } catch (\Exception $e) {
            Log::error('Error searching movies: ' . $e->getMessage());
            return ['results' => [], 'total_pages' => 0, 'total_results' => 0];
        }
    }

    /**
     * Discover phim với các bộ lọc
     */
    public function discoverMovies($filters = [], $page = 1)
    {
        try {
            $url = "https://api.themoviedb.org/3/discover/movie";
            $params = [
                'page' => $page,
                'include_adult' => false,
                'language' => 'vi-VN',
                'sort_by' => $filters['sort_by'] ?? 'popularity.desc'
            ];

            // Thêm các tham số lọc
            if (!empty($filters['year'])) {
                $params['primary_release_year'] = $filters['year'];
            }

            if (!empty($filters['genre'])) {
                $params['with_genres'] = $filters['genre'];
            }

            $response = $this->sendRequest($url, $params);

            return $response ?? ['results' => [], 'total_pages' => 0, 'total_results' => 0];
        } catch (\Exception $e) {
            Log::error('Error discovering movies: ' . $e->getMessage());
            return ['results' => [], 'total_pages' => 0, 'total_results' => 0];
        }
    }

    /**
     * Lấy danh sách thể loại phim
     */
    public function getGenres()
    {
        try {
            // Cache thể loại phim trong 24 giờ
            return Cache::remember('movie_genres', 86400, function () {
                $url = "https://api.themoviedb.org/3/genre/movie/list";
                $params = [
                    'language' => 'vi-VN'
                ];

                $response = $this->sendRequest($url, $params);
                return $response['genres'] ?? [];
            });
        } catch (\Exception $e) {
            Log::error('Error getting movie genres: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Send request to TMDB API
     */
    protected function sendRequest($url, $params = [])
    {
        try {
            // Use API key directly from config for consistency
            $response = Http::withToken($this->apiToken)->get($url, $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TMDB API error: ' . $response->status() . ' - ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Error sending request to TMDB API: ' . $e->getMessage());
            return null;
        }
    }
}
