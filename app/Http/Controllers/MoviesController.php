<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; // Add Auth facade
use App\Models\Movie; // Add Movie model
use App\Services\MovieService; // Add MovieService

class MoviesController extends Controller
{
    protected $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    /**
     * Quy trình gọi API và xử lý dữ liệu:
     * 1. Cấu hình API key trong config/services.php
     * 2. Sử dụng Http facade của Laravel để gọi API
     * 3. Xử lý dữ liệu: map genre IDs sang tên, giới hạn số lượng phim
     * 4. Truyền dữ liệu đã xử lý đến view
     * 5. Sử dụng append_to_response để giảm số lượng request API
     *
     * Endpoints API đã dùng:
     * - movie/popular: Lấy phim phổ biến
     * - movie/now_playing: Lấy phim đang chiếu
     * - movie/{id}: Chi tiết phim
     * - movie/{id}/similar: Phim tương tự
     * - movie/{id}/recommendations: Phim đề xuất
     * - genre/movie/list: Danh sách thể loại phim
     */

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Lấy danh sách phim phổ biến từ API TMDb
            $popularResponse = Http::withToken(config('services.tmdb.token'))
                ->get('https://api.themoviedb.org/3/movie/popular');

            $popularMovies = [];
            if ($popularResponse->successful() && isset($popularResponse->json()['results'])) {
                $popularMovies = $popularResponse->json()['results'];
            } else {
                Log::error('Failed to fetch popular movies: ' . $popularResponse->body());
            }

            // Lấy danh sách phim đang chiếu từ API TMDb
            $nowPlayingResponse = Http::withToken(config('services.tmdb.token'))
                ->get('https://api.themoviedb.org/3/movie/now_playing');

            $nowPlayingMovies = [];
            if ($nowPlayingResponse->successful() && isset($nowPlayingResponse->json()['results'])) {
                $nowPlayingMovies = $nowPlayingResponse->json()['results'];
            } else {
                Log::error('Failed to fetch now playing movies: ' . $nowPlayingResponse->body());
            }

            // Lấy danh sách thể loại phim từ API TMDb
            $genresResponse = Http::withToken(config('services.tmdb.token'))
                ->get('https://api.themoviedb.org/3/genre/movie/list');

            $genresArray = [];
            if ($genresResponse->successful() && isset($genresResponse->json()['genres'])) {
                $genresArray = $genresResponse->json()['genres'];
            } else {
                Log::error('Failed to fetch genres: ' . $genresResponse->body());
            }

            // Tạo map tra cứu thể loại từ ID sang tên
            $genres = collect($genresArray)->mapWithKeys(function ($genre) {
                return [$genre['id'] => $genre['name']];
            });

