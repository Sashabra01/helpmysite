<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_name',
        'product_price',
        'quantity'
    ];

    // Связь с заказом
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
