<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'website',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    protected $appends = [
        'products_count'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });

        static::updating(function ($brand) {
            if ($brand->isDirty('name') && empty($brand->getOriginal('slug'))) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }

    /**
     * Отношение к товарам
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope для активных брендов
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для сортировки
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Получить количество товаров бренда
     */
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }

    /**
     * Получить URL логотипа
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return \Illuminate\Support\Facades\Storage::url($this->logo);
        }
        return null;
    }

    /**
     * Получить URL страницы бренда
     */
    public function getUrlAttribute()
    {
        return route('catalog.brand', $this->slug);
    }

    /**
     * Проверить, есть ли товары у бренда
     */
    public function hasProducts()
    {
        return $this->products()->exists();
    }

    /**
     * Получить SEO заголовок
     */
    public function getSeoTitleAttribute()
    {
        return $this->meta_title ?: $this->name;
    }

    /**
     * Получить SEO описание
     */
    public function getSeoDescriptionAttribute()
    {
        return $this->meta_description ?: str_limit(strip_tags($this->description), 160);
    }
}