            // Trả về view index với dữ liệu phim và thể loại
            return view('index', [
                'popularMovies' => $popularMovies,
                'nowPlayingMovies' => $nowPlayingMovies,
                'genres' => $genres,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in index method: ' . $e->getMessage());
            return view('index', [
                'popularMovies' => [],
                'nowPlayingMovies' => [],
                'genres' => collect([]),
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Lấy thông tin chi tiết phim từ API TMDb
        $movie = Http::withToken(config('services.tmdb.token'))
            ->get('https://api.themoviedb.org/3/movie/'.$id.'?append_to_response=credits,videos,images')
            ->json();

        // Lấy danh sách phim tương tự từ API TMDb
        $similarMovies = Http::withToken(config('services.tmdb.token'))
            ->get('https://api.themoviedb.org/3/movie/'.$id.'/similar')
            ->json()['results'];

        // Lấy danh sách phim được đề xuất (recommended)
        $recommendedMovies = Http::withToken(config('services.tmdb.token'))
            ->get('https://api.themoviedb.org/3/movie/'.$id.'/recommendations')
            ->json()['results'];

        // Giới hạn kết quả tối đa 20 phim
        $similarMovies = array_slice($similarMovies, 0, 20);
        $recommendedMovies = array_slice($recommendedMovies, 0, 20);

        // Lấy danh sách thể loại phim từ API TMDb
        $genresArray = Http::withToken(config('services.tmdb.token'))
            ->get('https://api.themoviedb.org/3/genre/movie/list')
            ->json()['genres'];

        // Tạo map tra cứu thể loại từ ID sang tên
        $genres = collect($genresArray)->mapWithKeys(function ($genre) {
            return [$genre['id'] => $genre['name']];
        });

        // Trả về view chi tiết phim với dữ liệu
        return view('show', [
            'movie' => $movie,
            'similarMovies' => $similarMovies,
            'recommendedMovies' => $recommendedMovies,
            'genres' => $genres
        ]);
    }

    /**
     * Tìm kiếm phim
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $movieService = new MovieService();
        $results = [];
        $alternativeResults = []; // Định nghĩa biến alternativeResults để tránh lỗi

        if ($query && strlen($query) >= 2) {
            // Thiết lập bộ lọc nếu có
            $filters = [];

            // Lọc theo năm nếu có tham số year
            if ($request->has('year') && !empty($request->year)) {
                $filters['year'] = $request->year;
            }

            // Lọc theo thể loại nếu có tham số genre
            if ($request->has('genre') && !empty($request->genre)) {
                $filters['genre'] = $request->genre;
            }

            // Sắp xếp theo tham số nếu có
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                $filters['sort_by'] = $request->sort_by;
            }

            // Lấy kết quả tìm kiếm với bộ lọc
            $searchResults = $movieService->searchMovies($query, 1, $filters);
            $results = $searchResults['results'] ?? [];

            // Nếu không có kết quả tìm kiếm, lấy phim phổ biến làm gợi ý thay thế
            if (empty($results)) {
                try {
                    // Lấy phim phổ biến làm gợi ý thay thế
                    $alternativeResults = $movieService->discoverMovies(['sort_by' => 'popularity.desc'], 1)['results'] ?? [];

                    // Giới hạn số lượng kết quả thay thế
                    $alternativeResults = array_slice($alternativeResults, 0, 8);
                } catch (\Exception $e) {
                    \Log::error('Error getting alternative movies: ' . $e->getMessage());
                    $alternativeResults = [];
                }
            }
        }

        // Lấy danh sách năm và thể loại cho bộ lọc nâng cao (tương tự admin)
        $years = range(date('Y'), 2000);
        $genres = $movieService->getGenres();

        return view('search', compact('results', 'query', 'alternativeResults', 'years', 'genres'));
    }

    /**
     * Hiển thị danh sách phim yêu thích của người dùng
     */
    public function favorites()
    {
        $favoriteMovies = Auth::user()->favoriteMovies()->paginate(15);
        return view('movies.favorites', compact('favoriteMovies'));
    }

    /**
     * Xóa phim khỏi danh sách yêu thích
     */
    public function removeFavorite(Movie $movie)
    {
        Auth::user()->favoriteMovies()->detach($movie->id);
        return back()->with('success', 'Đã xóa phim khỏi danh sách yêu thích');
    }

    /**
     * Lọc phim theo năm, thể loại, hoặc quốc gia
     */
    public function filter(Request $request)
    {
        try {
            // Lấy tham số lọc từ request
            $year = $request->query('year');
            $genreId = $request->query('genre');
            $genreName = $request->query('genre_name');
            $countryCode = $request->query('country');
            $countryName = $request->query('country_name');

            // Lấy trang hiện tại, mặc định là trang 1
            $page = $request->query('page', 1);

            // Xây dựng tham số lọc
            $params = [
                'page' => $page,
                'include_adult' => false,
                'language' => 'vi-VN'
            ];

            // Danh sách thể loại
            $genresArray = Http::withToken(config('services.tmdb.token'))
                ->get('https://api.themoviedb.org/3/genre/movie/list')
                ->json()['genres'];

            $genres = collect($genresArray)->mapWithKeys(function ($genre) {
                return [$genre['id'] => $genre['name']];
            });

            // Loại filter và tiêu đề
            $filterTitle = 'Tất cả phim';
            $endpoint = 'https://api.themoviedb.org/3/discover/movie';

            // Áp dụng bộ lọc
            if ($year) {
                $params['primary_release_year'] = $year;
                $filterTitle = "Phim năm $year";
            }

            if ($genreId) {
                $params['with_genres'] = $genreId;
                $filterTitle = "Thể loại: $genreName";
            }

            if ($countryCode) {
                $params['with_origin_country'] = $countryCode;
                $filterTitle = "Quốc gia: $countryName";
            }

            // Gọi API với bộ lọc
            $results = Http::withToken(config('services.tmdb.token'))
                ->get($endpoint, $params)
                ->json();

            // Log cho việc debug
            Log::info('Filter applied', [
                'year' => $year,
                'genre' => $genreId,
                'country' => $countryCode,
                'results_count' => count($results['results'] ?? []),
                'total_pages' => $results['total_pages'] ?? 0
            ]);

            // Trả về view với dữ liệu đã lọc
            return view('filter', [
                'filterTitle' => $filterTitle,
                'movies' => $results['results'] ?? [],
                'totalPages' => $results['total_pages'] ?? 0,
                'currentPage' => $page,
                'totalResults' => $results['total_results'] ?? 0,
                'genres' => $genres,
                'year' => $year,
                'genreId' => $genreId,
                'genreName' => $genreName,
                'countryCode' => $countryCode,
                'countryName' => $countryName
            ]);
        } catch (\Exception $e) {
            Log::error('Error in filter method: ' . $e->getMessage());
            return redirect()->route('movie.index')
                ->with('error', 'Có lỗi xảy ra khi lọc phim. Vui lòng thử lại sau.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
