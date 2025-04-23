@extends('admin.layouts.app')

@section('title', 'Chi tiết phim')

@section('breadcrumb')
    <a href="{{ route('admin.movies.index') }}">Quản lý phim</a> / Chi tiết phim
@endsection

@section('content')
<div class="space-y-6">
    <div class="bg-white shadow overflow-hidden rounded-lg">
        <div class="px-4 py-4 sm:px-6 flex justify-between items-center bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Thông tin phim</h3>
            <div class="flex space-x-3">
                <a href="{{ route('admin.movies.edit', $movie->id) }}"
                   class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:shadow-outline-blue active:bg-blue-600 transition ease-in-out duration-150">
                    <i class="fas fa-edit mr-2"></i>
                    Sửa
                </a>
                <form action="{{ route('admin.movies.destroy', $movie->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa phim này?')"
                            class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:shadow-outline-red active:bg-red-600 transition ease-in-out duration-150">
                        <i class="fas fa-trash mr-2"></i>
                        Xóa
                    </button>
                </form>
            </div>
        </div>
        <div class="border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6">
                <div class="md:col-span-1">
                    @if($movie->poster_path)
                        <img src="https://image.tmdb.org/t/p/w342{{ $movie->poster_path }}"
                             alt="{{ $movie->title }}"
                             class="w-full h-auto rounded shadow">
                    @else
                        <div class="w-full aspect-[2/3] bg-gray-200 flex items-center justify-center rounded">
                            <i class="fas fa-film text-gray-400 text-5xl"></i>
                        </div>
                    @endif

                    <div class="mt-4 bg-gray-50 border rounded p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-500">Trạng thái:</span>
                            @if($movie->active)
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Đang bán
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Ẩn
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-500">Giá:</span>
                            <span class="text-sm text-gray-900 font-bold">{{ number_format($movie->price, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Tồn kho:</span>
                            <span class="text-sm text-gray-900">{{ $movie->stock }}</span>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $movie->title }}</h1>

                    <div class="mt-4 flex items-center text-sm text-gray-500">
                        <span>
                            <i class="fas fa-calendar mr-1"></i>
                            Phát hành: {{ $movie->release_date ? date('d/m/Y', strtotime($movie->release_date)) : 'N/A' }}
                        </span>
                        <span class="ml-6">
                            <i class="fas fa-star mr-1 text-yellow-400"></i>
                            Đánh giá: {{ $movie->vote_average ?? 'N/A' }}/10
                        </span>
                    </div>

                    @if($movie->genres)
                    <div class="mt-3">
                        <span class="text-sm font-medium text-gray-500">Thể loại:</span>
                        <div class="mt-1 flex flex-wrap gap-2">
                            @foreach((array)$movie->genres as $genre)
                                <span class="px-2 py-1 text-xs rounded-md bg-blue-100 text-blue-800">
                                    {{ $genre }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="mt-4">
                        <h3 class="text-lg font-medium text-gray-900">Nội dung</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $movie->overview ?? 'Không có mô tả.' }}
                        </p>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900">Thông tin bổ sung</h3>
                        <div class="mt-2 border-t border-gray-200 pt-4">
                            <dl class="sm:divide-y sm:divide-gray-200">
                                <div class="py-2 sm:py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">TMDB ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $movie->tmdb_id }}</dd>
                                </div>
                                <div class="py-2 sm:py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Số lượt đánh giá</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $movie->vote_count ?? 'N/A' }}</dd>
                                </div>
                                <div class="py-2 sm:py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Thời gian thêm</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $movie->created_at->format('d/m/Y H:i:s') }}</dd>
                                </div>
                                <div class="py-2 sm:py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Cập nhật lần cuối</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $movie->updated_at->format('d/m/Y H:i:s') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
            <a href="{{ route('admin.movies.index') }}" class="text-sm text-blue-600 hover:text-blue-500">
                <i class="fas fa-arrow-left mr-1"></i>
                Quay lại danh sách
            </a>

            <a href="https://www.themoviedb.org/movie/{{ $movie->tmdb_id }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-500">
                Xem trên TMDB
                <i class="fas fa-external-link-alt ml-1"></i>
            </a>
        </div>
    </div>
</div>
@endsection
