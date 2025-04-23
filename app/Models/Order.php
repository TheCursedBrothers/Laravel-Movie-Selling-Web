<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'email', 'phone', 'address', 'note',
        'total', 'payment_method', 'payment_status', 'order_status',
        'transaction_id', 'payment_details'
    ];

    protected $casts = [
        'payment_details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Trạng thái đơn hàng
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Trạng thái thanh toán
     */
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PROCESSING = 'processing';
    const PAYMENT_COMPLETED = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';
    const PAYMENT_DRAFT = 'draft'; // Thêm trạng thái draft cho đơn hàng chưa thanh toán

    /**
     * Phương thức thanh toán
     */
    const METHOD_COD = 'cod';
    const METHOD_MOMO = 'momo';

    /**
     * Quan hệ với User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ với OrderItem
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Kiểm tra đơn hàng đã hoàn thành chưa
     */
    public function isCompleted()
    {
        return $this->order_status === self::STATUS_COMPLETED;
    }

    /**
     * Kiểm tra đơn hàng đã thanh toán chưa
     */
    public function isPaid()
    {
        return $this->payment_status === self::PAYMENT_COMPLETED;
    }

    /**
     * Format total cho đẹp
     */
    public function formattedTotal()
    {
        return number_format($this->total, 0, ',', '.') . 'đ';
    }

    /**
     * Format status thành text tiếng Việt
     */
    public function statusText()
    {
        $statusMap = [
            self::STATUS_PENDING => 'Chờ xử lý',
            self::STATUS_PROCESSING => 'Đang xử lý',
            self::STATUS_COMPLETED => 'Đã hoàn thành',
            self::STATUS_CANCELLED => 'Đã hủy',
        ];

        return $statusMap[$this->order_status] ?? 'Không xác định';
    }

    /**
     * Format payment status thành text tiếng Việt
     */
    public function paymentStatusText()
    {
        $statusMap = [
            self::PAYMENT_PENDING => 'Chờ thanh toán',
            self::PAYMENT_PROCESSING => 'Đang xử lý',
            self::PAYMENT_COMPLETED => 'Đã thanh toán',
            self::PAYMENT_FAILED => 'Thanh toán thất bại',
            self::PAYMENT_REFUNDED => 'Đã hoàn tiền',
        ];

        return $statusMap[$this->payment_status] ?? 'Không xác định';
    }

    /**
     * Format payment method thành text tiếng Việt
     */
    public function paymentMethodText()
    {
        $methodMap = [
            self::METHOD_COD => 'Thanh toán khi nhận hàng',
            self::METHOD_MOMO => 'Ví MoMo',
        ];

        return $methodMap[$this->payment_method] ?? 'Không xác định';
    }
}
