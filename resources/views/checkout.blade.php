@extends('layouts.main')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Breadcrumb -->
        <div class="mb-8 flex items-center text-sm text-gray-400">
            <a href="{{ route('movie.index') }}" class="hover:text-blue-400">Trang chủ</a>
            <span class="mx-2">›</span>
            <a href="{{ route('cart.index') }}" class="hover:text-blue-400">Giỏ hàng</a>
            <span class="mx-2">›</span>
            <span class="text-gray-200 font-medium">Thanh toán</span>
        </div>

        <h1 class="text-3xl font-bold mb-10 text-center text-white">Hoàn tất đơn hàng</h1>
        
        <div class="flex flex-col lg:flex-row gap-10">
            <!-- Left Column: Order Details & Form -->
            <div class="lg:w-2/3">
                <!-- Cart Summary -->
                <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-8 border border-gray-700">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-800 to-gray-700 border-b border-gray-700">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Giỏ hàng của bạn ({{ $cart->items->sum('quantity') }} phim)
                        </h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-xs uppercase tracking-wider text-gray-400 bg-gray-700">
                                    <th class="px-6 py-3">Phim</th>
                                    <th class="px-6 py-3 text-center">Số lượng</th>
                                    <th class="px-6 py-3 text-right">Giá</th>
                                    <th class="px-6 py-3 text-right">Tổng</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach ($cart->items as $item)
                                <tr class="hover:bg-gray-750 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if ($item->movie->poster_path)
                                                <img src="https://image.tmdb.org/t/p/w92{{ $item->movie->poster_path }}" 
                                                    alt="{{ $item->movie->title }}" 
                                                    class="w-12 h-auto rounded-md mr-4 shadow-sm">
                                            @else
                                                <div class="w-12 h-18 bg-gray-600 rounded-md mr-4 flex items-center justify-center">
                                                    <span class="text-xs text-gray-400">No image</span>
                                                </div>
                                            @endif
                                            <span class="font-medium text-white">{{ $item->movie->title }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-300">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-right text-gray-300">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                    <td class="px-6 py-4 text-right font-medium text-orange-400">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-750 border-t border-gray-700">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-200">Tổng cộng:</span>
                            <span class="text-xl font-bold text-orange-500">{{ number_format($cart->total(), 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                </div>
                
                <!-- Checkout Form -->
                <form action="{{ route('orders.store') }}" method="POST" class="bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-700">
                    @csrf
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-800 to-gray-700 border-b border-gray-700">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Thông tin giao hàng
                        </h2>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Họ tên</label>
                                <input type="text" name="name" id="name" value="{{ auth()->user()->name }}" 
                                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white transition-colors" required>
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                                <input type="email" name="email" id="email" value="{{ auth()->user()->email }}" 
                                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white transition-colors" required>
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-300 mb-1">Số điện thoại</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white transition-colors" required>
                                @error('phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-300 mb-1">Địa chỉ</label>
                                <input type="text" name="address" id="address" value="{{ old('address') }}" 
                                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white transition-colors" required>
                                @error('address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-300 mb-1">Ghi chú</label>
                            <textarea name="note" id="note" rows="3" 
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white transition-colors">{{ old('note') }}</textarea>
                        </div>
                        
                        <div class="border-t border-b border-gray-700 py-6">
                            <h3 class="text-md font-semibold text-white mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                </svg>
                                Phương thức thanh toán
                            </h3>
                            
                            <div class="space-y-3">
                                <label class="flex items-center p-4 border border-gray-600 rounded-lg cursor-pointer hover:border-blue-500 transition-colors bg-gray-750">
                                    <input type="radio" name="payment_method" value="momo" checked
                                        class="h-5 w-5 text-blue-600 focus:ring-blue-500 bg-gray-700 border-gray-600 cursor-pointer">
                                    <div class="ml-3 flex items-center">
                                        <span class="block text-white font-medium mr-3">Thanh toán qua MoMo</span>
                                        <img src="{{ asset('images/momo-icon.png') }}" alt="MoMo" class="h-6 w-6">
                                    </div>
                                </label>
                            </div>
                            
                            @error('payment_method')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="px-6 py-6 border-t border-gray-700 bg-gray-750">
                        <button type="submit" class="w-full flex justify-center items-center movie-btn py-4 px-6 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Hoàn tất đặt hàng và thanh toán
                        </button>
                        
                        <p class="mt-4 text-sm text-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Bạn sẽ được chuyển đến cổng thanh toán MoMo sau khi hoàn tất đặt hàng
                        </p>
                        
                        <!-- Add debug info for testing -->
                        @if(app()->environment('local'))
                        <div class="mt-6 p-4 bg-gray-700 rounded-lg text-sm text-gray-300">
                            <p>Debug: Form will be submitted to <code>{{ route('orders.store') }}</code></p>
                            <p>MoMo API endpoint: <code>https://test-payment.momo.vn/v2/gateway/api/create</code></p>
                            <p>MoMo callback URL: <code>{{ url('/payments/momo/callback') }}</code></p>
                            <p>MoMo IPN URL: <code>{{ url('/payments/momo/ipn') }}</code></p>
                            <p><a href="{{ url('/payments/momo/test') }}" class="text-blue-400 underline">Run MoMo connection test</a></p>
                        </div>
                        @endif
                        
                        <p class="text-xs text-center text-gray-400 mt-4">
                            Bằng cách đặt hàng, bạn đồng ý với 
                            <a href="#" class="text-blue-400 hover:text-blue-300 underline">Điều khoản dịch vụ</a> và 
                            <a href="#" class="text-blue-400 hover:text-blue-300 underline">Chính sách bảo mật</a> của chúng tôi.
                        </p>
                    </div>
                </form>
            </div>
            
            <!-- Right Column: Order Summary -->
            <div class="lg:w-1/3">
                <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-700 sticky top-6">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-800 to-gray-700 border-b border-gray-700">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Tóm tắt đơn hàng
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between pb-4 border-b border-gray-700">
                                <span class="text-gray-400">Số lượng phim:</span>
                                <span class="font-medium text-white">{{ $cart->items->sum('quantity') }}</span>
                            </div>
                            
                            <div class="flex justify-between pb-4 border-b border-gray-700">
                                <span class="text-gray-400">Giá trị đơn hàng:</span>
                                <span class="font-medium text-white">{{ number_format($cart->total(), 0, ',', '.') }}đ</span>
                            </div>
                            
                            <div class="flex justify-between pt-2">
                                <span class="font-semibold text-gray-300">Tổng thanh toán:</span>
                                <span class="text-xl font-bold text-orange-500">{{ number_format($cart->total(), 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                        
                        <div class="mt-8">
                            <div class="rounded-lg bg-gray-750 p-4 border border-gray-700">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-white">Thông tin quan trọng</h3>
                                        <div class="mt-2 text-xs text-gray-400 space-y-1">
                                            <p>Vui lòng kiểm tra kỹ thông tin đơn hàng trước khi xác nhận thanh toán.</p>
                                            <p>Đối với các đơn hàng thanh toán qua MoMo, bạn sẽ được chuyển đến trang thanh toán của MoMo sau khi xác nhận.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <a href="{{ route('cart.index') }}" class="block text-center text-blue-400 hover:text-blue-300 font-medium">
                                ← Quay lại giỏ hàng
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
