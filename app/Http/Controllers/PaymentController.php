<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MomoPaymentService;
use App\Services\CartService;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    protected $momoService;
    protected $cartService;

    public function __construct(MomoPaymentService $momoService, CartService $cartService)
    {
        $this->momoService = $momoService;
        $this->cartService = $cartService;
    }

    /**
     * Khởi tạo thanh toán MoMo
     */
    public function payWithMomo(Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Kiểm tra trạng thái đơn hàng
        if ($order->payment_status === Order::PAYMENT_COMPLETED) {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Đơn hàng này đã được thanh toán.');
        }

        // Tạo thanh toán với MoMo
        $paymentData = $this->momoService->createPayment($order);

        if ($paymentData && isset($paymentData['payUrl'])) {
            // Chuyển hướng đến trang thanh toán MoMo
            return redirect()->away($paymentData['payUrl']);
        }

        // Thông báo lỗi nếu không thể tạo thanh toán
        return redirect()->route('orders.show', $order)
            ->with('error', 'Không thể khởi tạo thanh toán MoMo. Vui lòng thử lại sau.');
    }

    /**
     * Xử lý callback từ MoMo
     */
    public function momoCallback(Request $request)
    {
        Log::info('MoMo callback received', $request->all());

        // Kiểm tra dữ liệu từ MoMo
        if (!$request->has('orderId') || !$request->has('resultCode')) {
            Log::error('Invalid MoMo callback data');
            return redirect()->route('orders.index')
                ->with('error', 'Dữ liệu thanh toán không hợp lệ.');
        }

        $order = Order::where('transaction_id', $request->orderId)->first();

        if (!$order) {
            Log::error('Order not found for transaction: ' . $request->orderId);
            return redirect()->route('orders.index')
                ->with('error', 'Không tìm thấy đơn hàng tương ứng.');
        }

        // Đăng nhập người dùng nếu chưa đăng nhập (đề phòng session bị mất)
        if (!Auth::check()) {
            Auth::loginUsingId($order->user_id, true);
            Log::info('User re-authenticated for MoMo callback', ['user_id' => $order->user_id]);
        }

        // Xử lý kết quả thanh toán
        if ($request->resultCode == 0) {
            // Thanh toán thành công, cập nhật đơn hàng
            $order->update([
                'payment_status' => Order::PAYMENT_COMPLETED,
                'order_status' => Order::STATUS_PROCESSING,
                'payment_details' => array_merge($order->payment_details ?? [], [
                    'transaction_id' => $request->transId ?? null,
                    'payment_time' => now()->toDateTimeString(),
                    'result_code' => $request->resultCode,
                    'message' => $request->message ?? null
                ])
            ]);

            // Gửi email xác nhận đơn hàng
            try {
                $order->load('items.movie');
                Mail::to($order->email)->send(new OrderConfirmation($order));
                Log::info('Order confirmation email sent', ['order_id' => $order->id, 'email' => $order->email]);
            } catch (\Exception $e) {
                Log::error('Failed to send order confirmation email: ' . $e->getMessage(), [
                    'order_id' => $order->id,
                    'exception' => $e
                ]);
            }

            // Chỉ xóa giỏ hàng khi thanh toán thành công
            $this->cartService->clear();
            Log::info('Cart cleared after successful payment');

            // Luôn chuyển hướng đến trang success
            return redirect()->route('orders.success', $order)
                ->with('success', 'Thanh toán thành công! Đơn hàng của bạn đang được xử lý.');
        } else {
            // Thanh toán thất bại, cập nhật trạng thái đơn hàng
            $order->update([
                'payment_status' => Order::PAYMENT_FAILED,
                'payment_details' => array_merge($order->payment_details ?? [], [
                    'result_code' => $request->resultCode,
                    'message' => $request->message ?? 'Thanh toán thất bại',
                    'payment_time' => now()->toDateTimeString()
                ])
            ]);

            return redirect()->route('orders.show', $order)
                ->with('error', 'Thanh toán không thành công: ' . ($request->message ?? 'Không rõ lỗi'));
        }
    }

    /**
     * Xử lý IPN từ MoMo
     */
    public function momoIpn(Request $request)
    {
        Log::info('MoMo IPN received', $request->all());

        // Xử lý IPN và trả về kết quả
        $result = $this->momoService->handleIpn($request);

        // Nếu thanh toán thành công qua IPN, cũng gửi email và xóa giỏ hàng
        if (isset($result['status']) && $result['status'] === 'success') {
            $orderId = $request->orderId;
            $order = Order::where('transaction_id', $orderId)->first();

            if ($order) {
                try {
                    $order->load('items.movie');
                    Mail::to($order->email)->send(new OrderConfirmation($order));
                    Log::info('Order confirmation email sent via IPN', ['order_id' => $order->id]);
                } catch (\Exception $e) {
                    Log::error('Failed to send order confirmation email via IPN: ' . $e->getMessage());
                }
            }

            $this->cartService->clear();
            Log::info('Cart cleared after successful IPN payment notification');
        }

        return response()->json($result);
    }

    /**
     * Xử lý hủy thanh toán
     */
    public function momoCancel(Request $request)
    {
        Log::info('MoMo payment cancelled', $request->all());

        $orderId = $request->orderId ?? session('pending_order_id');

        if (!$orderId) {
            return redirect()->route('cart.index')
                ->with('warning', 'Bạn đã hủy thanh toán.');
        }

        $order = Order::where('transaction_id', $orderId)
                ->orWhere('id', $orderId)
                ->first();

        if ($order) {
            // Cập nhật trạng thái đơn hàng thành "hủy"
            $order->update([
                'payment_status' => Order::PAYMENT_FAILED,
                'order_status' => Order::STATUS_CANCELLED,
                'payment_details' => array_merge($order->payment_details ?? [], [
                    'cancelled_at' => now()->toDateTimeString(),
                    'cancellation_reason' => 'User cancelled the payment'
                ])
            ]);

            return redirect()->route('orders.show', $order)
                ->with('warning', 'Bạn đã hủy quá trình thanh toán. Vui lòng thử lại sau.');
        }

        return redirect()->route('cart.index')
            ->with('warning', 'Bạn đã hủy thanh toán. Giỏ hàng của bạn vẫn được giữ nguyên.');
    }
}
