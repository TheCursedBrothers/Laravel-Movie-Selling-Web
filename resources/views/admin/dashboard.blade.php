@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Trang tổng quan</h2>
        <div class="mt-4 md:mt-0">
            <span class="text-gray-500">Hôm nay: {{ now()->format('d/m/Y') }}</span>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500 hover:shadow-lg transition duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-gray-500 text-sm uppercase font-medium">Người dùng</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\User::count() }}</p>
                </div>
                <div class="text-blue-500 bg-blue-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-500">
                <a href="{{ route('admin.users.index') }}" class="text-blue-500 hover:text-blue-700 font-medium">Xem tất cả người dùng →</a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500 hover:shadow-lg transition duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-gray-500 text-sm uppercase font-medium">Phim</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Movie::count() }}</p>
                </div>
                <div class="text-green-500 bg-green-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-500">
                <a href="{{ route('admin.movies.index') }}" class="text-green-500 hover:text-green-700 font-medium">Xem tất cả phim →</a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500 hover:shadow-lg transition duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-gray-500 text-sm uppercase font-medium">Đơn hàng</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Order::count() }}</p>
                </div>
                <div class="text-purple-500 bg-purple-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-500">
                <a href="{{ route('admin.orders.index') }}" class="text-purple-500 hover:text-purple-700 font-medium">Xem tất cả đơn hàng →</a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-orange-500 hover:shadow-lg transition duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-gray-500 text-sm uppercase font-medium">Doanh thu</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format(\App\Models\Order::where('payment_status', 'paid')->sum('total'), 0, ',', '.') }}đ</p>
                </div>
                <div class="text-orange-500 bg-orange-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-500">
                <span class="text-orange-500 font-medium">Đã thanh toán thành công</span>
            </div>
        </div>
    </div>

    <!-- Đơn hàng mới nhất và người dùng mới nhất -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Đơn hàng mới nhất -->
        <div class="bg-white rounded-lg shadow lg:col-span-2">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-medium text-gray-800">Đơn hàng mới nhất</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-500 hover:text-blue-700">Xem tất cả</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã đơn hàng</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng tiền</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach(\App\Models\Order::with('user')->latest()->take(5)->get() as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">#{{ $order->id }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($order->order_status == 'completed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Hoàn thành</span>
                                @elseif($order->order_status == 'processing')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Đang xử lý</span>
                                @elseif($order->order_status == 'cancelled')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Đã hủy</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Chờ xử lý</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($order->total, 0, ',', '.') }}đ
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Người dùng mới nhất -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-medium text-gray-800">Người dùng mới nhất</h3>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-500 hover:text-blue-700">Xem tất cả</a>
            </div>
            <div class="p-5">
                <ul class="divide-y divide-gray-200">
                    @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                    <li class="py-3 flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-500 font-medium">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                        <div class="text-right text-xs text-gray-500">
                            {{ $user->created_at->diffForHumans() }}
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Phim bán chạy -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-medium text-gray-800">Top phim bán chạy</h3>
            <a href="{{ route('admin.movies.index') }}" class="text-sm text-blue-500 hover:text-blue-700">Xem tất cả phim</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phim</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đã bán</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Còn lại</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doanh thu</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $topMovies = \App\Models\Movie::select('movies.*', \DB::raw('SUM(order_items.quantity) as total_sold'))
                            ->join('order_items', 'movies.id', '=', 'order_items.movie_id')
                            ->join('orders', function($join) {
                                $join->on('order_items.order_id', '=', 'orders.id')
                                    ->where('orders.payment_status', '=', 'paid');
                            })
                            ->groupBy('movies.id')
                            ->orderByDesc('total_sold')
                            ->take(5)
                            ->get();
                    @endphp

                    @forelse($topMovies as $movie)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($movie->poster_path)
                                    <img class="h-10 w-auto rounded" src="https://image.tmdb.org/t/p/w92{{ $movie->poster_path }}" alt="{{ $movie->title }}">
                                @else
                                    <div class="h-10 w-7 bg-gray-200 rounded"></div>
                                @endif
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $movie->title }}</div>
                                    <div class="text-sm text-gray-500">
                                        @if($movie->release_date)
                                            {{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $movie->total_sold }}</div>
                            <div class="text-xs text-gray-500">bản</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $movie->stock }}</div>
                            <div class="text-xs text-gray-500">bản</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($movie->price, 0, ',', '.') }}đ
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($movie->price * $movie->total_sold, 0, ',', '.') }}đ
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            Không có dữ liệu
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
