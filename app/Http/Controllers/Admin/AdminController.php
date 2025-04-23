<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Hiển thị trang dashboard admin
     */
    public function index()
    {
        // Thống kê tổng quan
        $stats = [
            'total_users' => User::count(),
            'total_movies' => Movie::count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'recent_orders' => Order::with('user')->latest()->take(5)->get(),
            'recent_users' => User::latest()->take(5)->get(),
        ];

        // Thống kê theo tháng trong năm hiện tại
        $currentYear = Carbon::now()->year;
        $monthlyStats = [];

        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::createFromDate($currentYear, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($currentYear, $month, 1)->endOfMonth();

            $completedOrders = Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $revenue = Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total');

            $monthlyStats[] = [
                'month' => $startDate->format('M'),
                'completed_orders' => $completedOrders,
                'revenue' => $revenue,
            ];
        }

        // Thống kê đơn hàng theo trạng thái
        $orderStatusStats = [
            'pending' => Order::where('order_status', 'pending')->count(),
            'processing' => Order::where('order_status', 'processing')->count(),
            'completed' => Order::where('order_status', 'completed')->count(),
            'cancelled' => Order::where('order_status', 'cancelled')->count(),
        ];

        // Thống kê đơn hàng theo phương thức thanh toán
        $paymentMethodStats = Order::select('payment_method', DB::raw('count(*) as count'))
            ->groupBy('payment_method')
            ->pluck('count', 'payment_method')
            ->toArray();

        // Phim bán chạy nhất
        $topSellingMovies = Movie::select('movies.*', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items', 'movies.id', '=', 'order_items.movie_id')
            ->join('orders', function ($join) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.payment_status', '=', 'paid');
            })
            ->groupBy('movies.id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'monthlyStats',
            'orderStatusStats',
            'paymentMethodStats',
            'topSellingMovies'
        ));
    }

    /**
     * Hiển thị danh sách người dùng
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Tìm kiếm theo tên hoặc email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái admin
        if ($request->has('is_admin') && $request->is_admin != '') {
            $query->where('is_admin', $request->is_admin);
        }

        $users = $query->latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Hiển thị form tạo người dùng mới
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Lưu người dùng mới vào database
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_admin' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->is_admin ? true : false,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Người dùng đã được tạo thành công.');
    }

    /**
     * Hiển thị chi tiết người dùng
     */
    public function showUser(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Hiển thị form chỉnh sửa người dùng
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin người dùng
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'is_admin' => ['boolean'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->is_admin ? true : false,
        ];

        // Cập nhật mật khẩu nếu có
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Thông tin người dùng đã được cập nhật.');
    }

    /**
     * Xóa người dùng
     */
    public function destroyUser(User $user)
    {
        // Không cho phép xóa chính mình
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể xóa tài khoản của chính mình.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Người dùng đã được xóa thành công.');
    }

    /**
     * Chuyển đổi trạng thái admin của người dùng
     */
    public function toggleAdmin(User $user)
    {
        // Ngăn chặn việc loại bỏ quyền admin của chính mình
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể thay đổi trạng thái admin của chính mình.');
        }

        $user->update([
            'is_admin' => !$user->is_admin
        ]);

        $status = $user->is_admin ? 'cấp' : 'gỡ bỏ';
        return back()->with('success', "Đã {$status} quyền admin cho người dùng.");
    }

    /**
     * Hiển thị danh sách đơn hàng
     */
    public function orders(Request $request)
    {
        $query = Order::with('user');

        // Tìm kiếm theo ID, tên hoặc email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái đơn hàng
        if ($request->has('order_status') && $request->order_status != '') {
            $query->where('order_status', $request->order_status);
        }

        // Lọc theo trạng thái thanh toán
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->paginate(10);

        // Tính tổng doanh thu từ các đơn hàng đã thanh toán
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        
        // Thêm danh sách trạng thái đơn hàng để hiển thị trong dropdown filter
        $orderStatuses = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy'
        ];
        
        // Thêm danh sách trạng thái thanh toán để hiển thị trong dropdown filter
        $paymentStatuses = [
            'pending' => 'Chờ thanh toán',
            'processing' => 'Đang xử lý',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thất bại',
            'refunded' => 'Đã hoàn tiền'
        ];

        return view('admin.orders.index', compact('orders', 'totalRevenue', 'orderStatuses', 'paymentStatuses'));
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function showOrder(Order $order)
    {
        $order->load('items.movie', 'user');
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Hiển thị form chỉnh sửa đơn hàng
     */
    public function editOrder(Order $order)
    {
        $order->load('items.movie', 'user');
        
        // Add these arrays for the edit view
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

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => ['required', 'in:pending,processing,shipping,completed,cancelled'],
            'payment_status' => ['required', 'in:pending,processing,paid,failed'],
        ]);

        $order->update([
            'order_status' => $request->order_status,
            'payment_status' => $request->payment_status,
        ]);

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng thành công.');
    }

    /**
     * Xóa đơn hàng
     */
    public function destroyOrder(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')
            ->with('success', 'Đơn hàng đã được xóa thành công.');
    }
}
