<x-app-layout>
    <div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-white">
                    {{ __('Quản lý tài khoản') }}
                </h2>
                <p class="mt-1 text-sm text-gray-400">
                    {{ __('Quản lý thông tin, mật khẩu và các dữ liệu khác của tài khoản') }}
                </p>
            </div>

            <div class="flex flex-col md:flex-row gap-8">
                <!-- Sidebar Navigation -->
                <div class="md:w-1/4 lg:w-1/5">
                    <div class="bg-gray-800 rounded-lg shadow-md border border-gray-700 sticky top-6">
                        <div class="p-4 border-b border-gray-700">
                            <div class="flex items-center">
                                <img src="{{ asset('images/Avatar/avatar.jpg') }}" alt="{{ Auth::user()->name }}" class="w-12 h-12 rounded-full mr-3">
                                <div>
                                    <h3 class="font-medium text-white">{{ Auth::user()->name }}</h3>
                                    <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>

                        <nav class="p-2">
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('profile.edit') }}"
                                       class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('profile.edit') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Thông tin tài khoản
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('profile.password') }}"
                                       class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('profile.password') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                        </svg>
                                        Đổi mật khẩu
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('orders.index') }}"
                                       class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('orders.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                        Đơn hàng của tôi
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('movies.favorites') }}"
                                       class="flex items-center px-4 py-2.5 rounded-md {{ request()->routeIs('movies.favorites') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                        Phim yêu thích
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                                        @csrf
                                        <button type="submit"
                                           class="flex w-full items-center px-4 py-2.5 rounded-md text-gray-300 hover:bg-red-700 hover:text-white">
                                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="md:w-3/4 lg:w-4/5">
                    <!-- Orders Section -->
                    <div class="bg-gray-800 p-6 rounded-lg shadow-md border border-gray-700">
                        <div class="flex justify-between items-center mb-6">
                            <div class="text-xl font-semibold text-white">{{ __('Đơn hàng của tôi') }}</div>

                            <a href="{{ route('movie.index') }}" class="text-blue-400 hover:text-blue-300 text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Mua thêm phim
                            </a>
                        </div>

                        @if(count($orders) > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 bg-gray-700 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                                Mã đơn hàng
                                            </th>
                                            <th class="px-6 py-3 bg-gray-700 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                                Ngày đặt
                                            </th>
                                            <th class="px-6 py-3 bg-gray-700 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                                Tổng tiền
                                            </th>
                                            <th class="px-6 py-3 bg-gray-700 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                                Trạng thái
                                            </th>
                                            <th class="px-6 py-3 bg-gray-700 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                                                Thao tác
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-gray-750 divide-y divide-gray-700">
                                        @foreach($orders as $order)
                                            <tr class="hover:bg-gray-700 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-white">#{{ $order->id }}</div>
                                                    <div class="text-xs text-gray-400">{{ $order->transaction_id ?? 'N/A' }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-300">
                                                        {{ $order->created_at->format('d/m/Y') }}
                                                    </div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $order->created_at->format('H:i') }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-orange-500">
                                                        {{ number_format($order->total, 0, ',', '.') }}đ
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                                        @if($order->payment_status == 'paid') bg-green-900 text-green-300
                                                        @elseif($order->payment_status == 'processing') bg-blue-900 text-blue-300
                                                        @elseif($order->payment_status == 'failed') bg-red-900 text-red-300
                                                        @else bg-gray-700 text-gray-300 @endif">
                                                        {{ $order->paymentStatusText() }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="{{ route('orders.show', $order->id) }}" class="text-blue-400 hover:text-blue-300">
                                                        Xem chi tiết
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-6">
                                {{ $orders->links() }}
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-400 mb-2">Bạn chưa có đơn hàng nào</h3>
                                <p class="text-gray-500 mb-6">Hãy mua sắm để bắt đầu trải nghiệm dịch vụ của chúng tôi</p>
                                <a href="{{ route('movie.index') }}" class="bg-orange-500 hover:bg-orange-600 text-gray-900 font-medium py-2 px-4 rounded-lg transition">
                                    Mua sắm ngay
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
