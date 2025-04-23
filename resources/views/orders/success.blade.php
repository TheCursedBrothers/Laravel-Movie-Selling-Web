@extends('layouts.main')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-3xl mx-auto bg-gray-800 rounded-lg shadow-lg p-8 border border-gray-700">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-900 rounded-full mb-4">
                <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white">Đặt hàng thành công!</h1>
            <p class="text-gray-400 mt-2">Cảm ơn bạn đã đặt hàng. Chúng tôi đã gửi email xác nhận đơn hàng tới địa chỉ email của bạn.</p>
        </div>
        
        <div class="border-t border-b border-gray-700 py-6 mb-6">
            <div class="flex justify-between mb-3">
                <span class="font-medium text-gray-300">Mã đơn hàng:</span>
                <span class="font-medium text-white">#{{ $order->id }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium text-gray-300">Ngày đặt hàng:</span>
                <span class="text-gray-300">{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
        
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-white">Thông tin đơn hàng</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="text-left py-2 text-gray-300">Phim</th>
                            <th class="text-center py-2 text-gray-300">Số lượng</th>
                            <th class="text-right py-2 text-gray-300">Giá</th>
                            <th class="text-right py-2 text-gray-300">Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                        <tr class="border-b border-gray-700">
                            <td class="py-4 text-white">{{ $item->movie->title }}</td>
                            <td class="py-4 text-center text-gray-300">{{ $item->quantity }}</td>
                            <td class="py-4 text-right text-gray-300">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                            <td class="py-4 text-right text-orange-400">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right py-4 font-semibold text-gray-300">Tổng cộng:</td>
                            <td class="text-right py-4 font-semibold text-orange-500">{{ number_format($order->total, 0, ',', '.') }}đ</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h3 class="font-semibold mb-2 text-gray-300">Thông tin thanh toán</h3>
                <p class="text-white">{{ $order->name }}</p>
                <p class="text-gray-300">{{ $order->email }}</p>
                <p class="text-gray-300">{{ $order->phone }}</p>
            </div>
            
            <div>
                <h3 class="font-semibold mb-2 text-gray-300">Phương thức thanh toán</h3>
                <p class="text-white">
                    @if ($order->payment_method == 'cod')
                        Thanh toán khi nhận hàng (COD)
                    @elseif ($order->payment_method == 'momo')
                        Thanh toán qua MoMo
                    @else
                        {{ $order->payment_method }}
                    @endif
                </p>
                <p class="mt-1 text-sm">
                    Trạng thái: 
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if ($order->payment_status == 'paid') bg-green-900 text-green-300
                        @elseif ($order->payment_status == 'processing') bg-yellow-900 text-yellow-300
                        @else bg-gray-700 text-gray-300 @endif">
                        @if ($order->payment_status == 'paid') Đã thanh toán
                        @elseif ($order->payment_status == 'processing') Đang xử lý
                        @else Chưa thanh toán @endif
                    </span>
                </p>
            </div>
        </div>
        
        <div class="text-center space-y-4">
            <a href="{{ route('orders.show', $order->id) }}" class="inline-block movie-btn py-2 px-6 rounded-lg">
                Xem chi tiết đơn hàng
            </a>
            
            <div>
                <a href="{{ route('movie.index') }}" class="text-blue-400 hover:text-blue-300 underline">
                    Tiếp tục mua sắm
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
