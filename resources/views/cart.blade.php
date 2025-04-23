@extends('layouts.main')

@section('content')
    <div class="container mx-auto px-4 py-16">
        <h2 class="text-4xl font-semibold text-white mb-8">Giỏ hàng của bạn</h2>

        @if(count($cart->items) > 0)
            <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden">
                <!-- Cart Header -->
                <div class="px-6 py-4 bg-gray-700 border-b border-gray-600 flex flex-wrap md:flex-nowrap justify-between items-center">
                    <h3 class="text-xl font-semibold text-white">Phim đã chọn ({{ $cart->items->sum('quantity') }})</h3>
                    <div class="text-lg mt-2 md:mt-0">
                        Tổng thanh toán: <span class="font-bold text-orange-500">{{ number_format($cart->items->sum(function($item) { return $item->price * $item->quantity; }), 0, ',', '.') }}đ</span>
                    </div>
                </div>

                <!-- Cart Items -->
                <div class="divide-y divide-gray-700">
                    @foreach($cart->items as $item)
                        <div class="flex flex-col md:flex-row items-center px-6 py-6 hover:bg-gray-750 transition duration-150">
                            {{-- Poster phim --}}
                            <div class="flex-shrink-0 w-32 h-48 mb-4 md:mb-0">
                                @if($item->movie->poster_path)
                                    <img src="https://image.tmdb.org/t/p/w185{{ $item->movie->poster_path }}" alt="{{ $item->movie->title }}" class="w-full h-full object-cover rounded shadow-md hover:opacity-75 transition">
                                @else
                                    <div class="w-full h-full bg-gray-600 rounded flex items-center justify-center">
                                        <span class="text-sm text-gray-400">Không có ảnh</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Thông tin phim --}}
                            <div class="md:ml-6 flex-1 text-center md:text-left">
                                <h4 class="text-xl font-medium text-white">{{ $item->movie->title }}</h4>
                                <div class="mt-2 text-gray-400">
                                    @if(isset($item->movie->release_date))
                                        <span>Năm phát hành: {{ \Carbon\Carbon::parse($item->movie->release_date)->format('Y') }}</span>
                                    @endif
                                </div>

                                <div class="mt-4 flex flex-col md:flex-row md:items-center">
                                    <div class="mb-3 md:mb-0 md:mr-6">
                                        <span class="text-orange-500 font-semibold text-lg">{{ number_format($item->price, 0, ',', '.') }}đ</span>
                                        <span class="text-gray-400 text-sm ml-1">/ phim</span>
                                    </div>

                                    <div class="flex items-center">
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center">
                                            @csrf
                                            @method('PATCH')
                                            <label class="text-gray-400 mr-3">Số lượng:</label>
                                            <div class="custom-number-input flex">
                                                <button type="button" onclick="decrementQuantity(this)" class="bg-gray-700 hover:bg-gray-600 text-white w-8 h-8 rounded-l flex items-center justify-center focus:outline-none">-</button>
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="10" class="bg-gray-700 border-t border-b border-gray-600 text-center w-12 h-8 text-white focus:outline-none" readonly>
                                                <button type="button" onclick="incrementQuantity(this)" class="bg-gray-700 hover:bg-gray-600 text-white w-8 h-8 rounded-r flex items-center justify-center focus:outline-none">+</button>
                                            </div>
                                            <button type="submit" class="ml-3 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition focus:outline-none focus:ring-2 focus:ring-blue-400">
                                                Cập nhật
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="mt-4 text-right md:text-left">
                                    <span class="text-gray-400">Thành tiền:</span>
                                    <span class="ml-2 text-white font-semibold">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</span>
                                </div>
                            </div>

                            {{-- Nút xóa --}}
                            <div class="mt-4 md:ml-6 md:mt-0">
                                <form action="{{ route('cart.remove', $item->movie_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-400 transition flex items-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span class="ml-2">Xóa</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Cart Footer -->
                <div class="px-6 py-6 bg-gray-700 border-t border-gray-600 flex flex-col sm:flex-row justify-between items-center">
                    <form action="{{ route('cart.clear') }}" method="POST" class="mb-4 sm:mb-0">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-400 transition flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Xóa giỏ hàng
                        </button>
                    </form>

                    <div class="flex flex-col items-end">
                        <div class="text-gray-300 mb-2">
                            Tổng <span class="font-medium">{{ $cart->items->sum('quantity') }}</span> phim:
                            <span class="font-semibold text-white">{{ number_format($cart->items->sum(function($item) { return $item->price * $item->quantity; }), 0, ',', '.') }}đ</span>
                        </div>

                        @auth
                            <a href="{{ route('checkout') }}" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold px-6 py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 shadow-lg hover:shadow-green-500/30 transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                Thanh toán ngay
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold px-6 py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-lg hover:shadow-blue-500/30 transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Đăng nhập để thanh toán
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        @else
            <div class="mt-8 bg-gray-800 rounded-lg p-10 text-center shadow-xl">
                <svg class="w-20 h-20 text-gray-500 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-xl text-gray-300 mb-6">Giỏ hàng của bạn đang trống</p>
                <a href="{{ route('movie.index') }}" class="inline-block bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold px-6 py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-lg hover:shadow-blue-500/30 transition">
                    Tiếp tục mua sắm
                </a>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function incrementQuantity(button) {
            const input = button.parentNode.querySelector('input');
            const currentValue = parseInt(input.value);
            if (currentValue < 10) {
                input.value = currentValue + 1;
            }
        }

        function decrementQuantity(button) {
            const input = button.parentNode.querySelector('input');
            const currentValue = parseInt(input.value);
            if (currentValue > 1) {
                input.value = currentValue - 1;
            }
        }
    </script>
    @endpush
@endsection
