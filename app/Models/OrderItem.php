<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'price',
        'quantity'
    ];

    // Связь с заказом
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Связь с товаром
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessors
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', ' ') . ' ₽';
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->price * $this->quantity, 0, ',', ' ') . ' ₽';
    }
}
