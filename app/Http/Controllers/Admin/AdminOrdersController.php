<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminOrdersController extends Controller
{
    // Helper method để kiểm tra quyền admin
    private function checkAdmin()
    {
        if (!Auth::user() || !Auth::user()->is_admin) {
            abort(403, 'Bạn không có quyền truy cập trang này');
        }
    }

    public function index(Request $request)
    {
        // Kiểm tra quyền admin
        $this->checkAdmin();

        $query = Order::with('user');

        // Tìm kiếm theo mã đơn hàng hoặc tên/email khách hàng
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%")
                    ->orWhere('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Lọc theo trạng thái đơn hàng
        if ($request->has('status') && $request->status) {
            $query->where('order_status', $request->status);
        }

        // Lọc theo trạng thái thanh toán
        if ($request->has('payment') && $request->payment) {
            $query->where('payment_status', $request->payment);
        }

        // Lọc theo phương thức thanh toán
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // Lọc theo khoảng thời gian
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(15);

        // Tính tổng doanh thu từ tất cả các đơn hàng đã thanh toán (không bị ảnh hưởng bởi bộ lọc)
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');

        // Định nghĩa biến $orderStatuses để hiển thị trong dropdown filter
        $orderStatuses = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy'
        ];

        // Định nghĩa biến $paymentStatuses để hiển thị trong dropdown filter
        $paymentStatuses = [
            'pending' => 'Chờ thanh toán',
            'processing' => 'Đang xử lý',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thất bại',
            'refunded' => 'Đã hoàn tiền'
        ];

        // Định nghĩa biến $paymentMethods để hiển thị trong dropdown filter
        $paymentMethods = [
            'momo' => 'Ví MoMo',
            'cod' => 'Thanh toán khi nhận hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'credit_card' => 'Thẻ tín dụng/ghi nợ'
        ];

        return view('admin.orders.index', compact(
            'orders',
            'orderStatuses',
            'paymentStatuses',
            'paymentMethods',
            'totalRevenue'
        ));
    }

    public function show(Order $order)
    {
        // Kiểm tra quyền admin
        $this->checkAdmin();

        $order->load('items.movie', 'user');

        // Add these arrays for the show view as well
        $orderStatuses = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy'
        ];

        $paymentStatuses = [
            'pending' => 'Chờ thanh toán',
            'processing' => 'Đang xử lý',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thất bại',
            'refunded' => 'Đã hoàn tiền'
        ];

        $paymentMethods = [
            'momo' => 'Ví MoMo',
            'cod' => 'Thanh toán khi nhận hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'credit_card' => 'Thẻ tín dụng/ghi nợ'
        ];

        return view('admin.orders.show', compact('order', 'orderStatuses', 'paymentStatuses', 'paymentMethods'));
    }

    public function edit(Order $order)
    {
        // Kiểm tra quyền admin
        $this->checkAdmin();

        $order->load('items.movie', 'user');

        // Add these arrays for the edit view as well
        $orderStatuses = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy'
        ];

        $paymentStatuses = [
            'pending' => 'Chờ thanh toán',
            'processing' => 'Đang xử lý',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thất bại',
            'refunded' => 'Đã hoàn tiền'
        ];

        $paymentMethods = [
            'momo' => 'Ví MoMo',
            'cod' => 'Thanh toán khi nhận hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'credit_card' => 'Thẻ tín dụng/ghi nợ'
        ];

        return view('admin.orders.edit', compact('order', 'orderStatuses', 'paymentStatuses', 'paymentMethods'));
    }

    public function update(Request $request, Order $order)
    {
        // Kiểm tra quyền admin
        $this->checkAdmin();

        $request->validate([
            'order_status' => 'required|in:pending,processing,completed,cancelled',
            'payment_status' => 'required|in:pending,processing,paid,failed,refunded',
        ]);

        try {
            $order->update([
                'order_status' => $request->order_status,
                'payment_status' => $request->payment_status,
            ]);

            // Nếu có ghi chú admin, thêm vào payment_details
            if ($request->filled('admin_note')) {
                $paymentDetails = $order->payment_details ?? [];
                $paymentDetails['admin_notes'] = array_merge(
                    $paymentDetails['admin_notes'] ?? [],
                    [[
                        'note' => $request->admin_note,
                        'time' => now()->toDateTimeString(),
                        'admin' => auth()->user()->name
                    ]]
                );

                $order->update(['payment_details' => $paymentDetails]);
            }

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Đơn hàng đã được cập nhật thành công');
        } catch (\Exception $e) {
            Log::error('Error updating order: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Order $order)
    {
        // Kiểm tra quyền admin
        $this->checkAdmin();

        try {
            $order->delete();
            return redirect()->route('admin.orders.index')
                ->with('success', 'Đơn hàng đã được xóa thành công');
        } catch (\Exception $e) {
            Log::error('Error deleting order: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function addNote(Request $request, Order $order)
    {
        // Kiểm tra quyền admin
        $this->checkAdmin();

        $request->validate([
            'note' => 'required|string|max:500',
        ]);

        try {
            $paymentDetails = $order->payment_details ?? [];
            $paymentDetails['admin_notes'] = array_merge(
                $paymentDetails['admin_notes'] ?? [],
                [[
                    'note' => $request->note,
                    'time' => now()->toDateTimeString(),
                    'admin' => auth()->user()->name
                ]]
            );

            $order->update(['payment_details' => $paymentDetails]);

            return back()->with('success', 'Đã thêm ghi chú vào đơn hàng');
        } catch (\Exception $e) {
            Log::error('Error adding note to order: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xuất danh sách đơn hàng ra Excel
     */
    public function export(Request $request)
    {
        // Kiểm tra quyền admin
        $this->checkAdmin();

        $query = Order::with('user');

        // Áp dụng các bộ lọc giống như trong index
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%")
                    ->orWhere('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('order_status', $request->status);
        }

        if ($request->has('payment') && $request->payment) {
            $query->where('payment_status', $request->payment);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->get();

        // Tạo tên file Excel
        $fileName = 'don_hang_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Tạo và trả về file Excel (bạn cần thêm thư viện Excel như Laravel Excel)
        // Đoạn code này cần thêm thư viện Laravel Excel
        // Ví dụ: return Excel::download(new OrdersExport($orders), $fileName);

        // Tạm thời trả về thông báo
        return back()->with('info', 'Tính năng xuất Excel đang được phát triển. Vui lòng thử lại sau.');
    }
}
