@extends('admin.layouts.app')

@section('title', 'Chi tiết người dùng')

@section('breadcrumb')
    <a href="{{ route('admin.users.index') }}">Quản lý người dùng</a> / Chi tiết người dùng
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Thông tin người dùng: {{ $user->name }}</h2>
        <div class="flex space-x-3">
            <a href="{{ route('admin.users.edit', $user->id) }}"
               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i>
                Sửa
            </a>
            @if(auth()->id() != $user->id)
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i>
                        Xóa
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Thông tin cơ bản -->
        <div class="md:col-span-3 bg-white shadow overflow-hidden rounded-lg">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                <h3 class="text-lg font-medium text-gray-900">Thông tin cơ bản</h3>
            </div>

            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="flex justify-between py-3 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-500">ID:</span>
                            <span class="text-sm text-gray-900">{{ $user->id }}</span>
                        </div>
                        <div class="flex justify-between py-3 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-500">Tên:</span>
                            <span class="text-sm text-gray-900">{{ $user->name }}</span>
                        </div>
                        <div class="flex justify-between py-3 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-500">Email:</span>
                            <span class="text-sm text-gray-900">{{ $user->email }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between py-3 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-500">Vai trò:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_admin ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $user->is_admin ? 'Admin' : 'User' }}
                            </span>
                        </div>
                        <div class="flex justify-between py-3 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-500">Ngày tạo:</span>
                            <span class="text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="flex justify-between py-3 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-500">Cập nhật lần cuối:</span>
                            <span class="text-sm text-gray-900">{{ $user->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê người dùng -->
        <div class="bg-white shadow overflow-hidden rounded-lg">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                <h3 class="text-lg font-medium text-gray-900">Thống kê</h3>
            </div>

            <div class="p-6 space-y-4">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100 flex flex-col items-center">
                    <span class="text-sm text-blue-800">Tổng đơn hàng</span>
                    <span class="text-3xl font-bold text-blue-600">{{ $stats['total_orders'] }}</span>
                </div>

                <div class="bg-green-50 rounded-lg p-4 border border-green-100 flex flex-col items-center">
                    <span class="text-sm text-green-800">Đơn hàng hoàn thành</span>
                    <span class="text-3xl font-bold text-green-600">{{ $stats['completed_orders'] }}</span>
                </div>

                <div class="bg-purple-50 rounded-lg p-4 border border-purple-100 flex flex-col items-center">
                    <span class="text-sm text-purple-800">Phim yêu thích</span>
                    <span class="text-3xl font-bold text-purple-600">{{ $stats['favorite_movies'] }}</span>
                </div>

                <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100 flex flex-col items-center">
                    <span class="text-sm text-yellow-800">Tổng chi tiêu</span>
                    <span class="text-3xl font-bold text-yellow-600">{{ number_format($stats['total_spent'], 0, ',', '.') }}đ</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Đơn hàng gần đây -->
    <div class="bg-white shadow overflow-hidden rounded-lg">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Đơn hàng gần đây</h3>
            <a href="#" class="text-sm text-blue-500 hover:underline">Xem tất cả</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mã đơn hàng
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ngày đặt
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái đơn hàng
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái thanh toán
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tổng tiền
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            #{{ $order->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $order->order_status == 'completed' ? 'bg-green-100 text-green-800' :
                                  ($order->order_status == 'processing' ? 'bg-blue-100 text-blue-800' :
                                  ($order->order_status == 'cancelled' ? 'bg-red-100 text-red-800' :
                                  'bg-yellow-100 text-yellow-800')) }}">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-800' :
                                  ($order->payment_status == 'processing' ? 'bg-yellow-100 text-yellow-800' :
                                  ($order->payment_status == 'failed' ? 'bg-red-100 text-red-800' :
                                  'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                            {{ number_format($order->total, 0, ',', '.') }}đ
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900">
                                Xem chi tiết
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            Người dùng chưa có đơn hàng nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
