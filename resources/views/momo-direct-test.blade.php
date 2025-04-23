@extends('layouts.main')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-gray-800 p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold mb-6 text-white">Thanh toán với MoMo</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Giỏ hàng -->
            <div class="bg-gray-700 p-4 rounded-lg">
                <h2 class="text-xl font-semibold mb-4 text-white">Thông tin giỏ hàng</h2>
                <div class="flex justify-between">
                    <span class="text-gray-300">Tổng sản phẩm:</span>
                    <span class="text-white font-medium">{{ $cart->items->sum('quantity') }} phim</span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-gray-300">Tổng tiền:</span>
                    <span class="text-orange-500 font-bold text-xl">{{ $total }}</span>
                </div>
            </div>
            
            <!-- Thông tin giao hàng -->
            <div class="bg-gray-700 p-4 rounded-lg">
                <h2 class="text-xl font-semibold mb-4 text-white">Thông tin giao hàng</h2>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-300">Họ tên:</span>
                        <span class="text-white">{{ $checkoutData['name'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Email:</span>
                        <span class="text-white">{{ $checkoutData['email'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">SĐT:</span>
                        <span class="text-white">{{ $checkoutData['phone'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Địa chỉ:</span>
                        <span class="text-white">{{ $checkoutData['address'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form để test với dữ liệu khác -->
        <div class="mb-6 bg-gray-700 p-4 rounded-lg">
            <h3 class="text-lg font-medium text-white mb-4">Thay đổi thông tin thanh toán</h3>
            <form action="{{ route('momo-direct-test') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Họ tên</label>
                        <input type="text" name="name" value="{{ old('name', $checkoutData['name'] ?? '') }}" 
                               class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $checkoutData['email'] ?? '') }}" 
                               class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white">
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Số điện thoại</label>
                        <input type="text" name="phone" value="{{ old('phone', $checkoutData['phone'] ?? '') }}" 
                               class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Địa chỉ</label>
                        <input type="text" name="address" value="{{ old('address', $checkoutData['address'] ?? '') }}" 
                               class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white">
                        @error('address')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                        Cập nhật & Tạo thanh toán
                    </button>
                </div>
            </form>
        </div>
        
        @if(isset($jsonResult['payUrl']))
            <div class="bg-gray-700 p-6 rounded-lg mb-6 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-green-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-medium text-white mb-4">Đã tạo thanh toán MoMo thành công!</h3>
                <p class="text-gray-300 mb-6">Vui lòng nhấn vào nút bên dưới để tiếp tục thanh toán.</p>
                
                <a href="{{ $jsonResult['payUrl'] }}" 
                   class="inline-flex items-center justify-center py-3 px-6 border border-transparent text-base font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                    <img src="{{ asset('images/momo-icon.png') }}" alt="MoMo" class="w-6 h-6 mr-2">
                    Thanh toán ngay với MoMo
                </a>
            </div>
            
            <div class="bg-gray-700 p-4 rounded-lg mb-6">
                <h4 class="font-medium text-gray-300 mb-2">Thông tin giao dịch:</h4>
                <p class="text-sm text-gray-400">Order ID: {{ $jsonResult['orderId'] }}</p>
                <p class="text-sm text-gray-400">Transaction ID: {{ $jsonResult['requestId'] }}</p>
            </div>
        @else
            <div class="bg-red-900/50 border border-red-800 p-6 rounded-lg mb-6">
                <h3 class="text-lg font-medium text-white mb-2">Không thể tạo thanh toán</h3>
                <p class="text-gray-300">{{ $jsonResult['message'] ?? 'Lỗi kết nối đến cổng thanh toán MoMo' }}</p>
            </div>
        @endif
        
        <div class="flex flex-col space-y-4">
            <a href="{{ route('checkout') }}" 
               class="inline-block text-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow">
                Quay lại trang thanh toán
            </a>
            
            <a href="{{ route('cart.index') }}" 
               class="inline-block text-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg shadow">
                Quay lại giỏ hàng
            </a>
        </div>
    </div>
</div>
@endsection
