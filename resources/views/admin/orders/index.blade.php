@extends('admin.layouts.app')

@section('title', 'Quản lý đơn hàng')

@section('breadcrumb', 'Quản lý đơn hàng')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Danh sách đơn hàng</h2>
        <div class="mt-4 md:mt-0">
            <button id="exportOrdersBtn" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md flex items-center">
                <i class="fas fa-file-export mr-2"></i>
                Xuất Excel
            </button>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-gray-700">Tổng đơn hàng</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $orders->total() }}</p>
            <div class="text-sm text-gray-500 mt-2">Tất cả đơn hàng trong hệ thống</div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-gray-700">Đơn hoàn thành</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $orders->where('order_status', 'completed')->count() }}</p>
            <div class="text-sm text-gray-500 mt-2">Đơn hàng đã giao thành công</div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <h3 class="text-lg font-semibold text-gray-700">Đơn chờ xử lý</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $orders->whereIn('order_status', ['pending', 'processing'])->count() }}</p>
            <div class="text-sm text-gray-500 mt-2">Đơn hàng đang chờ xử lý</div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
            <h3 class="text-lg font-semibold text-gray-700">Tổng doanh thu</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalRevenue, 0, ',', '.') }}đ</p>
            <div class="text-sm text-gray-500 mt-2">Từ đơn hàng đã thanh toán</div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" id="search"
                                   class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Mã đơn, tên, email..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <div>
                        <label for="order_status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái đơn hàng</label>
                        <select name="order_status" id="order_status" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tất cả trạng thái</option>
                            @foreach($orderStatuses as $value => $label)
                                <option value="{{ $value }}" {{ request('order_status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái thanh toán</label>
                        <select name="payment_status" id="payment_status" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tất cả trạng thái</option>
                            @foreach($paymentStatuses as $value => $label)
                                <option value="{{ $value }}" {{ request('payment_status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Phương thức thanh toán</label>
                        <select name="payment_method" id="payment_method" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tất cả phương thức</option>
                            @foreach($paymentMethods as $value => $label)
                                <option value="{{ $value }}" {{ request('payment_method') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                        <input type="date" name="date_from" id="date_from"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               value="{{ request('date_from') }}">
                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                        <input type="date" name="date_to" id="date_to"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               value="{{ request('date_to') }}">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                            <i class="fas fa-filter mr-2"></i>Lọc
                        </button>

                        @if(request()->anyFilled(['search', 'order_status', 'payment_status', 'payment_method', 'date_from', 'date_to']))
                            <a href="{{ route('admin.orders.index') }}" class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                                Xóa bộ lọc
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mã ĐH
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Khách hàng
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ngày đặt
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tổng tiền
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái đơn hàng
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thanh toán
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $order->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->phone }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            {{ number_format($order->total, 0, ',', '.') }}đ
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $order->order_status == 'completed' ? 'bg-green-100 text-green-800' :
                                   ($order->order_status == 'processing' ? 'bg-blue-100 text-blue-800' :
                                   ($order->order_status == 'shipping' ? 'bg-yellow-100 text-yellow-800' :
                                   ($order->order_status == 'cancelled' ? 'bg-red-100 text-red-800' :
                                   'bg-gray-100 text-gray-800'))) }}">
                                {{ $orderStatuses[$order->order_status] ?? $order->order_status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full mb-1
                                    {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-800' :
                                       ($order->payment_status == 'processing' ? 'bg-blue-100 text-blue-800' :
                                       ($order->payment_status == 'failed' ? 'bg-red-100 text-red-800' :
                                       'bg-yellow-100 text-yellow-800')) }}">
                                    {{ $paymentStatuses[$order->payment_status] ?? $order->payment_status }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    @if($order->payment_method == 'momo')
                                        <i class="fas fa-wallet text-pink-500 mr-1"></i> MoMo
                                    @elseif($order->payment_method == 'cod')
                                        <i class="fas fa-money-bill text-green-500 mr-1"></i> Tiền mặt
                                    @elseif($order->payment_method == 'bank_transfer')
                                        <i class="fas fa-university text-blue-500 mr-1"></i> Chuyển khoản
                                    @else
                                        {{ $order->payment_method }}
                                    @endif
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900 mr-2" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="{{ route('admin.orders.edit', $order) }}" class="text-blue-600 hover:text-blue-900 mr-2" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')"
                                        title="Xóa đơn hàng">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 whitespace-nowrap">
                            Không tìm thấy đơn hàng nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200">
            {{ $orders->appends(request()->except('page'))->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý xuất Excel
        document.getElementById('exportOrdersBtn').addEventListener('click', function() {
            let url = '{{ route("admin.orders.export") }}';

            // Thêm các tham số lọc vào URL xuất Excel
            const searchParams = new URLSearchParams(window.location.search);
            if (searchParams.toString()) {
                url += '?' + searchParams.toString();
            }

            window.location.href = url;
        });
    });
</script>
@endsection
