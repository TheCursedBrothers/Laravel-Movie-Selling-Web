<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Services\MovieService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminMoviesController extends Controller
{
    protected $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    /**
     * Hiển thị danh sách phim
     */
    public function index(Request $request)
    {
        $query = Movie::query();

        // Tìm kiếm theo tiêu đề
        if ($request->has('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        // Lọc theo trạng thái active
        if ($request->has('active') && $request->active !== '') {
            $query->where('active', $request->active);
        }

        // Sắp xếp theo cột và hướng
        $sortColumn = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortColumn, $sortDirection);

        $movies = $query->paginate(10);

        return view('admin.movies.index', compact('movies'));
    }

    /**
     * Hiển thị form tạo phim mới
     */
    public function create()
    {
        return view('admin.movies.create');
    }

    /**
     * Lưu phim mới vào database
     */
    public function store(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|integer|unique:movies,tmdb_id',
        ]);

        try {
            // Lấy thông tin phim từ API TMDB
            $movieData = $this->movieService->getMovieDetails($request->tmdb_id);

            if (!$movieData) {
                return back()
                    ->with('error', 'Không tìm thấy phim với ID này từ TMDB.')
                    ->withInput();
            }

            // Convert 'active' from checkbox 'on' to boolean true/false
            $isActive = $request->has('active') ? true : false;

            // Tạo phim mới với thông tin từ API
            $movie = Movie::create([
                'tmdb_id' => $request->tmdb_id,
                'title' => $movieData['title'],
                'overview' => $movieData['overview'] ?? null,
                'poster_path' => $movieData['poster_path'] ?? null,
                'backdrop_path' => $movieData['backdrop_path'] ?? null,
                'release_date' => $movieData['release_date'] ?? null,
                'vote_average' => $movieData['vote_average'] ?? null,
                'vote_count' => $movieData['vote_count'] ?? null,
                'price' => $request->input('price', 120000), // Mặc định 120.000đ
                'stock' => $request->input('stock', 100), // Mặc định 100
                'genres' => isset($movieData['genres']) ? json_encode(collect($movieData['genres'])->pluck('name')) : null,
                'active' => $isActive,
            ]);

            return redirect()->route('admin.movies.index')
                ->with('success', 'Phim đã được thêm thành công.');

        } catch (\Exception $e) {
            Log::error('Lỗi khi thêm phim: ' . $e->getMessage());
            return back()
                ->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hiển thị chi tiết phim
     */
    public function show(Movie $movie)
    {
        return view('admin.movies.show', compact('movie'));
    }

    /**
     * Hiển thị form chỉnh sửa phim
     */
    public function edit(Movie $movie)
    {
        return view('admin.movies.edit', compact('movie'));
    }

    /**
     * Cập nhật thông tin phim
     */
    public function update(Request $request, Movie $movie)
    {
        // Log received request for debugging
        Log::info('Movie update request received', [
            'movie_id' => $movie->id,
            'request_data' => $request->all()
        ]);

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'price' => 'required|integer|min:0',
                'stock' => 'required|integer|min:0',
                'overview' => 'nullable|string',
            ]);

            // Prepare data for update
            $data = $validated;
            $data['active'] = $request->has('active');

            // Use the model method to update the movie
            $movie->update([
                'title' => $data['title'],
                'overview' => $data['overview'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'active' => $data['active'],
            ]);

            return redirect()->route('admin.movies.index')
                ->with('success', 'Phim đã được cập nhật thành công.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật phim: ' . $e->getMessage());
            return back()
                ->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Xóa phim
     */
    public function destroy(Movie $movie)
    {
        $movie->delete();
        return redirect()->route('admin.movies.index')
            ->with('success', 'Phim đã được xóa thành công.');
    }

    /**
     * Tìm kiếm phim từ TMDB API
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        $page = $request->get('page', 1);
        $filters = [
            'year' => $request->get('year'),
            'genre' => $request->get('genre'),
            'sort_by' => $request->get('sort_by', 'popularity.desc')
        ];

        // Kiểm tra xác request mong muốn HTML hay JSON
        $wantsJson = $request->ajax() || $request->wantsJson();

        if (empty($query) && !$wantsJson) {
            // Lấy thể loại phim từ TMDB API cho filter
            $genres = $this->movieService->getGenres();

            // Năm phát hành từ 1900 đến hiện tại
            $years = range(date('Y'), 1900);

            return view('admin.movies.search', compact('genres', 'years'));
        }

        $results = [];
        $totalPages = 0;
        $totalResults = 0;

        if (!empty($query) || (!empty($filters['year']) || !empty($filters['genre']))) {
            if (!empty($query)) {
                // Tìm kiếm phim theo từ khóa
                $searchResult = $this->movieService->searchMovies($query, $page, $filters);
            } else {
                // Discover phim theo bộ lọc
                $searchResult = $this->movieService->discoverMovies($filters, $page);
            }

            $results = $searchResult['results'] ?? [];
            $totalPages = $searchResult['total_pages'] ?? 0;
            $totalResults = $searchResult['total_results'] ?? 0;

            // Lọc phim đã có trong database
            $existingMovieIds = Movie::whereIn('tmdb_id', collect($results)->pluck('id'))->pluck('tmdb_id')->toArray();

            // Đánh dấu phim đã có trong database
            $results = collect($results)->map(function($movie) use ($existingMovieIds) {
                $movie['exists'] = in_array($movie['id'], $existingMovieIds);
                return $movie;
            })->toArray();
        }

        if ($wantsJson) {
            // Trả về JSON cho AJAX requests
            return response()->json([
                'results' => $results,
                'total_pages' => $totalPages,
                'total_results' => $totalResults
            ]);
        }

        // Trả về HTML view
        return view('admin.movies.search', [
            'results' => $results,
            'query' => $query,
            'page' => $page,
            'total_pages' => $totalPages,
            'total_results' => $totalResults,
            'filters' => $filters,
            'genres' => $this->movieService->getGenres(),
            'years' => range(date('Y'), 1900)
        ]);
    }

    /**
     * API để thêm phim từ TMDB
     */
    public function apiStore(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|integer|unique:movies,tmdb_id',
        ]);

        try {
            // Lấy thông tin phim từ API TMDB
            $movieData = $this->movieService->getMovieDetails($request->tmdb_id);

            if (!$movieData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy phim với ID này từ TMDB.'
                ], 404);
            }

            // Convert 'active' from checkbox 'on' to boolean true/false
            $isActive = $request->has('active') ? true : false;

            // Tạo phim mới với thông tin từ API
            $movie = Movie::create([
                'tmdb_id' => $request->tmdb_id,
                'title' => $movieData['title'],
                'overview' => $movieData['overview'] ?? null,
                'poster_path' => $movieData['poster_path'] ?? null,
                'backdrop_path' => $movieData['backdrop_path'] ?? null,
                'release_date' => $movieData['release_date'] ?? null,
                'vote_average' => $movieData['vote_average'] ?? null,
                'vote_count' => $movieData['vote_count'] ?? null,
                'price' => $request->input('price', 120000), // Mặc định 120.000đ
                'stock' => $request->input('stock', 100), // Mặc định 100
                'genres' => isset($movieData['genres']) ? json_encode(collect($movieData['genres'])->pluck('name')) : null,
                'active' => $isActive,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Phim đã được thêm thành công.',
                'movie' => $movie
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi khi thêm phim qua API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}
