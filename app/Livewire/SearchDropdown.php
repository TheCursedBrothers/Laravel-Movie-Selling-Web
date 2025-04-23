<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SearchDropdown extends Component
{
    public $search = null;

    public function render()
    {
        // Lấy kết quả tìm kiếm phim từ API TMDb
        $searchResults = [];
        if (strlen($this->search) >= 2) {
            try {
                // Chuẩn hóa input: loại bỏ khoảng trắng thừa và chuyển thành chữ thường
                $formattedSearch = strtolower(trim($this->search));

                $response = Http::withToken(config('services.tmdb.token'))
                    ->get('https://api.themoviedb.org/3/search/movie', [
                        'query' => $formattedSearch,
                        'include_adult' => false,
                    ])
                    ->json();

                $searchResults = $response['results'] ?? [];

                // Giới hạn kết quả hiển thị trong dropdown
                $searchResults = array_slice($searchResults, 0, 7);
            } catch (\Exception $e) {
                Log::error('Error fetching search results: ' . $e->getMessage());
                $searchResults = [];
            }
        }

        return view('livewire.search-dropdown', [
            'searchResults' => $searchResults,
        ]);
    }

    // Method này không được gọi trực tiếp do form sẽ submit thông thường
    public function updateSearch($value)
    {
        $this->search = $value;
    }
}
