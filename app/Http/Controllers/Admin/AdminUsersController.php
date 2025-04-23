<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminUsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Tìm kiếm theo tên hoặc email
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Lọc theo quyền admin
        if ($request->has('filter') && in_array($request->filter, ['admin', 'user'])) {
            $isAdmin = $request->filter === 'admin';
            $query->where('is_admin', $isAdmin);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Rules\Password::defaults()],
            'is_admin' => 'boolean',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_admin' => $request->has('is_admin'),
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'Người dùng đã được tạo thành công');
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(User $user)
    {
        // Lấy thống kê về người dùng
        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'completed_orders' => Order::where('user_id', $user->id)
                ->where('order_status', 'completed')->count(),
            'total_spent' => Order::where('user_id', $user->id)
                ->where('payment_status', 'paid')
                ->sum('total'),
            'favorite_movies' => DB::table('favorite_movies')
                ->where('user_id', $user->id)->count(),
        ];

        // Kèm thông tin về đơn hàng của user
        $user->load(['orders' => function ($query) {
            $query->latest()->take(5);
        }]);

        return view('admin.users.show', compact('user', 'stats'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'is_admin' => 'boolean',
        ]);

        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'is_admin' => $request->has('is_admin'),
            ];

            // Cập nhật mật khẩu nếu có
            if ($request->filled('password')) {
                $request->validate([
                    'password' => ['required', Rules\Password::defaults()],
                ]);
                $userData['password'] = Hash::make($request->password);
            }

            // Ngăn chặn việc hủy quyền admin của bản thân
            if (auth()->id() == $user->id && $user->is_admin && !$request->has('is_admin')) {
                return back()->with('error', 'Bạn không thể hủy quyền admin của chính mình');
            }

            $user->update($userData);

            return redirect()->route('admin.users.index')
                ->with('success', 'Thông tin người dùng đã được cập nhật');
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(User $user)
    {
        // Ngăn chặn xóa tài khoản đang đăng nhập
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể xóa tài khoản của chính mình');
        }

        try {
            $user->delete();
            return redirect()->route('admin.users.index')
                ->with('success', 'Đã xóa người dùng thành công');
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function toggleAdmin(User $user)
    {
        // Ngăn chặn hủy quyền admin của chính mình
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể thay đổi quyền admin của chính mình');
        }

        try {
            $user->update([
                'is_admin' => !$user->is_admin
            ]);

            $message = $user->is_admin ?
                'Đã cấp quyền admin cho ' . $user->name :
                'Đã hủy quyền admin của ' . $user->name;

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error toggling admin: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
