@extends('layouts.main')

@section('content')
<div class="container mx-auto px-4 py-10">
    <div class="max-w-5xl mx-auto">
        <!-- Breadcrumb với thiết kế mới -->
        <div class="mb-8 flex items-center text-sm bg-gray-800/50 px-4 py-2 rounded-md shadow-sm">
            <a href="{{ route('movie.index') }}" class="text-blue-400 hover:text-blue-300 transition-colors">Trang chủ</a>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('orders.index') }}" class="text-blue-400 hover:text-blue-300 transition-colors">Đơn hàng của tôi</a>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-200 font-medium">Đơn hàng #{{ $order->id }}</span>
        </div>

        <!-- Header với status badge cải tiến -->
        <div class="bg-gray-800/70 border border-gray-700 rounded-xl p-6 mb-8 shadow-lg">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h1 class="text-3xl font-bold text-white">
                            Đơn hàng #{{ $order->id }}
                        </h1>
                    </div>
                    <p class="text-gray-400 mt-2 ml-11">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div class="flex flex-wrap items-center gap-3 ml-11 md:ml-0">
                    <!-- Trạng thái đơn hàng -->
                    <div class="flex items-center px-4 py-2 rounded-full
                        @if($order->order_status == 'completed') bg-green-900/50 border border-green-700
                        @elseif($order->order_status == 'processing') bg-blue-900/50 border border-blue-700
                        @elseif($order->order_status == 'shipping') bg-yellow-900/50 border border-yellow-700
                        @elseif($order->order_status == 'cancelled') bg-red-900/50 border border-red-700
                        @else bg-gray-900/50 border border-gray-700 @endif">
                        <div class="relative w-3 h-3 rounded-full mr-2
                            @if($order->order_status == 'completed') bg-green-500
                            @elseif($order->order_status == 'processing') bg-blue-500
                            @elseif($order->order_status == 'shipping') bg-yellow-500
                            @elseif($order->order_status == 'cancelled') bg-red-500
                            @else bg-gray-500 @endif">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-50
                                @if($order->order_status == 'completed') bg-green-400
                                @elseif($order->order_status == 'processing') bg-blue-400
                                @elseif($order->order_status == 'shipping') bg-yellow-400
                                @elseif($order->order_status == 'cancelled') bg-red-400
                                @else bg-gray-400 @endif"></span>
                        </div>
                        <span class="font-medium text-sm
                            @if($order->order_status == 'completed') text-green-400
                            @elseif($order->order_status == 'processing') text-blue-400
                            @elseif($order->order_status == 'shipping') text-yellow-400
                            @elseif($order->order_status == 'cancelled') text-red-400
                            @else text-gray-400 @endif">
                            @if($order->order_status == 'completed') Đã hoàn thành
                            @elseif($order->order_status == 'processing') Đang xử lý
                            @elseif($order->order_status == 'shipping') Đang giao hàng
                            @elseif($order->order_status == 'cancelled') Đã hủy
                            @else Chờ xử lý @endif
                        </span>
                    </div>

                    <!-- Trạng thái thanh toán -->
                    <div class="flex items-center px-4 py-2 rounded-full
                        @if($order->payment_status == 'paid') bg-green-900/50 border border-green-700
                        @elseif($order->payment_status == 'processing') bg-yellow-900/50 border border-yellow-700
                        @elseif($order->payment_status == 'failed') bg-red-900/50 border border-red-700
                        @else bg-gray-900/50 border border-gray-700 @endif">
                        <div class="relative w-3 h-3 rounded-full mr-2
                            @if($order->payment_status == 'paid') bg-green-500
                            @elseif($order->payment_status == 'processing') bg-yellow-500
                            @elseif($order->payment_status == 'failed') bg-red-500
                            @else bg-gray-500 @endif">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-50
                                @if($order->payment_status == 'paid') bg-green-400
                                @elseif($order->payment_status == 'processing') bg-yellow-400
                                @elseif($order->payment_status == 'failed') bg-red-400
                                @else bg-gray-400 @endif"></span>
                        </div>
                        <span class="font-medium text-sm
                            @if($order->payment_status == 'paid') text-green-400
                            @elseif($order->payment_status == 'processing') text-yellow-400
                            @elseif($order->payment_status == 'failed') text-red-400
                            @else text-gray-400 @endif">
                            @if($order->payment_status == 'paid') Đã thanh toán
                            @elseif($order->payment_status == 'processing') Đang xử lý thanh toán
                            @elseif($order->payment_status == 'failed') Thanh toán thất bại
                            @else Chưa thanh toán @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin đơn hàng chính - thiết kế với hiệu ứng gradient cải tiến -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Thông tin cơ bản -->
            <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg border border-gray-700 group hover:border-blue-600 transition-colors duration-300">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-800 to-gray-700 border-b border-gray-700 group-hover:from-gray-700 group-hover:to-gray-600 transition-colors duration-300">
                    <h3 class="text-lg font-medium text-white flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400 group-hover:text-blue-300 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Thông tin đơn hàng
                    </h3>
                </div>
                <div class="p-6 space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Mã đơn hàng:</span>
                        <span class="text-white font-medium">#{{ $order->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Ngày đặt hàng:</span>
                        <span class="text-white">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Số lượng phim:</span>
                        <span class="text-white">{{ $order->items->sum('quantity') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Tổng thanh toán:</span>
                        <span class="text-orange-500 font-semibold">{{ number_format($order->total, 0, ',', '.') }}đ</span>
                    </div>

                    @if($order->note)
                    <div class="mt-4 pt-4 border-t border-gray-700">
                        <p class="text-gray-400 mb-1">Ghi chú đơn hàng:</p>
                        <p class="text-white bg-gray-700/50 p-3 rounded-md border border-gray-600">{{ $order->note }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Thông tin người nhận -->
            <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg border border-gray-700 group hover:border-blue-600 transition-colors duration-300">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-800 to-gray-700 border-b border-gray-700 group-hover:from-gray-700 group-hover:to-gray-600 transition-colors duration-300">
                    <h3 class="text-lg font-medium text-white flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400 group-hover:text-blue-300 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Thông tin người nhận
                    </h3>
                </div>
                <div class="p-6 space-y-4 text-sm">
                    <div class="flex items-center mb-4">
                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-medium text-white">{{ $order->name }}</h4>
                            <p class="text-blue-400 hover:text-blue-300 transition-colors">{{ $order->email }}</p>
                        </div>
                    </div>

                    <div class="space-y-3 pt-3 border-t border-gray-700">
                        <div>
                            <div class="flex items-center text-gray-400 mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                Số điện thoại:
                            </div>
                            <p class="text-white bg-gray-700/50 p-2 rounded-md border border-gray-600 ml-6">{{ $order->phone }}</p>
                        </div>
                        <div>
                            <div class="flex items-center text-gray-400 mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Địa chỉ:
                            </div>
                            <p class="text-white bg-gray-700/50 p-2 rounded-md border border-gray-600 ml-6">{{ $order->address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin thanh toán -->
            <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg border border-gray-700 group hover:border-blue-600 transition-colors duration-300">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-800 to-gray-700 border-b border-gray-700 group-hover:from-gray-700 group-hover:to-gray-600 transition-colors duration-300">
                    <h3 class="text-lg font-medium text-white flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400 group-hover:text-blue-300 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Thông tin thanh toán
                    </h3>
                </div>
                <div class="p-6 space-y-4 text-sm">
                    <!-- Payment method -->
                    <div class="flex items-center mb-2">
                        @if($order->payment_method == 'momo')
                            <div class="h-12 w-12 rounded-lg bg-pink-600 flex items-center justify-center shadow-lg">
                                @if(file_exists(public_path('images/momo-icon.png')))
                                    <img src="{{ asset('images/momo-icon.png') }}" alt="MoMo" class="h-8 w-8">
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="ml-4">
                                <p class="text-white font-medium">Thanh toán qua MoMo</p>
                                <p class="text-gray-400 text-xs">Cổng thanh toán trực tuyến</p>
                            </div>
                        @elseif($order->payment_method == 'cod')
                            <div class="h-12 w-12 rounded-lg bg-green-600 flex items-center justify-center shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-white font-medium">Thanh toán khi nhận hàng</p>
                                <p class="text-gray-400 text-xs">Thanh toán tiền mặt khi nhận hàng</p>
                            </div>
                        @else
                            <div class="h-12 w-12 rounded-lg bg-gray-600 flex items-center justify-center shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-white font-medium">{{ ucfirst($order->payment_method) }}</p>
                                <p class="text-gray-400 text-xs">Phương thức thanh toán</p>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-3 pt-4 border-t border-gray-700">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Trạng thái:</span>
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @if($order->payment_status == 'paid') bg-green-900 text-green-300 border border-green-700
                                @elseif($order->payment_status == 'processing') bg-yellow-900 text-yellow-300 border border-yellow-700
                                @elseif($order->payment_status == 'failed') bg-red-900 text-red-300 border border-red-700
                                @else bg-gray-800 text-gray-300 border border-gray-700 @endif">
                                @if($order->payment_status == 'paid') Đã thanh toán
                                @elseif($order->payment_status == 'processing') Đang xử lý
                                @elseif($order->payment_status == 'failed') Thanh toán thất bại
                                @else Chưa thanh toán @endif
                            </span>
                        </div>

                        @if(isset($order->transaction_id))
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Mã giao dịch:</span>
                            <span class="text-white font-mono bg-gray-700/50 px-2 py-1 rounded-md border border-gray-600 text-xs">{{ $order->transaction_id }}</span>
                        </div>
                        @endif

                        @if($order->payment_status != 'paid' && $order->payment_method == 'momo')
                            <div class="mt-4 pt-3 border-t border-gray-700">
                                @if(isset($order->payment_details['payUrl']))
                                    <a href="{{ $order->payment_details['payUrl'] }}"
                                       class="w-full block text-center bg-pink-600 hover:bg-pink-700 text-white font-medium py-2 px-4 rounded-lg transition-colors shadow-md hover:shadow-pink-700/20">
                                        <div class="flex items-center justify-center">
                                            @if(file_exists(public_path('images/momo-icon.png')))
                                                <img src="{{ asset('images/momo-icon.png') }}" alt="MoMo" class="h-5 w-5 mr-2">
                                            @endif
                                            Thanh toán lại với MoMo
                                        </div>
                                    </a>
                                @else
                                    <a href="{{ route('payments.momo', $order->id) }}"
                                       class="w-full block text-center bg-pink-600 hover:bg-pink-700 text-white font-medium py-2 px-4 rounded-lg transition-colors shadow-md hover:shadow-pink-700/20">
                                        <div class="flex items-center justify-center">
                                            @if(file_exists(public_path('images/momo-icon.png')))
                                                <img src="{{ asset('images/momo-icon.png') }}" alt="MoMo" class="h-5 w-5 mr-2">
                                            @endif
                                            Tạo lại thanh toán
                                        </div>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Chi tiết sản phẩm - thiết kế mới -->
        <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-700 mb-8 hover:border-blue-600 transition-colors duration-300 group">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-800 to-gray-700 border-b border-gray-700 group-hover:from-gray-700 group-hover:to-gray-600 transition-colors duration-300">
                <h3 class="text-lg font-medium text-white flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400 group-hover:text-blue-300 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Danh sách phim đã đặt
                    <span class="ml-2 bg-blue-600 text-white text-xs rounded-full px-2 py-1 inline-flex items-center justify-center">
                        {{ $order->items->sum('quantity') }}
                    </span>
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-full">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-gray-400 bg-gray-700">
                            <th class="px-6 py-3">Phim</th>
                            <th class="px-6 py-3 text-center">Số lượng</th>
                            <th class="px-6 py-3 text-right">Đơn giá</th>
                            <th class="px-6 py-3 text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($order->items as $item)
                        <tr class="hover:bg-gray-750 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($item->movie->poster_path)
                                        <img src="https://image.tmdb.org/t/p/w92{{ $item->movie->poster_path }}"
                                            alt="{{ $item->movie->title }}"
                                            class="w-12 h-auto rounded-md mr-4 shadow-md hover:opacity-75 transition-opacity">
                                    @else
                                        <div class="w-12 h-18 bg-gray-600 rounded-md mr-4 flex items-center justify-center">
                                            <span class="text-xs text-gray-400">No image</span>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('movies.show', $item->movie) }}" class="text-white hover:text-blue-400 font-medium transition-colors">
                                            {{ $item->movie->title }}
                                        </a>
                                        @if(isset($item->movie->release_date))
                                            <p class="text-gray-400 text-sm">{{ $item->movie->release_date->format('Y') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-300">
                                <span class="inline-flex items-center justify-center bg-gray-700 px-3 py-1 rounded-full">
                                    {{ $item->quantity }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-gray-300">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                            <td class="px-6 py-4 text-right font-medium">
                                <span class="text-orange-400 bg-orange-900/20 border border-orange-800/30 px-3 py-1 rounded-md">
                                    {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-750 border-t-2 border-gray-600">
                            <td colspan="3" class="px-6 py-4 text-right font-medium text-gray-300">Tổng cộng:</td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-xl font-bold text-orange-500 bg-orange-900/30 px-4 py-2 rounded-lg border border-orange-800/40">
                                    {{ number_format($order->total, 0, ',', '.') }}đ
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Timeline tiến trình đơn hàng - Thiết kế cải tiến -->
        <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-700 mb-8 hover:border-blue-600 transition-colors duration-300 group">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-800 to-gray-700 border-b border-gray-700 group-hover:from-gray-700 group-hover:to-gray-600 transition-colors duration-300">
                <h3 class="text-lg font-medium text-white flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400 group-hover:text-blue-300 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Tiến trình đơn hàng
                </h3>
            </div>
            <div class="p-6">
                <div class="relative">
                    <!-- Thanh timeline dọc -->
                    <div class="absolute left-5 top-0 h-full w-1 bg-gradient-to-b from-blue-600 via-gray-700 to-gray-700"></div>

                    <ol class="relative space-y-6 pl-12">
                        <!-- Đặt hàng -->
                        <li class="relative">
                            <div class="absolute -left-7 mt-1.5 h-5 w-5 rounded-full border-2 border-blue-500 bg-gray-800 flex items-center justify-center shadow-lg shadow-blue-500/20">
                                <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                            </div>
                            <div class="bg-gray-750 p-4 rounded-lg border border-gray-700 hover:border-blue-600 transition-colors duration-300">
                                <time class="mb-1 text-sm font-normal text-blue-400">{{ $order->created_at->format('d/m/Y H:i') }}</time>
                                <h3 class="text-lg font-semibold text-white">Đơn hàng đã được đặt</h3>
                                <p class="text-base font-normal text-gray-400">Đơn hàng của bạn đã được tạo thành công và đang chờ xử lý.</p>
                            </div>
                        </li>

                        <!-- Thanh toán -->
                        @if($order->payment_status == 'paid' || $order->payment_status == 'processing')
                        <li class="relative">
                            <div class="absolute -left-7 mt-1.5 h-5 w-5 rounded-full border-2
                                @if($order->payment_status == 'paid') border-green-500 @else border-yellow-500 @endif
                                bg-gray-800 flex items-center justify-center
                                @if($order->payment_status == 'paid') shadow-lg shadow-green-500/20 @else shadow-lg shadow-yellow-500/20 @endif">
                                <div class="h-2 w-2 rounded-full
                                    @if($order->payment_status == 'paid') bg-green-500 @else bg-yellow-500 @endif"></div>
                            </div>
                            <div class="bg-gray-750 p-4 rounded-lg border border-gray-700
                                 @if($order->payment_status == 'paid') hover:border-green-600 @else hover:border-yellow-600 @endif
                                 transition-colors duration-300">
                                <time class="mb-1 text-sm font-normal
                                      @if($order->payment_status == 'paid') text-green-400 @else text-yellow-400 @endif">
                                    @if(isset($order->payment_details['payment_time']))
                                        {{ \Carbon\Carbon::parse($order->payment_details['payment_time'])->format('d/m/Y H:i') }}
                                    @else
                                        {{ $order->updated_at->format('d/m/Y H:i') }}
                                    @endif
                                </time>
                                <h3 class="text-lg font-semibold
                                    @if($order->payment_status == 'paid') text-green-400 @else text-yellow-400 @endif">
                                    @if($order->payment_status == 'paid')
                                        Thanh toán thành công
                                    @else
                                        Đang xử lý thanh toán
                                    @endif
                                </h3>
                                <p class="text-base font-normal text-gray-400">
                                    @if($order->payment_status == 'paid')
                                        Đơn hàng đã được thanh toán thành công qua {{ $order->payment_method == 'momo' ? 'MoMo' : ucfirst($order->payment_method) }}.
                                    @else
                                        Hệ thống đang xử lý thanh toán của bạn.
                                    @endif
                                </p>
                                @if(isset($order->transaction_id))
                                    <div class="mt-2 flex items-center">
                                        <span class="text-sm text-gray-500">Mã giao dịch:</span>
                                        <span class="ml-2 text-sm bg-gray-700/50 px-2 py-0.5 rounded font-mono text-gray-300 border border-gray-600">{{ $order->transaction_id }}</span>
                                    </div>
                                @endif
                            </div>
                        </li>
                        @endif

                        <!-- Xử lý đơn hàng -->
                        @if($order->order_status == 'processing' || $order->order_status == 'shipping' || $order->order_status == 'completed')
                        <li class="relative">
                            <div class="absolute -left-7 mt-1.5 h-5 w-5 rounded-full border-2 border-blue-500 bg-gray-800 flex items-center justify-center shadow-lg shadow-blue-500/20">
                                <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                            </div>
                            <div class="bg-gray-750 p-4 rounded-lg border border-gray-700 hover:border-blue-600 transition-colors duration-300">
                                <time class="mb-1 text-sm font-normal text-blue-400">
                                    {{ $order->updated_at->format('d/m/Y H:i') }}
                                </time>
                                <h3 class="text-lg font-semibold text-blue-400">Đơn hàng đang được xử lý</h3>
                                <p class="text-base font-normal text-gray-400">Chúng tôi đang chuẩn bị phim của bạn.</p>
                            </div>
                        </li>
                        @endif

                        <!-- Giao hàng -->
                        @if($order->order_status == 'shipping' || $order->order_status == 'completed')
                        <li class="relative">
                            <div class="absolute -left-7 mt-1.5 h-5 w-5 rounded-full border-2 border-yellow-500 bg-gray-800 flex items-center justify-center shadow-lg shadow-yellow-500/20">
                                <div class="h-2 w-2 rounded-full bg-yellow-500"></div>
                            </div>
                            <div class="bg-gray-750 p-4 rounded-lg border border-gray-700 hover:border-yellow-600 transition-colors duration-300">
                                <time class="mb-1 text-sm font-normal text-yellow-400">
                                    {{ $order->updated_at->format('d/m/Y H:i') }}
                                </time>
                                <h3 class="text-lg font-semibold text-yellow-400">Đang giao hàng</h3>
                                <p class="text-base font-normal text-gray-400">Đơn hàng đang được giao đến bạn. Vui lòng kiểm tra điện thoại để nhận hàng khi shipper đến.</p>
                            </div>
                        </li>
                        @endif

                        <!-- Hoàn thành -->
                        @if($order->order_status == 'completed')
                        <li class="relative">
                            <div class="absolute -left-7 mt-1.5 h-5 w-5 rounded-full border-2 border-green-500 bg-gray-800 flex items-center justify-center shadow-lg shadow-green-500/20">
                                <div class="h-2 w-2 rounded-full bg-green-500"></div>
                            </div>
                            <div class="bg-gray-750 p-4 rounded-lg border border-gray-700 hover:border-green-600 transition-colors duration-300">
                                <time class="mb-1 text-sm font-normal text-green-400">
                                    {{ $order->updated_at->format('d/m/Y H:i') }}
                                </time>
                                <h3 class="text-lg font-semibold text-green-400">Đơn hàng hoàn thành</h3>
                                <p class="text-base font-normal text-gray-400">Bạn đã nhận được hàng. Cảm ơn bạn đã mua sắm tại TopMovies!</p>

                                <div class="mt-3 bg-green-900/20 border border-green-800/30 p-3 rounded-lg">
                                    <p class="text-sm text-green-400">Mong rằng bạn hài lòng với trải nghiệm mua sắm. Nếu có bất kỳ vấn đề gì, vui lòng liên hệ với chúng tôi.</p>
                                </div>
                            </div>
                        </li>
                        @endif

                        <!-- Hủy đơn -->
                        @if($order->order_status == 'cancelled')
                        <li class="relative">
                            <div class="absolute -left-7 mt-1.5 h-5 w-5 rounded-full border-2 border-red-500 bg-gray-800 flex items-center justify-center shadow-lg shadow-red-500/20">
                                <div class="h-2 w-2 rounded-full bg-red-500"></div>
                            </div>
                            <div class="bg-gray-750 p-4 rounded-lg border border-gray-700 hover:border-red-600 transition-colors duration-300">
                                <time class="mb-1 text-sm font-normal text-red-400">
                                    {{ $order->updated_at->format('d/m/Y H:i') }}
                                </time>
                                <h3 class="text-lg font-semibold text-red-400">Đơn hàng đã bị hủy</h3>
                                <p class="text-base font-normal text-gray-400">Đơn hàng đã bị hủy. Vui lòng liên hệ hỗ trợ nếu bạn có thắc mắc.</p>

                                <div class="mt-3 bg-red-900/20 border border-red-800/30 p-3 rounded-lg">
                                    <p class="text-sm text-red-400">Nếu bạn đã thanh toán cho đơn hàng này, số tiền sẽ được hoàn trả trong vòng 5-7 ngày làm việc.</p>
                                </div>
                            </div>
                        </li>
                        @endif
                    </ol>
                </div>
            </div>
        </div>

        <!-- Các nút thao tác - thiết kế mới -->
        <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700 flex flex-wrap justify-center gap-4">
            <a href="{{ route('orders.index') }}" class="group relative bg-gray-700 hover:bg-gray-600 text-white px-6 py-3 rounded-lg flex items-center transition-all duration-300 hover:shadow-lg overflow-hidden">
                <div class="absolute left-0 top-0 h-full w-0 bg-gradient-to-r from-gray-600 to-gray-500 transition-all duration-300 group-hover:w-full"></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                <span class="relative z-10">Quay lại danh sách đơn hàng</span>
            </a>

            <a href="{{ route('movie.index') }}" class="group relative bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center transition-all duration-300 hover:shadow-lg overflow-hidden">
                <div class="absolute left-0 top-0 h-full w-0 bg-gradient-to-r from-blue-700 to-blue-600 transition-all duration-300 group-hover:w-full"></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="relative z-10">Tiếp tục mua sắm</span>
            </a>

            @if($order->payment_status != 'paid' && $order->payment_method == 'momo' && $order->order_status != 'cancelled')
                @if(isset($order->payment_details['payUrl']))
                    <a href="{{ $order->payment_details['payUrl'] }}" class="group relative bg-pink-600 hover:bg-pink-700 text-white px-6 py-3 rounded-lg flex items-center transition-all duration-300 hover:shadow-lg overflow-hidden">
                        <div class="absolute left-0 top-0 h-full w-0 bg-gradient-to-r from-pink-700 to-pink-600 transition-all duration-300 group-hover:w-full"></div>
                        @if(file_exists(public_path('images/momo-icon.png')))
                            <img src="{{ asset('images/momo-icon.png') }}" alt="MoMo" class="h-5 w-5 mr-2 relative z-10">
                        @endif
                        <span class="relative z-10">Thanh toán qua MoMo</span>
                    </a>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hiệu ứng khi scroll đến những phần tử của timeline
        const timelineItems = document.querySelectorAll('.relative .bg-gray-750');

        // Observer to animate timeline items when they become visible
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fadeIn');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });

            timelineItems.forEach(item => {
                item.classList.add('opacity-0');
                observer.observe(item);
            });
        } else {
            // Fallback for browsers that don't support IntersectionObserver
            timelineItems.forEach(item => {
                item.classList.add('animate-fadeIn');
            });
        }
    });
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fadeIn {
        animation: fadeIn 0.5s ease-out forwards;
    }

    .bg-gray-750 {
        background-color: rgba(55, 65, 81, 0.5);
    }
</style>
@endsection
