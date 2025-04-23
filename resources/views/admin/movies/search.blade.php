@extends('admin.layouts.app')

@section('title', 'Tìm kiếm phim từ TMDB')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.movies.index') }}">Quản lý phim</a> / Tìm kiếm phim từ TMDB
@endsection

@section('content')
<div class="space-y-6" x-data="{
    selectedMovies: [],
    importingMovies: false,
    showAdvancedFilters: false,
    activeView: 'grid',
    importProgress: 0,
    searchLoading: false
}">
    <!-- Banner thông tin với gradient đẹp -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-5 sm:px-8 sm:py-7 flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-4 md:mb-0">
                <h2 class="text-xl sm:text-2xl font-bold text-white mb-1">TMDB Movie Search</h2>
                <p class="text-blue-100 text-sm sm:text-base max-w-2xl">
                    Tìm kiếm và nhập phim từ The Movie Database API. Chọn phim mà bạn muốn thêm vào hệ thống.
                </p>
            </div>
            <div class="flex space-x-3">
                <button
                    type="button"
                    class="bg-white hover:bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center shadow-sm hover:shadow-md"
                    @click="showAdvancedFilters = !showAdvancedFilters"
                >
                    <i class="fas fa-sliders-h mr-2"></i>
                    <span>Bộ lọc</span>
                    <i class="fas fa-chevron-down ml-2" :class="{'transform rotate-180': showAdvancedFilters}"></i>
                </button>

                <button
                    type="button"
                    class="bg-white hover:bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center shadow-sm hover:shadow-md"
                    @click="activeView = activeView === 'grid' ? 'list' : 'grid'"
                >
                    <i class="fas" :class="activeView === 'grid' ? 'fa-list' : 'fa-th-large'"></i>
                    <span class="ml-2" x-text="activeView === 'grid' ? 'Dạng danh sách' : 'Dạng lưới'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Form tìm kiếm với thiết kế mới -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ route('admin.movies.search') }}" method="GET" class="space-y-6" @submit="searchLoading = true">
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input
                            type="text"
                            name="query"
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Nhập tên phim cần tìm..."
                            value="{{ request('query') }}"
                            required
                        >
                    </div>
                    <div>
                        <button
                            type="submit"
                            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center"
                            :class="{'opacity-75 cursor-wait': searchLoading}"
                            :disabled="searchLoading"
                        >
                            <span x-show="!searchLoading">
                                <i class="fas fa-search mr-2"></i> Tìm kiếm
                            </span>
                            <span x-show="searchLoading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Đang tìm...
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Advanced filters -->
                <div x-show="showAdvancedFilters" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Năm phát hành</label>
                            <select name="year" id="year" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tất cả các năm</option>
                                @php $currentYear = date('Y'); @endphp
                                @for($year = $currentYear; $year >= 1950; $year--)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>

                        <div>
                            <label for="genre" class="block text-sm font-medium text-gray-700 mb-1">Thể loại</label>
                            <select name="genre" id="genre" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tất cả thể loại</option>
                                @php
                                    $genres = [
                                        28 => 'Hành động',
                                        12 => 'Phiêu lưu',
                                        16 => 'Hoạt hình',
                                        35 => 'Hài',
                                        80 => 'Hình sự',
                                        99 => 'Tài liệu',
                                        18 => 'Chính kịch',
                                        10751 => 'Gia đình',
                                        14 => 'Giả tưởng',
                                        36 => 'Lịch sử',
                                        27 => 'Kinh dị',
                                        10402 => 'Âm nhạc',
                                        9648 => 'Bí ẩn',
                                        10749 => 'Lãng mạn',
                                        878 => 'Khoa học viễn tưởng',
                                        10770 => 'TV Movie',
                                        53 => 'Gây cấn',
                                        10752 => 'Chiến tranh',
                                        37 => 'Miền Tây'
                                    ];
                                @endphp
                                @foreach($genres as $id => $name)
                                    <option value="{{ $id }}" {{ request('genre') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp theo</label>
                            <select name="sort_by" id="sort_by" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="popularity.desc" {{ request('sort_by') == 'popularity.desc' ? 'selected' : '' }}>Phổ biến (Cao đến thấp)</option>
                                <option value="popularity.asc" {{ request('sort_by') == 'popularity.asc' ? 'selected' : '' }}>Phổ biến (Thấp đến cao)</option>
                                <option value="vote_average.desc" {{ request('sort_by') == 'vote_average.desc' ? 'selected' : '' }}>Đánh giá (Cao đến thấp)</option>
                                <option value="vote_average.asc" {{ request('sort_by') == 'vote_average.asc' ? 'selected' : '' }}>Đánh giá (Thấp đến cao)</option>
                                <option value="release_date.desc" {{ request('sort_by') == 'release_date.desc' ? 'selected' : '' }}>Ngày phát hành (Mới đến cũ)</option>
                                <option value="release_date.asc" {{ request('sort_by') == 'release_date.asc' ? 'selected' : '' }}>Ngày phát hành (Cũ đến mới)</option>
                                <option value="original_title.asc" {{ request('sort_by') == 'original_title.asc' ? 'selected' : '' }}>Tên (A-Z)</option>
                                <option value="original_title.desc" {{ request('sort_by') == 'original_title.desc' ? 'selected' : '' }}>Tên (Z-A)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @if(request()->anyFilled(['year', 'genre', 'sort_by']))
                            <a href="{{ route('admin.movies.search', ['query' => request('query')]) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium text-gray-700 transition-colors">
                                <i class="fas fa-times mr-1.5"></i>
                                Xóa bộ lọc
                            </a>
                        @endif

                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-md text-sm font-medium transition-colors">
                            <i class="fas fa-filter mr-1.5"></i>
                            Áp dụng bộ lọc
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Search Results -->
    @if(isset($results) && count($results) > 0)
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="border-b border-gray-200 p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h3 class="text-lg font-medium text-gray-900">Kết quả tìm kiếm</h3>
                    <p class="text-sm text-gray-500">Tìm thấy {{ number_format($total_results) }} kết quả {{ !empty($query) ? 'cho "' . $query . '"' : '' }}</p>
                </div>

                <div class="flex items-center">
                    <div class="text-sm text-gray-500 mr-4">
                        Trang {{ $page }} / {{ $total_pages }}
                    </div>

                    <div class="flex space-x-2">
                        @if($page > 1)
                            <a href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        @else
                            <button disabled class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        @endif

                        @if($page < $total_pages)
                            <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @else
                            <button disabled class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Floating selection actions -->
            <div
                x-show="selectedMovies.length > 0"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-10"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-10"
                class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 bg-blue-600 rounded-lg shadow-lg"
            >
                <div class="px-6 py-3 flex items-center space-x-4">
                    <span class="text-white text-sm">
                        <span x-text="selectedMovies.length"></span> phim đã được chọn
                    </span>
                    <button
                        @click="importSelectedMovies()"
                        :disabled="importingMovies"
                        class="bg-white text-blue-600 hover:bg-blue-50 font-medium text-sm px-4 py-2 rounded-md transition-colors"
                    >
                        <i class="fas fa-file-import mr-2"></i>
                        <span x-text="importingMovies ? 'Đang nhập...' : 'Nhập vào hệ thống'"></span>
                    </button>
                    <button
                        @click="selectedMovies = []"
                        class="text-blue-200 hover:text-white"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Grid view -->
            <div x-show="activeView === 'grid'" class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach($results as $movie)
                        <div
                            class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200"
                            :class="{ 'ring-2 ring-blue-500 shadow-md': selectedMovies.includes({{ $movie['id'] }}) }"
                        >
                            <div class="relative">
                                @if(isset($movie['poster_path']) && $movie['poster_path'])
                                    <img
                                        src="https://image.tmdb.org/t/p/w500{{ $movie['poster_path'] }}"
                                        alt="{{ $movie['title'] }}"
                                        class="w-full h-64 object-cover"
                                    >
                                @else
                                    <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-film text-4xl text-gray-400"></i>
                                    </div>
                                @endif

                                <!-- Selection checkbox -->
                                <div class="absolute top-3 right-3">
                                    <button
                                        type="button"
                                        @click="toggleMovieSelection({{ $movie['id'] }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-full"
                                        :class="selectedMovies.includes({{ $movie['id'] }}) ? 'bg-blue-500 text-white' : 'bg-black/50 hover:bg-black/70 text-white'"
                                    >
                                        <i
                                            class="fas"
                                            :class="selectedMovies.includes({{ $movie['id'] }}) ? 'fa-check' : 'fa-plus'"
                                        ></i>
                                    </button>
                                </div>

                                <!-- Rating badge -->
                                @if(isset($movie['vote_average']))
                                    <div class="absolute top-3 left-3 bg-black/50 text-white text-xs px-2 py-1 rounded-md flex items-center">
                                        <i class="fas fa-star text-yellow-400 mr-1"></i>
                                        {{ number_format($movie['vote_average'], 1) }}
                                    </div>
                                @endif

                                <!-- Year badge -->
                                @if(isset($movie['release_date']) && !empty($movie['release_date']))
                                    <div class="absolute bottom-3 left-3 bg-black/50 text-white text-xs px-2 py-1 rounded-md">
                                        {{ \Carbon\Carbon::parse($movie['release_date'])->format('Y') }}
                                    </div>
                                @endif
                            </div>

                            <div class="p-4">
                                <h4 class="font-semibold text-gray-900 mb-1 truncate">{{ $movie['title'] }}</h4>
                                <p class="text-sm text-gray-500 mb-3 line-clamp-2 h-10">
                                    {{ $movie['overview'] ?? 'Không có mô tả.' }}
                                </p>

                                <!-- Genre tags -->
                                @if(isset($movie['genre_ids']) && is_array($movie['genre_ids']))
                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                        @php
                                            $genres = [
                                                28 => 'Hành động',
                                                12 => 'Phiêu lưu',
                                                16 => 'Hoạt hình',
                                                35 => 'Hài',
                                                80 => 'Hình sự',
                                                99 => 'Tài liệu',
                                                18 => 'Chính kịch',
                                                10751 => 'Gia đình',
                                                14 => 'Giả tưởng',
                                                36 => 'Lịch sử',
                                                27 => 'Kinh dị',
                                                10402 => 'Âm nhạc',
                                                9648 => 'Bí ẩn',
                                                10749 => 'Lãng mạn',
                                                878 => 'Khoa học viễn tưởng',
                                                10770 => 'TV Movie',
                                                53 => 'Gây cấn',
                                                10752 => 'Chiến tranh',
                                                37 => 'Miền Tây'
                                            ];
                                        @endphp
                                        @foreach($movie['genre_ids'] as $genreId)
                                            @if(isset($genres[$genreId]) && $loop->index < 3)
                                                <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 rounded-md text-xs">
                                                    {{ $genres[$genreId] }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                <div class="flex justify-between items-center mt-4">
                                    <a
                                        href="https://www.themoviedb.org/movie/{{ $movie['id'] }}"
                                        target="_blank"
                                        class="text-blue-600 hover:text-blue-800 text-sm flex items-center"
                                    >
                                        <i class="fas fa-external-link-alt mr-1"></i> Chi tiết
                                    </a>

                                    <button
                                        type="button"
                                        @click="toggleMovieSelection({{ $movie['id'] }})"
                                        class="text-sm px-3 py-1.5 rounded-md"
                                        :class="selectedMovies.includes({{ $movie['id'] }}) ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-blue-100 text-blue-700 hover:bg-blue-200'"
                                    >
                                        <i
                                            class="fas"
                                            :class="selectedMovies.includes({{ $movie['id'] }}) ? 'fa-times' : 'fa-plus'"
                                        ></i>
                                        <span x-text="selectedMovies.includes({{ $movie['id'] }}) ? 'Bỏ chọn' : 'Chọn'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- List view -->
            <div x-show="activeView === 'list'" class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="w-8 px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input
                                        type="checkbox"
                                        @click="toggleAllMovies($event)"
                                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    >
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Phim
                                </th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Năm
                                </th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Đánh giá
                                </th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thể loại
                                </th>
                                <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($results as $movie)
                                <tr class="hover:bg-gray-50 transition-colors" :class="{ 'bg-blue-50': selectedMovies.includes({{ $movie['id'] }}) }">
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <input
                                            type="checkbox"
                                            value="{{ $movie['id'] }}"
                                            x-model="selectedMovies"
                                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        >
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if(isset($movie['poster_path']) && $movie['poster_path'])
                                                <img src="https://image.tmdb.org/t/p/w92{{ $movie['poster_path'] }}" alt="{{ $movie['title'] }}" class="h-16 w-auto rounded-sm mr-3">
                                            @else
                                                <div class="h-16 w-10 bg-gray-200 rounded-sm mr-3 flex items-center justify-center">
                                                    <i class="fas fa-film text-gray-400"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $movie['title'] }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $movie['id'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-center text-sm text-gray-500">
                                        {{ isset($movie['release_date']) && !empty($movie['release_date']) ? \Carbon\Carbon::parse($movie['release_date'])->format('Y') : 'N/A' }}
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center">
                                            <i class="fas fa-star text-yellow-400 mr-1"></i>
                                            <span class="text-gray-700">{{ number_format($movie['vote_average'] ?? 0, 1) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        @if(isset($movie['genre_ids']) && is_array($movie['genre_ids']))
                                            <div class="flex flex-wrap gap-1 justify-center">
                                                @php
                                                    $genres = [
                                                        28 => 'Hành động',
                                                        12 => 'Phiêu lưu',
                                                        16 => 'Hoạt hình',
                                                        35 => 'Hài',
                                                        80 => 'Hình sự',
                                                        99 => 'Tài liệu',
                                                        18 => 'Chính kịch',
                                                        10751 => 'Gia đình',
                                                        14 => 'Giả tưởng',
                                                        36 => 'Lịch sử',
                                                        27 => 'Kinh dị',
                                                        10402 => 'Âm nhạc',
                                                        9648 => 'Bí ẩn',
                                                        10749 => 'Lãng mạn',
                                                        878 => 'Khoa học viễn tưởng',
                                                        10770 => 'TV Movie',
                                                        53 => 'Gây cấn',
                                                        10752 => 'Chiến tranh',
                                                        37 => 'Miền Tây'
                                                    ];
                                                @endphp
                                                @foreach($movie['genre_ids'] as $genreId)
                                                    @if(isset($genres[$genreId]) && $loop->index < 2)
                                                        <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 rounded-md text-xs">
                                                            {{ $genres[$genreId] }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                                @if(count($movie['genre_ids']) > 2)
                                                    <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 rounded-md text-xs">
                                                        +{{ count($movie['genre_ids']) - 2 }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-right">
                                        <button
                                            type="button"
                                            @click="toggleMovieSelection({{ $movie['id'] }})"
                                            class="text-sm px-3 py-1.5 rounded-md"
                                            :class="selectedMovies.includes({{ $movie['id'] }}) ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-blue-100 text-blue-700 hover:bg-blue-200'"
                                        >
                                            <i
                                                class="fas"
                                                :class="selectedMovies.includes({{ $movie['id'] }}) ? 'fa-times' : 'fa-plus'"
                                            ></i>
                                            <span x-text="selectedMovies.includes({{ $movie['id'] }}) ? 'Bỏ chọn' : 'Chọn'"></span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200">
                <div class="text-sm text-gray-500">
                    Hiển thị {{ count($results) }} trên tổng số {{ $total_results }} kết quả
                </div>

                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        @if($page > 1)
                            <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Trang đầu</span>
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Trang trước</span>
                                <i class="fas fa-angle-left"></i>
                            </a>
                        @endif

                        @for($i = max(1, $page - 2); $i <= min($page + 2, $total_pages); $i++)
                            <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                               class="relative inline-flex items-center px-4 py-2 border {{ $i == $page ? 'bg-blue-50 border-blue-500 text-blue-600 z-10' : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50' }} text-sm font-medium">
                                {{ $i }}
                            </a>
                        @endfor

                        @if($page < $total_pages)
                            <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Trang tiếp</span>
                                <i class="fas fa-angle-right"></i>
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['page' => $total_pages]) }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Trang cuối</span>
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        @endif
                    </nav>
                </div>
            </div>
        </div>
    @elseif(request('query') || request('year') || request('genre'))
        <!-- Không tìm thấy kết quả -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-10 flex flex-col items-center justify-center text-center">
                <div class="bg-blue-100 rounded-full p-6 mb-4">
                    <i class="fas fa-search text-blue-500 text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Không tìm thấy kết quả</h3>
                <p class="text-gray-600 max-w-md">Không tìm thấy phim nào phù hợp với tìm kiếm của bạn. Vui lòng thử lại với các từ khóa hoặc bộ lọc khác.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 w-full max-w-2xl">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="font-medium text-gray-900 mb-2">Thử tìm kiếm</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li><a href="{{ route('admin.movies.search', ['query' => 'Avengers']) }}" class="text-blue-600 hover:underline">Avengers</a></li>
                            <li><a href="{{ route('admin.movies.search', ['query' => 'Star Wars']) }}" class="text-blue-600 hover:underline">Star Wars</a></li>
                            <li><a href="{{ route('admin.movies.search', ['query' => 'The Godfather']) }}" class="text-blue-600 hover:underline">The Godfather</a></li>
                        </ul>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="font-medium text-gray-900 mb-2">Thể loại phổ biến</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li><a href="{{ route('admin.movies.search', ['query' => 'action', 'genre' => 28]) }}" class="text-blue-600 hover:underline">Hành động</a></li>
                            <li><a href="{{ route('admin.movies.search', ['query' => 'comedy', 'genre' => 35]) }}" class="text-blue-600 hover:underline">Hài</a></li>
                            <li><a href="{{ route('admin.movies.search', ['query' => 'drama', 'genre' => 18]) }}" class="text-blue-600 hover:underline">Chính kịch</a></li>
                        </ul>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="font-medium text-gray-900 mb-2">Mẹo tìm kiếm</h4>
                        <ul class="text-sm text-gray-600 space-y-1 text-left">
                            <li>• Sử dụng tên phim tiếng Anh</li>
                            <li>• Kiểm tra lỗi chính tả</li>
                            <li>• Sử dụng từ khóa ngắn hơn</li>
                        </ul>
                    </div>
                </div>

                <a href="{{ route('admin.movies.search') }}" class="mt-8 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-redo mr-2"></i>
                    Thử lại
                </a>
            </div>
        </div>
    @else
        <!-- Trang chưa tìm kiếm -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-10 flex flex-col items-center justify-center text-center">
                <div class="bg-blue-100 rounded-full p-6 mb-4">
                    <i class="fas fa-film text-blue-500 text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tìm kiếm phim từ TMDB</h3>
                <p class="text-gray-600 max-w-md">Nhập tên phim vào ô tìm kiếm phía trên để bắt đầu. Bạn có thể tìm kiếm theo tên, năm phát hành hoặc thể loại.</p>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-2xl">
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-5 text-left">
                        <h4 class="font-medium text-blue-800 flex items-center mb-3">
                            <i class="fas fa-lightbulb text-blue-400 mr-2"></i>
                            Phim theo xu hướng
                        </h4>
                        <p class="text-sm text-blue-700 mb-4">Tìm kiếm các phim đang được quan tâm nhiều nhất hiện nay.</p>
                        <a href="{{ route('admin.movies.search', ['query' => 'trending', 'sort_by' => 'popularity.desc']) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
                            Tìm phim xu hướng
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-5 text-left">
                        <h4 class="font-medium text-indigo-800 flex items-center mb-3">
                            <i class="fas fa-star text-indigo-400 mr-2"></i>
                            Phim đánh giá cao
                        </h4>
                        <p class="text-sm text-indigo-700 mb-4">Tìm kiếm các phim có điểm đánh giá cao từ người xem.</p>
                        <a href="{{ route('admin.movies.search', ['query' => 'best', 'sort_by' => 'vote_average.desc']) }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            Tìm phim đánh giá cao
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Import Movies Modal -->
    <div
        x-show="selectedMovies.length > 0 && importingMovies"
        x-cloak
        class="fixed inset-0 overflow-y-auto z-50"
    >
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-file-import text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Nhập phim vào hệ thống</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Đang nhập <span class="font-medium" x-text="selectedMovies.length"></span> phim vào hệ thống. Vui lòng đợi trong giây lát.
                                </p>

                                <div class="mt-4">
                                    <div class="relative pt-1">
                                        <div class="overflow-hidden h-2 text-xs flex rounded bg-blue-100">
                                            <div :style="'width: ' + (importProgress * 100) + '%'" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-300 ease-in-out"></div>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <div class="text-xs text-gray-500">Tiến trình</div>
                                            <div class="text-xs text-gray-500" x-text="Math.round(importProgress * 100) + '%'"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Ẩn các phần tử có thuộc tính x-cloak */
    [x-cloak] { display: none !important; }

    /* CSS cho các chuyển động mượt mà */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    /* Line clamp để giới hạn số dòng hiển thị */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Hiệu ứng hover cho các thẻ */
    .movie-card {
        transition: all 0.3s ease;
    }

    .movie-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection
