<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id'
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if product is in user's wishlist
     */
    public static function isInWishlist($userId, $productId)
    {
        return static::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->exists();
    }

    /**
     * Get user's wishlist items count
     */
    public static function getCount($userId)
    {
        return static::where('user_id', $userId)->count();
    }

    /**
     * Add product to wishlist
     */
    public static function addToWishlist($userId, $productId)
    {
        if (!static::isInWishlist($userId, $productId)) {
            return static::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
        }
        return null;
    }

    /**
     * Remove product from wishlist
     */
    public static function removeFromWishlist($userId, $productId)
    {
        return static::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->delete();
    }
}