<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác nhận đơn hàng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            background-color: #3a3a3a;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .order-details {
            margin: 20px 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }
        .item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .item:last-child {
            border-bottom: none;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #777;
        }
        .total {
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
            font-size: 16px;
        }
        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Xác nhận đơn hàng</h1>
    </div>

    <div class="content">
        <p>Xin chào {{ $order->name }},</p>

        <p>Cảm ơn bạn đã đặt hàng tại TopMovies. Đơn hàng của bạn đã được xác nhận và đang được xử lý.</p>

        <div class="order-details">
            <h2>Thông tin đơn hàng #{{ $order->id }}</h2>
            <p><strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Trạng thái thanh toán:</strong>
                @if($order->payment_status == 'paid')
                    Đã thanh toán
                @elseif($order->payment_status == 'processing')
                    Đang xử lý
                @else
                    Chưa thanh toán
                @endif
            </p>
            <p><strong>Phương thức thanh toán:</strong>
                @if($order->payment_method == 'momo')
                    Thanh toán qua MoMo
                @else
                    {{ $order->payment_method }}
                @endif
            </p>
        </div>

        <h3>Chi tiết sản phẩm</h3>

        @foreach($order->items as $item)
        <div class="item">
            <p><strong>{{ $item->movie->title }}</strong> x {{ $item->quantity }}</p>
            <p>Đơn giá: {{ number_format($item->price, 0, ',', '.') }}đ</p>
            <p>Thành tiền: {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</p>
        </div>
        @endforeach

        <div class="total">
            Tổng cộng: {{ number_format($order->total, 0, ',', '.') }}đ
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ url('/orders/' . $order->id) }}" class="button">Xem chi tiết đơn hàng</a>
        </div>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} TopMovies. Tất cả các quyền được bảo lưu.</p>
        <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua email: support@topmovies.com</p>
    </div>
</body>
</html>
