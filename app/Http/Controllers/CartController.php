<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        // Kiểm tra người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để xem giỏ hàng của bạn.');
        }

        $cart = $this->cartService->getCart();
        return view('cart', compact('cart'));
    }

    public function addToCart(Request $request)
    {
        // Xác thực request
        $request->validate([
            'tmdbId' => 'required|integer',
        ]);

        // Kiểm tra người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return response()->json(['message' => 'Vui lòng đăng nhập để thêm phim vào giỏ hàng.'], 401);
        }

        try {
            // Thêm phim vào giỏ hàng
            $cartItem = $this->cartService->addMovie($request->tmdbId);

            if ($cartItem) {
                // Phản hồi thành công với dữ liệu
                return response()->json([
                    'success' => true,
                    'message' => 'Đã thêm phim vào giỏ hàng',
                    'cart_count' => $this->cartService->count(),
                    'item' => $cartItem
                ]);
            } else {
                // Phản hồi lỗi
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể thêm phim vào giỏ hàng'
                ], 400);
            }
        } catch (\Exception $e) {
            // Xử lý ngoại lệ
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeItem($movieId)
    {
        // Kiểm tra người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để quản lý giỏ hàng.');
        }

        $this->cartService->removeMovie($movieId);
        return back()->with('success', 'Đã xóa phim khỏi giỏ hàng');
    }

    public function updateQuantity(Request $request, $itemId)
    {
        // Kiểm tra người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để quản lý giỏ hàng.');
        }

        $quantity = $request->input('quantity');
        $this->cartService->updateQuantity($itemId, $quantity);
        return back()->with('success', 'Đã cập nhật số lượng');
    }

    public function clear()
    {
        // Kiểm tra người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để quản lý giỏ hàng.');
        }

        $this->cartService->clear();
        return back()->with('success', 'Đã xóa giỏ hàng');
    }
}
