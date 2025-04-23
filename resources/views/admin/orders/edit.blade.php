@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa đơn hàng')

@section('breadcrumb')
    <a href="{{ route('admin.orders.index') }}">Quản lý đơn hàng</a> /
    <a href="{{ route('admin.orders.show', $order) }}">Chi tiết đơn hàng #{{ $order->id }}</a> /
    Chỉnh sửa đơn hàng
@endsection

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Chỉnh sửa đơn hàng #{{ $order->id }}</h2>
            <div class="flex space-x-3">
                <a href="{{ route('admin.orders.show', $order) }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md transition focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Quay lại
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Thông tin đơn hàng -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-blue-50 border-b border-blue-100">
                        <h3 class="text-lg font-medium text-blue-800">
                            <i class="fas fa-edit mr-2"></i>Chỉnh sửa thông tin đơn hàng
                        </h3>
                    </div>

                    <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="p-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="order_status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái đơn hàng *</label>
                                <select name="order_status" id="order_status" required
                                    class="block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                                    @foreach($orderStatuses as $value => $label)
                                        <option value="{{ $value }}" {{ $order->order_status === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('order_status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái thanh toán *</label>
                                <select name="payment_status" id="payment_status" required
                                    class="block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                                    @foreach($paymentStatuses as $value => $label)
                                        <option value="{{ $value }}" {{ $order->payment_status === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('payment_status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-2">
                                <label for="admin_note" class="block text-sm font-medium text-gray-700 mb-1">Ghi chú của admin</label>
                                <textarea name="admin_note" id="admin_note" rows="3"
                                    class="block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Nhập ghi chú về thay đổi này..."></textarea>
                                <p class="mt-1 text-xs text-gray-500">Ghi chú này sẽ được lưu vào lịch sử thay đổi đơn hàng</p>
                            </div>
                        </div>

                        <div class="pt-4 flex items-center justify-end border-t border-gray-200">
                            <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-save mr-2"></i>Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Danh sách sản phẩm (chỉ hiển thị) -->
                <div class="mt-6 bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-green-50 border-b border-green-100">
                        <h3 class="text-lg font-medium text-green-800">
                            <i class="fas fa-film mr-2"></i>Chi tiết phim
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Phim
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Số lượng
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Đơn giá
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Thành tiền
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($order->items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if ($item->movie->poster_path)
                                                    <img src="https://image.tmdb.org/t/p/w92{{ $item->movie->poster_path }}"
                                                        alt="{{ $item->movie->title }}" class="w-10 h-auto rounded mr-3">
                                                @else
                                                    <div
                                                        class="w-10 h-15 bg-gray-200 rounded mr-3 flex items-center justify-center">
                                                        <span class="text-xs text-gray-500">No image</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $item->movie->title }}</div>
                                                    <div class="text-xs text-gray-500">ID: {{ $item->movie->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                            {{ number_format($item->price, 0, ',', '.') }}đ
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                            {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50">
                                    <td colspan="3" class="px-6 py-4 text-right font-medium text-gray-700">Tổng cộng:
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-green-600">
                                        {{ number_format($order->total, 0, ',', '.') }}đ</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Thông tin khách hàng -->
            <div>
                <!-- Thông tin khách hàng -->
                <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
                    <div class="px-6 py-4 bg-indigo-50 border-b border-indigo-100">
                        <h3 class="text-lg font-medium text-indigo-800">
                            <i class="fas fa-user mr-2"></i>Thông tin khách hàng
                        </h3>
                    </div>

                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                <i class="fas fa-user text-indigo-500"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">{{ $order->name }}</h4>
                                @if ($order->user)
                                    <p class="text-sm text-gray-500">ID: {{ $order->user->id }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center">
                                <div class="text-indigo-500 mr-3">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 uppercase tracking-wide">Email:</label>
                                    <p class="text-gray-900">{{ $order->email }}</p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <div class="text-indigo-500 mr-3">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 uppercase tracking-wide">Số điện thoại:</label>
                                    <p class="text-gray-900">{{ $order->phone }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="text-indigo-500 mr-3 mt-1">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 uppercase tracking-wide">Địa chỉ:</label>
                                    <p class="text-gray-900">{{ $order->address }}</p>
                                </div>
                            </div>
                        </div>

                        @if ($order->user)
                            <div class="mt-6 border-t border-gray-200 pt-4">
                                <a href="{{ route('admin.users.show', $order->user) }}"
                                    class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    Xem hồ sơ người dùng
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                @if(isset($order->payment_details['admin_notes']) && is_array($order->payment_details['admin_notes']) && count($order->payment_details['admin_notes']) > 0)
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-yellow-50 border-b border-yellow-100">
                        <h3 class="text-lg font-medium text-yellow-800">
                            <i class="fas fa-history mr-2"></i>Lịch sử ghi chú
                        </h3>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach ($order->payment_details['admin_notes'] as $note)
                                <div class="p-3 bg-gray-50 rounded-md border border-gray-200">
                                    <p class="text-gray-800">{{ $note['note'] }}</p>
                                    <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
                                        <span>{{ $note['admin'] }}</span>
                                        <span>{{ \Carbon\Carbon::parse($note['time'])->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
