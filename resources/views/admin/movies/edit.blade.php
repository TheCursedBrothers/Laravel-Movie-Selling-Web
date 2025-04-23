@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa phim')

@section('breadcrumb')
    <a href="{{ route('admin.movies.index') }}">Quản lý phim</a> / Chỉnh sửa phim
@endsection

@section('content')
    <div class="space-y-6">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-xl font-medium text-gray-900">Chỉnh sửa phim "{{ $movie->title }}"</h2>
            </div>

            <form action="{{ route('admin.movies.update', $movie->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <!-- Add this line to debug route issues -->
                <input type="hidden" name="_debug_route" value="{{ route('admin.movies.update', $movie->id) }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Tên phim *</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $movie->title) }}"
                            required
                            class="block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Giá *</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₫</span>
                            </div>
                            <input type="number" name="price" id="price" value="{{ old('price', $movie->price) }}"
                                required
                                class="pl-8 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Tồn kho *</label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', $movie->stock) }}"
                            required
                            class="block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                        @error('stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label for="overview" class="block text-sm font-medium text-gray-700 mb-1">Mô tả phim</label>
                        <textarea name="overview" id="overview" rows="5"
                            class="block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">{{ old('overview', $movie->overview) }}</textarea>
                    </div>

                    <div class="col-span-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="active" name="active" type="checkbox"
                                    {{ old('active', $movie->active) ? 'checked' : '' }}
                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="active" class="font-medium text-gray-700">Hiển thị phim</label>
                                <p class="text-gray-500">Phim sẽ hiển thị trên trang chủ và có thể mua.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-between border-t border-gray-200">
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.movies.index') }}"
                            class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Hủy
                        </a>
                        <a href="{{ route('admin.movies.show', $movie->id) }}"
                            class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Xem chi tiết
                        </a>
                    </div>
                    <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Lưu thay đổi
                    </button>
                </div>
            </form>

            <div class="p-6 bg-gray-50 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin từ TMDB</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        @if ($movie->poster_path)
                            <img src="https://image.tmdb.org/t/p/w342{{ $movie->poster_path }}" alt="{{ $movie->title }}"
                                class="w-full h-auto rounded shadow">
                        @else
                            <div class="w-full aspect-[2/3] bg-gray-200 flex items-center justify-center rounded">
                                <i class="fas fa-film text-gray-400 text-5xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="md:col-span-2">
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">TMDB ID:</dt>
                                <dd class="text-sm text-gray-900">{{ $movie->tmdb_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ngày phát hành:</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ $movie->release_date ? date('d/m/Y', strtotime($movie->release_date)) : 'N/A' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Đánh giá:</dt>
                                <dd class="text-sm text-gray-900">{{ $movie->vote_average ?? 'N/A' }}/10
                                    ({{ $movie->vote_count ?? '0' }} lượt)</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Thể loại:</dt>
                                <dd class="text-sm text-gray-900">
                                    @if ($movie->genres)
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            @foreach ((array) $movie->genres as $genre)
                                                <span class="px-2 py-1 text-xs rounded-md bg-blue-100 text-blue-800">
                                                    {{ $genre }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span>Không có thông tin</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>

                        <div class="mt-4">
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Thông tin này được lấy từ TMDB và không thể chỉnh sửa trực tiếp.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
