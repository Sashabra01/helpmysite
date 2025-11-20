<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id', 
        'rating',
        'comment',
        'advantages',
        'disadvantages',
        'is_approved',
        'is_verified'
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'is_verified' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = [
        'formatted_date'
    ];

    /**
     * Отношение к пользователю
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Отношение к товару
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope для одобренных отзывов
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope для проверенных покупок
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope для отзывов с рейтингом
     */
    public function scopeWithRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope для последних отзывов
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Проверяет, может ли пользователь оставить отзыв
     */
    public static function canUserReview($userId, $productId)
    {
        return !static::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Получить форматированную дату
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d.m.Y');
    }

    /**
     * Получить короткий комментарий
     */
    public function getShortCommentAttribute()
    {
        return str_limit($this->comment, 150);
    }

    /**
     * Пометить отзыв как проверенный
     */
    public function markAsVerified()
    {
        $this->update(['is_verified' => true]);
    }

    /**
     * Одобрить отзыв
     */
    public function approve()
    {
        $this->update(['is_approved' => true]);
    }

    /**
     * Отклонить отзыв
     */
    public function reject()
    {
        $this->delete();
    }

    /**
     * Проверить, принадлежит ли отзыв пользователю
     */
    public function isOwnedBy($userId)
    {
        return $this->user_id == $userId;
    }
}
