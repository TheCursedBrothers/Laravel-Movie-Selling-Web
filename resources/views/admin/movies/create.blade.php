@extends('admin.layouts.app')

@section('title', 'Thêm phim mới')

@section('breadcrumb')
    <a href="{{ route('admin.movies.index') }}">Quản lý phim</a> / Thêm phim mới
@endsection

@section('content')
<div class="space-y-6">
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Thêm phim mới</h2>

        <p class="mb-6 text-gray-500">
            <i class="fas fa-info-circle mr-2"></i>
            Bạn nên sử dụng chức năng <a href="{{ route('admin.movies.search') }}" class="text-blue-600 hover:underline">tìm kiếm từ TMDB</a> để thêm phim mới với đầy đủ thông tin.
        </p>

        <form action="{{ route('admin.movies.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tmdb_id" class="block text-sm font-medium text-gray-700">TMDB ID *</label>
                    <input type="number" name="tmdb_id" id="tmdb_id" value="{{ old('tmdb_id') }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <p class="mt-1 text-xs text-gray-500">ID phim trên TMDB, ví dụ: 550 (Fight Club)</p>
                    @error('tmdb_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Giá *</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">₫</span>
                        </div>
                        <input type="number" name="price" id="price" value="{{ old('price', 120000) }}" required
                            class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-8 pr-12 sm:text-sm border-gray-300 rounded-md"
                            placeholder="0">
                    </div>
                    @error('price')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700">Tồn kho *</label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', 100) }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('stock')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-start pt-6">
                    <div class="flex items-center h-5">
                        <input id="active" name="active" type="checkbox" checked
                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="active" class="font-medium text-gray-700">Hiển thị phim</label>
                        <p class="text-gray-500">Phim sẽ hiển thị trên trang chủ và có thể mua.</p>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('admin.movies.index') }}"
                    class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Hủy
                </a>
                <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Thêm phim
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
