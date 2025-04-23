@extends('admin.layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('breadcrumb')
    <a href="{{ route('admin.orders.index') }}">Quản lý đơn hàng</a> / Chi tiết đơn hàng #{{ $order->id }}
@endsection

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Chi tiết đơn hàng #{{ $order->id }}</h2>
            <div class="flex space-x-3">
                <a href="{{ route('admin.orders.edit', $order) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-edit mr-2"></i>Sửa đơn hàng
                </a>
                <a href="{{ route('admin.orders.index') }}"
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
                    <div class="px-6 py-4 bg-blue-50 border-b border-blue-100 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>Thông tin đơn hàng
                        </h3>
                        <div>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium
                            {{ $order->order_status == 'completed'
                                ? 'bg-green-100 text-green-800'
                                : ($order->order_status == 'processing'
                                    ? 'bg-blue-100 text-blue-800'
                                    : ($order->order_status == 'shipping'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : ($order->order_status == 'cancelled'
                                            ? 'bg-red-100 text-red-800'
                                            : 'bg-gray-100 text-gray-800'))) }}">
                                {{ $orderStatuses[$order->order_status] ?? $order->order_status }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500 font-medium mb-1">Mã đơn hàng:</p>
                                <p class="text-gray-900 font-semibold">#{{ $order->id }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 font-medium mb-1">Ngày đặt hàng:</p>
                                <p class="text-gray-900">{{ $order->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 font-medium mb-1">Trạng thái thanh toán:</p>
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $order->payment_status == 'paid'
                                    ? 'bg-green-100 text-green-800'
                                    : ($order->payment_status == 'processing'
                                        ? 'bg-blue-100 text-blue-800'
                                        : ($order->payment_status == 'failed'
                                            ? 'bg-red-100 text-red-800'
                                            : 'bg-yellow-100 text-yellow-800')) }}">
                                    {{ $paymentStatuses[$order->payment_status] ?? $order->payment_status }}
                                </span>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 font-medium mb-1">Phương thức thanh toán:</p>
                                <p class="text-gray-900">
                                    @if ($order->payment_method == 'momo')
                                        <span class="flex items-center">
                                            <img src="{{ asset('images/momo-icon.png') }}" alt="MoMo"
                                                class="h-5 w-5 mr-2">
                                            Ví MoMo
                                        </span>
                                    @elseif($order->payment_method == 'cod')
                                        <span class="flex items-center">
                                            <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                                            Thanh toán khi nhận hàng
                                        </span>
                                    @elseif($order->payment_method == 'bank_transfer')
                                        <span class="flex items-center">
                                            <i class="fas fa-university text-blue-600 mr-2"></i>
                                            Chuyển khoản ngân hàng
                                        </span>
                                    @else
                                        {{ $paymentMethods[$order->payment_method] ?? $order->payment_method }}
                                    @endif
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 font-medium mb-1">Tổng tiền:</p>
                                <p class="text-lg font-bold text-blue-600">{{ number_format($order->total, 0, ',', '.') }}đ
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 font-medium mb-1">Thời gian cập nhật cuối:</p>
                                <p class="text-gray-900">{{ $order->updated_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>

                        @if ($order->transaction_id)
                            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                <h4 class="font-medium text-gray-700 mb-2">Thông tin giao dịch:</h4>
                                <p class="text-sm text-gray-600">Mã giao dịch: <span
                                        class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $order->transaction_id }}</span>
                                </p>

                                @if (isset($order->payment_details) && is_array($order->payment_details))
                                    @foreach ($order->payment_details as $key => $value)
                                        @if (!is_array($value) && !is_object($value) && $key != 'admin_notes')
                                            <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}:
                                                {{ $value }}</p>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        @endif

                        @if ($order->note)
                            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                                <h4 class="font-medium text-blue-700 mb-1">Ghi chú của khách hàng:</h4>
                                <p class="text-blue-600">{{ $order->note }}</p>
                            </div>
                        @endif

                        @if (isset($order->payment_details['admin_notes']) &&
                                is_array($order->payment_details['admin_notes']) &&
                                count($order->payment_details['admin_notes']) > 0)
                            <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                                <h4 class="font-medium text-yellow-700 mb-2">Ghi chú của Admin:</h4>
                                <div class="space-y-2">
                                    @foreach ($order->payment_details['admin_notes'] as $note)
                                        <div class="p-3 bg-white rounded-md border border-yellow-200">
                                            <p class="text-gray-800">{{ $note['note'] }}</p>
                                            <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
                                                <span>{{ $note['admin'] }}</span>
                                                <span>{{ \Carbon\Carbon::parse($note['time'])->format('d/m/Y H:i') }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Danh sách sản phẩm -->
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
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
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

            <!-- Sidebar: Thông tin khách hàng và cập nhật trạng thái -->
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
                            <div
                                class="flex-shrink-0 h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
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
                                    <label class="block text-xs text-gray-500 uppercase tracking-wide">Số điện
                                        thoại:</label>
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

                <!-- Cập nhật trạng thái -->
                <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
                    <div class="px-6 py-4 bg-pink-50 border-b border-pink-100">
                        <h3 class="text-lg font-medium text-pink-800">
                            <i class="fas fa-edit mr-2"></i>Cập nhật trạng thái
                        </h3>
                    </div>

                    <div class="p-6">
                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="order_status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái
                                    đơn hàng</label>
                                <select name="order_status" id="order_status"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach ($orderStatuses as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ $order->order_status == $value ? 'selected' : '' }}>{{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-6">
                                <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Trạng
                                    thái thanh toán</label>
                                <select name="payment_status" id="payment_status"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach ($paymentStatuses as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ $order->payment_status == $value ? 'selected' : '' }}>{{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                Cập nhật trạng thái
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Thêm ghi chú -->
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-yellow-50 border-b border-yellow-100">
                        <h3 class="text-lg font-medium text-yellow-800">
                            <i class="fas fa-sticky-note mr-2"></i>Thêm ghi chú
                        </h3>
                    </div>

                    <div class="p-6">
                        <form action="{{ route('admin.orders.add-note', $order) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="note" class="block text-sm font-medium text-gray-700 mb-1">Ghi chú
                                    mới</label>
                                <textarea name="note" id="note" rows="3"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                                    placeholder="Nhập ghi chú của bạn về đơn hàng này..."></textarea>
                            </div>

                            <button type="submit"
                                class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition">
                                <i class="fas fa-plus mr-2"></i>Thêm ghi chú
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
