<div class="relative mt-3 md:mt-0">
    <form action="/search" method="GET" class="search-form">
        <input
            id="search-input"
            name="query"
            type="text"
            value="{{ $search }}"
            class="search-input bg-gray-700 text-white text-sm rounded-lg w-72 px-5 pl-10 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-md"
            placeholder="Tìm kiếm phim..."
            wire:model.debounce.300ms="search"
        >

        <div class="absolute top-0 left-3 flex items-center h-full mr-2">
            <button type="submit" class="text-gray-400 hover:text-white focus:outline-none">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>
    </form>

    @if(strlen($search) >= 2 && count($searchResults) > 0)
        <div class="absolute z-50 bg-gray-800 text-sm rounded-lg w-72 mt-2 overflow-hidden shadow-xl">
            <ul>
                @foreach($searchResults as $result)
                    <li class="border-b border-gray-700 last:border-0">
                        <a href="{{ route('movies.show', $result['id']) }}" class="block hover:bg-gray-700 transition ease-in-out duration-150">
                            <div class="flex items-center px-3 py-3">
                                @if(isset($result['poster_path']) && $result['poster_path'])
                                    <img src="https://image.tmdb.org/t/p/w92{{ $result['poster_path'] }}" alt="{{ $result['title'] }}" class="w-10 h-15 object-cover rounded">
                                @else
                                    <div class="w-10 h-15 bg-gray-600 rounded flex items-center justify-center">
                                        <span class="text-xs text-gray-400">No Image</span>
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <div class="font-medium text-white">{{ $result['title'] }}</div>
                                    <div class="text-xs text-gray-400">
                                        {{ isset($result['release_date']) ? \Carbon\Carbon::parse($result['release_date'])->format('M d, Y') : 'Unknown date' }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
                <li class="border-t border-gray-700 px-4 py-3 bg-gray-750">
                    <a href="/search?query={{ urlencode($search) }}" class="flex items-center justify-center text-blue-400 hover:text-blue-300">
                        <span>Xem tất cả kết quả</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
    @endif
</div>
