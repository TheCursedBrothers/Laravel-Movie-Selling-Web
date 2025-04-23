<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class MomoPaymentService
{
    protected $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
    protected $partnerCode;
    protected $accessKey;
    protected $secretKey;
    protected $ipnUrl;
    protected $redirectUrl;

    public function __construct()
    {
        // Lấy thông tin cấu hình từ config
        $this->partnerCode = config('services.momo.partner_code');
        $this->accessKey = config('services.momo.access_key');
        $this->secretKey = config('services.momo.secret_key');

        // Đặt URL callback - Đảm bảo URL đầy đủ
        $this->redirectUrl = config('app.url') . '/payments/momo/callback';
        $this->ipnUrl = config('app.url') . '/payments/momo/ipn';

        // Log thông tin URL
        Log::info('MoMo Payment URLs', [
            'redirectUrl' => $this->redirectUrl,
            'ipnUrl' => $this->ipnUrl
        ]);
    }

    /**
     * Khởi tạo thanh toán cho đơn hàng
     */
    public function createPayment(Order $order)
    {
        try {
            // Tạo các tham số giao dịch
            $orderId = time() . "";
            $requestId = time() . "";
            $amount = (string)max(10000, (int)$order->total);
            $orderInfo = "Thanh toan don hang #" . $order->id;
            $extraData = "";
            $requestType = "payWithATM";

            // Lưu transaction_id vào đơn hàng
            $order->update([
                'transaction_id' => $orderId,
                'payment_details' => [
                    'payment_method' => 'momo_atm',
                    'order_id' => $orderId,
                    'amount' => $amount,
                ]
            ]);

            // Tạo chuỗi hash
            $rawHash = "accessKey=" . $this->accessKey .
                "&amount=" . $amount .
                "&extraData=" . $extraData .
                "&ipnUrl=" . $this->ipnUrl .
                "&orderId=" . $orderId .
                "&orderInfo=" . $orderInfo .
                "&partnerCode=" . $this->partnerCode .
                "&redirectUrl=" . $this->redirectUrl .
                "&requestId=" . $requestId .
                "&requestType=" . $requestType;

            $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

            // Chuẩn bị dữ liệu
            $data = [
                'partnerCode' => $this->partnerCode,
                'partnerName' => "TopMovies",
                'storeId' => "MomoTestStore",
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $this->redirectUrl,
                'ipnUrl' => $this->ipnUrl,
                'lang' => 'vi',
                'extraData' => $extraData,
                'requestType' => $requestType,
                'signature' => $signature
            ];

            // Log thông tin request
            Log::info('MoMo payment request data', $data);

            // Gửi request đến MoMo
            $result = $this->execPostRequest($this->endpoint, json_encode($data));
            $jsonResult = json_decode($result, true);

            // Log kết quả
            Log::info('MoMo payment response', $jsonResult ?: ['error' => 'No response']);

            // Kiểm tra kết quả
            if (isset($jsonResult['payUrl'])) {
                // Cập nhật trạng thái thanh toán
                $order->update([
                    'payment_status' => Order::PAYMENT_PROCESSING
                ]);

                return $jsonResult;
            }

            // Log lỗi nếu không có payUrl
            Log::error('MoMo payment failed', $jsonResult ?: ['error' => 'Unknown error']);

            return null;
        } catch (\Exception $e) {
            Log::error('Error creating MoMo payment: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Xử lý callback từ MoMo
     */
    public function handleCallback($request)
    {
        try {
            // Lấy thông tin từ request
            $resultCode = $request->resultCode;
            $orderId = $request->orderId;

            // Tìm đơn hàng
            $order = Order::where('transaction_id', $orderId)->first();

            if (!$order) {
                Log::error('Order not found for transaction: ' . $orderId);
                return false;
            }

            // Cập nhật thông tin thanh toán
            $paymentDetails = array_merge($order->payment_details ?? [], [
                'result_code' => $resultCode,
                'message' => $request->message ?? null,
                'transaction_id' => $request->transId ?? null,
                'pay_type' => $request->payType ?? null,
                'response_time' => now()->toDateTimeString(),
            ]);

            // Cập nhật trạng thái đơn hàng
            if ($resultCode == 0) {
                // Thanh toán thành công
                $order->update([
                    'payment_status' => Order::PAYMENT_COMPLETED,
                    'order_status' => Order::STATUS_PROCESSING,
                    'payment_details' => $paymentDetails
                ]);

                return $order;
            } else {
                // Thanh toán thất bại
                $order->update([
                    'payment_status' => Order::PAYMENT_FAILED,
                    'payment_details' => $paymentDetails
                ]);

                return $order;
            }
        } catch (\Exception $e) {
            Log::error('Error handling MoMo callback: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xử lý IPN từ MoMo
     */
    public function handleIpn($request)
    {
        try {
            // Lấy thông tin từ request
            $resultCode = $request->resultCode;
            $orderId = $request->orderId;

            // Tìm đơn hàng
            $order = Order::where('transaction_id', $orderId)->first();

            if (!$order) {
                Log::error('Order not found for transaction: ' . $orderId);
                return [
                    'status' => 'error',
                    'message' => 'Order not found'
                ];
            }

            // Cập nhật thông tin thanh toán
            $paymentDetails = array_merge($order->payment_details ?? [], [
                'result_code' => $resultCode,
                'message' => $request->message ?? null,
                'transaction_id' => $request->transId ?? null,
                'pay_type' => $request->payType ?? null,
                'response_time' => now()->toDateTimeString(),
            ]);

            // Cập nhật trạng thái đơn hàng
            if ($resultCode == 0) {
                // Thanh toán thành công
                $order->update([
                    'payment_status' => Order::PAYMENT_COMPLETED,
                    'order_status' => Order::STATUS_PROCESSING,
                    'payment_details' => $paymentDetails
                ]);

                return [
                    'status' => 'success',
                    'message' => 'Payment completed'
                ];
            } else {
                // Thanh toán thất bại
                $order->update([
                    'payment_status' => Order::PAYMENT_FAILED,
                    'payment_details' => $paymentDetails
                ]);

                return [
                    'status' => 'error',
                    'message' => 'Payment failed'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error handling MoMo IPN: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Server error'
            ];
        }
    }

    /**
     * Gửi request POST tới API MoMo
     */
    protected function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);

        // Increase timeout values
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // Increase from 30 to 60
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);  // Increase from 30 to 60

        // Disable SSL verification for testing
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Better error handling
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            Log::error('cURL Error in MoMo payment request: ' . $error, [
                'url' => $url,
                'curl_errno' => curl_errno($ch)
            ]);

            // For debugging
            $info = curl_getinfo($ch);
            Log::info('cURL info for failed request:', $info);
        }

        curl_close($ch);

        return $result;
    }
}
