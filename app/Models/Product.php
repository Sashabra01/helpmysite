<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'sale_price',
        'stock',
        'category_id',
        'brand_id',
        'is_active',
        'color',
        'size',
        'material'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Отношения
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // Аксессоры для удобства
    public function getFinalPriceAttribute()
    {
        return $this->sale_price ?: $this->price;
    }

    public function getHasDiscountAttribute()
    {
        return $this->sale_price && $this->sale_price < $this->price;
    }

    public function getDiscountPercentAttribute()
    {
        if (!$this->has_discount) {
            return 0;
        }
        
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function getInStockAttribute()
    {
        return $this->stock > 0;
    }
}
