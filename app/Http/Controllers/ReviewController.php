<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a newly created review.
     */
    public function store(Request $request, Product $product)
    {
        // Проверяем авторизацию
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для добавления отзыва необходимо авторизоваться'
            ], 401);
        }

        // Проверяем, может ли пользователь оставить отзыв
        if (Review::canUserReview(Auth::id(), $product->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже оставляли отзыв на этот товар'
            ], 400);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
            'advantages' => 'nullable|string|max:500',
            'disadvantages' => 'nullable|string|max:500'
        ]);

        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'advantages' => $request->advantages,
            'disadvantages' => $request->disadvantages,
            'is_approved' => true, // Автоматическое одобрение для демо
            'is_verified' => $this->checkIfVerifiedPurchase(Auth::id(), $product->id)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Отзыв успешно добавлен',
            'review' => $review->load('user')
        ]);
    }

    /**
     * Update the specified review.
     */
    public function update(Request $request, Review $review)
    {
        // Проверяем авторизацию
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Необходимо авторизоваться'
            ], 401);
        }

        // Проверяем, что пользователь редактирует свой отзыв
        if ($review->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Вы можете редактировать только свои отзывы'
            ], 403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
            'advantages' => 'nullable|string|max:500',
            'disadvantages' => 'nullable|string|max:500'
        ]);

        $review->update($request->only(['rating', 'comment', 'advantages', 'disadvantages']));

        return response()->json([
            'success' => true,
            'message' => 'Отзыв успешно обновлен',
            'review' => $review->fresh()->load('user')
        ]);
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Review $review)
    {
        // Проверяем авторизацию
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Необходимо авторизоваться'
            ], 401);
        }

        // Проверяем, что пользователь удаляет свой отзыв
        if ($review->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Вы можете удалять только свои отзывы'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Отзыв успешно удален'
        ]);
    }

    /**
     * Get reviews for a product.
     */
    public function index(Product $product, Request $request)
    {
        $reviews = $product->approvedReviews()
            ->with('user')
            ->latest();

        // Фильтрация по рейтингу
        if ($request->has('rating') && $request->rating) {
            $reviews->where('rating', $request->rating);
        }

        // Сортировка
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'newest':
                    $reviews->latest();
                    break;
                case 'oldest':
                    $reviews->oldest();
                    break;
                case 'highest':
                    $reviews->orderBy('rating', 'desc');
                    break;
                case 'lowest':
                    $reviews->orderBy('rating', 'asc');
                    break;
            }
        }

        $reviews = $reviews->paginate(10);

        $userReview = null;
        if (Auth::check()) {
            $userReview = $product->getUserReview(Auth::id());
        }

        return response()->json([
            'reviews' => $reviews,
            'user_review' => $userReview,
            'average_rating' => $product->average_rating,
            'reviews_count' => $product->reviews_count,
            'rating_distribution' => $product->rating_distribution,
            'rating_percentages' => $product->rating_percentages
        ]);
    }

    /**
     * Check if user has purchased the product
     */
    private function checkIfVerifiedPurchase($userId, $productId)
    {
        // Здесь можно реализовать проверку покупки товара
        // Например, проверить есть ли заказ с этим товаром у пользователя
        return false;
    }

    /**
     * Get user's reviews
     */
    public function userReviews(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Необходимо авторизоваться'
            ], 401);
        }

        $reviews = Auth::user()->reviews()
            ->with('product')
            ->latest()
            ->paginate(10);

        return response()->json([
            'reviews' => $reviews
        ]);
    }
}
