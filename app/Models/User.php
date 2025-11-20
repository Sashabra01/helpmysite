<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'postal_code'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = [
        'cart_count',
        'wishlist_count',
        'reviews_count',
        'average_rating'
    ];

    /**
     * Отношение к корзине
     */
    public function cartItems()
{
    return $this->hasMany(\App\Models\Cart::class);
}

    /**
     * Отношение к избранному
     */
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Отношение к заказам
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Отношение к отзывам
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Получить количество товаров в корзине
     */
    public function getCartCountAttribute()
    {
        return $this->cartItems()->count();
    }

    /**
     * Получить количество товаров в избранном
     */
    public function getWishlistCountAttribute()
    {
        return $this->wishlist()->count();
    }

    /**
     * Получить количество отзывов пользователя
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * Получить средний рейтинг отзывов пользователя
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?: 0;
    }

    /**
     * Получить одобренные отзывы пользователя
     */
    public function approvedReviews()
    {
        return $this->reviews()->where('is_approved', true);
    }

    /**
     * Проверить, может ли пользователь оставить отзыв на товар
     */
    public function canReviewProduct($productId)
    {
        return !$this->reviews()->where('product_id', $productId)->exists();
    }

    /**
     * Получить отзыв пользователя на конкретный товар
     */
    public function getReviewForProduct($productId)
    {
        return $this->reviews()->where('product_id', $productId)->first();
    }

    /**
     * Получить последние отзывы пользователя
     */
    public function getRecentReviews($limit = 5)
    {
        return $this->reviews()
            ->with('product')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Получить товары в избранном
     */
    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists')
            ->withTimestamps();
    }

    /**
     * Проверить, есть ли товар в избранном
     */
    public function hasInWishlist($productId)
    {
        return $this->wishlist()->where('product_id', $productId)->exists();
    }

    /**
     * Добавить товар в избранное
     */
    public function addToWishlist($productId)
    {
        if (!$this->hasInWishlist($productId)) {
            return $this->wishlist()->create(['product_id' => $productId]);
        }
        return null;
    }

    /**
     * Удалить товар из избранного
     */
    public function removeFromWishlist($productId)
    {
        return $this->wishlist()->where('product_id', $productId)->delete();
    }

    /**
     * Переключить товар в избранном
     */
    public function toggleWishlist($productId)
    {
        if ($this->hasInWishlist($productId)) {
            $this->removeFromWishlist($productId);
            return false;
        } else {
            $this->addToWishlist($productId);
            return true;
        }
    }

    /**
     * Получить товары в корзине
     */
    public function cartProducts()
    {
        return $this->belongsToMany(Product::class, 'carts')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Добавить товар в корзину
     */
    public function addToCart($productId, $quantity = 1)
    {
        $cartItem = $this->cartItems()->where('product_id', $productId)->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            $cartItem = $this->cartItems()->create([
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        return $cartItem;
    }

    /**
     * Обновить количество товара в корзине
     */
    public function updateCartItem($productId, $quantity)
    {
        return $this->cartItems()
            ->where('product_id', $productId)
            ->update(['quantity' => $quantity]);
    }

    /**
     * Удалить товар из корзины
     */
    public function removeFromCart($productId)
    {
        return $this->cartItems()->where('product_id', $productId)->delete();
    }

    /**
     * Очистить корзину
     */
    public function clearCart()
    {
        return $this->cartItems()->delete();
    }

    /**
     * Получить общую стоимость корзины
     */
    public function getCartTotalAttribute()
    {
        return $this->cartItems()
            ->with('product')
            ->get()
            ->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });
    }

    /**
     * Получить статистику заказов
     */
    public function getOrderStatsAttribute()
    {
        return [
            'total_orders' => $this->orders()->count(),
            'total_spent' => $this->orders()->where('status', 'completed')->sum('total_amount'),
            'pending_orders' => $this->orders()->whereIn('status', ['new', 'processing'])->count(),
            'completed_orders' => $this->orders()->where('status', 'completed')->count(),
        ];
    }

    /**
     * Получить уровень лояльности пользователя
     */
    public function getLoyaltyLevelAttribute()
    {
        $totalSpent = $this->orders()->where('status', 'completed')->sum('total_amount');

        if ($totalSpent >= 50000) return 'platinum';
        if ($totalSpent >= 20000) return 'gold';
        if ($totalSpent >= 5000) return 'silver';
        return 'basic';
    }

    /**
     * Получить скидку по уровню лояльности
     */
    public function getLoyaltyDiscountAttribute()
    {
        switch ($this->loyalty_level) {
            case 'platinum': return 15;
            case 'gold': return 10;
            case 'silver': return 5;
            default: return 0;
        }
    }

    /**
     * Проверить, является ли пользователь постоянным клиентом
     */
    public function getIsRegularCustomerAttribute()
    {
        return $this->orders()->count() >= 3;
    }

    /**
     * Получить полный адрес пользователя
     */
    public function getFullAddressAttribute()
    {
        $parts = [];
        if ($this->city) $parts[] = $this->city;
        if ($this->address) $parts[] = $this->address;
        if ($this->postal_code) $parts[] = $this->postal_code;

        return implode(', ', $parts);
    }

    /**
     * Получить инициалы пользователя
     */
    public function getInitialsAttribute()
    {
        $names = explode(' ', $this->name);
        $initials = '';

        foreach ($names as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }

        return $initials;
    }

    /**
     * Scope для поиска пользователей
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
    }

    /**
     * Scope для пользователей с заказами
     */
    public function scopeWithOrders($query)
    {
        return $query->has('orders');
    }

    /**
     * Scope для пользователей с отзывами
     */
    public function scopeWithReviews($query)
    {
        return $query->has('reviews');
    }
}
