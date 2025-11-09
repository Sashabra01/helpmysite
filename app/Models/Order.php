<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total_amount',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'payment_method',
        'shipping_method',
        'notes',
    ];

    // Статусы заказов
    const STATUSES = [
        'new' => 'Новый',
        'processing' => 'В обработке',
        'shipped' => 'Отправлен',
        'delivered' => 'Доставлен',
        'cancelled' => 'Отменен',
        'refunded' => 'Возврат',
    ];

    // Способы оплаты
    const PAYMENT_METHODS = [
        'card' => 'Банковская карта',
        'cash' => 'Наличные',
        'online' => 'Электронные деньги',
    ];

    // Способы доставки
    const SHIPPING_METHODS = [
        'delivery' => 'Курьерская доставка',
        'pickup' => 'Самовывоз',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Accessors
     */
    public function getStatusTextAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getPaymentMethodTextAttribute()
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    public function getShippingMethodTextAttribute()
    {
        return self::SHIPPING_METHODS[$this->shipping_method] ?? $this->shipping_method;
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 0, ',', ' ') . ' ₽';
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d.m.Y H:i');
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['new', 'processing']);
    }

    /**
     * Check if status can be changed
     */
    public function canChangeStatus($newStatus)
    {
        $allowedTransitions = [
            'new' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered', 'cancelled'],
            'delivered' => ['refunded'],
            'cancelled' => [],
            'refunded' => [],
        ];

        return in_array($newStatus, $allowedTransitions[$this->status] ?? []);
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'new' => 'blue',
            'processing' => 'yellow',
            'shipped' => 'indigo',
            'delivered' => 'green',
            'cancelled' => 'red',
            'refunded' => 'gray',
        ];

        return $colors[$this->status] ?? 'gray';
    }
}
