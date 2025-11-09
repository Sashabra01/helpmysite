<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug', 
        'sku',
        'description',
        'full_description',
        'price',
        'sale_price',
        'cost_price',
        'stock',
        'category_id',
        'brand_id',
        'weight',
        'dimensions',
        'images',
        'is_active',
        'is_featured',
        'meta_title',
        'meta_description',
        'views'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock' => 'integer',
        'weight' => 'decimal:2',
        'images' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'views' => 'integer'
    ];

    protected $appends = [
        'average_rating',
        'reviews_count',
        'rating_distribution'
    ];

    /**
     * Отношение к категории
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Отношение к бренду
     */
    public function brand()
{
    return $this->belongsTo(Brand::class);
}

    /**
     * Отношение к отзывам
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Отношение к одобренным отзывам
     */
    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    /**
     * Scope для активных товаров
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для товаров в наличии
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope для рекомендуемых товаров
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope для товаров со скидкой
     */
    public function scopeOnSale($query)
    {
        return $query->where('sale_price', '>', 0)
                    ->whereColumn('sale_price', '<', 'price');
    }

    /**
     * Получить URL изображения
     */
    public function getImageUrlAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            return Storage::url($this->images[0]);
        }
        return null;
    }

    /**
     * Получить основное изображение
     */
    public function getMainImageAttribute()
    {
        return $this->image_url;
    }

    /**
     * Получить форматированную цену
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', ' ') . ' ₽';
    }

    /**
     * Получить форматированную цену со скидкой
     */
    public function getFormattedSalePriceAttribute()
    {
        return $this->sale_price ? number_format($this->sale_price, 0, ',', ' ') . ' ₽' : null;
    }

    /**
     * Получить скидку в процентах
     */
    public function getDiscountPercentAttribute()
    {
        if ($this->sale_price && $this->price > 0) {
            return round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }

    /**
     * Проверить наличие скидки
     */
    public function getHasDiscountAttribute()
    {
        return $this->sale_price > 0 && $this->sale_price < $this->price;
    }

    /**
     * Получить средний рейтинг товара
     */
    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?: 0;
    }

    /**
     * Получить количество отзывов
     */
    public function getReviewsCountAttribute()
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Получить распределение рейтингов
     */
    public function getRatingDistributionAttribute()
    {
        $distribution = $this->approvedReviews()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->pluck('count', 'rating');

        // Заполняем все возможные рейтинги от 1 до 5
        $fullDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $fullDistribution[$i] = $distribution[$i] ?? 0;
        }

        return $fullDistribution;
    }

    /**
     * Получить процент для каждого рейтинга
     */
    public function getRatingPercentagesAttribute()
    {
        $total = $this->reviews_count;
        if ($total === 0) return [];

        $percentages = [];
        foreach ($this->rating_distribution as $rating => $count) {
            $percentages[$rating] = round(($count / $total) * 100);
        }

        return $percentages;
    }

    /**
     * Проверить, оставлял ли пользователь отзыв на этот товар
     */
    public function hasUserReview($userId = null)
    {
        if (!$userId && auth()->check()) {
            $userId = auth()->id();
        }

        if (!$userId) return false;

        return $this->reviews()->where('user_id', $userId)->exists();
    }

    /**
     * Получить отзыв пользователя на этот товар
     */
    public function getUserReview($userId = null)
    {
        if (!$userId && auth()->check()) {
            $userId = auth()->id();
        }

        if (!$userId) return null;

        return $this->reviews()->where('user_id', $userId)->first();
    }

    /**
     * Проверить, покупал ли пользователь этот товар
     */
    public function hasUserPurchased($userId = null)
    {
        // Здесь можно реализовать проверку покупки через заказы
        // Пока возвращаем false для примера
        return false;
    }

    /**
     * Увеличить счетчик просмотров
     */
    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * Получить похожие товары
     */
    public function getRelatedProducts($limit = 4)
    {
        return self::active()
            ->where('category_id', $this->category_id)
            ->where('id', '!=', $this->id)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Проверить, есть ли товар в избранном у пользователя
     */
    public function getIsInWishlistAttribute()
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->user()->wishlist()
            ->where('product_id', $this->id)
            ->exists();
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
