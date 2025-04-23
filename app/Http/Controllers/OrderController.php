<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\MomoPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $cartService;
    protected $momoService;

    public function __construct(CartService $cartService, MomoPaymentService $momoService)
    {
        $this->cartService = $cartService;
        $this->momoService = $momoService;
    }

    /**
     * Hiển thị trang thanh toán
     */
    public function checkout()
    {
        $cart = $this->cartService->getCart();

        // Kiểm tra nếu giỏ hàng trống
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('warning', 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi thanh toán.');
        }

        return view('checkout', compact('cart'));
    }

    /**
     * Lưu đơn hàng mới vào database
     */
    public function store(Request $request)
    {
        // Validate dữ liệu - Hiện tại chỉ chấp nhận phương thức thanh toán MoMo
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'payment_method' => 'required|in:momo',
        ]);

        Log::info('Checkout process started', [
            'user_id' => Auth::id(),
            'payment_method' => $request->payment_method
        ]);

        try {
            // Lấy giỏ hàng hiện tại
            $cart = $this->cartService->getCart();
            Log::info('Cart retrieved', ['cart_items_count' => $cart->items->count()]);

            // Kiểm tra nếu giỏ hàng trống
            if ($cart->items->isEmpty()) {
                Log::warning('Attempted checkout with empty cart', ['user_id' => Auth::id()]);
                return redirect()->route('cart.index')
                    ->with('error', 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi thanh toán.');
            }

            // Tính tổng tiền
            $total = $cart->total();
            Log::info('Cart total calculated', ['total' => $total]);

            // Transaction để đảm bảo tính toàn vẹn dữ liệu
            DB::beginTransaction();
            Log::info('DB transaction started');

            // Tạo đơn hàng mới với trạng thái "draft" - chỉ lưu tạm thời
            $orderData = [
                'user_id' => Auth::id(),
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'note' => $request->note,
                'total' => $total,
                'payment_method' => 'momo',
                'payment_status' => 'draft', // Thay đổi thành 'draft' thay vì 'processing'
                'order_status' => 'pending',
            ];

            Log::info('Creating temporary order with data', $orderData);
            $order = Order::create($orderData);
            Log::info('Temporary order created', ['order_id' => $order->id]);

            // Thêm các sản phẩm từ giỏ hàng vào đơn hàng
            foreach ($cart->items as $item) {
                $orderItemData = [
                    'order_id' => $order->id,
                    'movie_id' => $item->movie_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ];

                Log::info('Creating order item', $orderItemData);
                OrderItem::create($orderItemData);
            }

            Log::info('Order items created');

            // Lưu ID đơn hàng vào session để có thể khôi phục nếu thanh toán thất bại
            session(['pending_order_id' => $order->id]);

            // Commit transaction - đã lưu đơn hàng tạm thời
            DB::commit();
            Log::info('DB transaction committed');

            // Khởi tạo thanh toán MoMo và chuyển hướng người dùng
            Log::info('Initializing MoMo payment for order', ['order_id' => $order->id]);
            $paymentData = $this->momoService->createPayment($order);

            if ($paymentData && isset($paymentData['payUrl'])) {
                Log::info('MoMo payment created successfully', [
                    'order_id' => $order->id,
                    'payUrl' => $paymentData['payUrl']
                ]);

                // Lưu payUrl vào payment_details
                $order->update([
                    'payment_details' => array_merge($order->payment_details ?? [], [
                        'payUrl' => $paymentData['payUrl'],
                        'requestId' => $paymentData['requestId'] ?? null,
                        'orderId' => $paymentData['orderId'] ?? null,
                    ])
                ]);

                // Chưa xóa giỏ hàng vì thanh toán chưa thành công
                // Sẽ xóa giỏ hàng sau khi thanh toán thành công

                Log::info('Redirecting to MoMo payment URL', ['url' => $paymentData['payUrl']]);

                // Chuyển hướng tới trang thanh toán MoMo
                return redirect()->away($paymentData['payUrl']);
            }

            Log::error('Failed to create MoMo payment', [
                'order_id' => $order->id,
                'paymentData' => $paymentData
            ]);

            // Nếu không tạo được thanh toán, hiển thị thông báo lỗi
            return redirect()->route('orders.show', ['order' => $order->id])
                ->with('warning', 'Đơn hàng đã được tạo nhưng không thể khởi tạo thanh toán MoMo. Vui lòng thử thanh toán lại sau.');

        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();
            Log::error('Error creating order: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hiển thị trang cảm ơn sau khi đặt hàng
     */
    public function success(Order $order)
    {
        // Kiểm tra quyền truy cập, cho phép xem nếu là người dùng sở hữu đơn hàng
        // hoặc nếu có thông tin user_id từ order khớp với session
        if (!Auth::check()) {
            // Đăng nhập lại người dùng từ order nếu chưa đăng nhập
            Auth::loginUsingId($order->user_id, true);
            Log::info('User re-authenticated for success page', ['user_id' => $order->user_id]);
        } else if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Eager loading để tối ưu performance
        $order->load('items.movie');

        return view('orders.success', compact('order'));
    }

    /**
     * Hiển thị danh sách đơn hàng của người dùng
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show(Order $order)
    {
        // Kiểm tra quyền truy cập, chỉ người dùng sở hữu đơn hàng mới xem được
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Eager loading để tối ưu performance
        $order->load('items.movie');

        return view('orders.show', compact('order'));
    }
}
