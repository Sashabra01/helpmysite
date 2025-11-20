<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $users = User::all();

        $reviews = [
            [
                'product_id' => $products->where('name', 'iPhone 15 Pro')->first()->id,
                'user_id' => $users->where('email', 'ivan@example.com')->first()->id,
                'rating' => 5,
                'comment' => 'Отличный телефон! Камера просто потрясающая, батарея держит долго. Рекомендую!',
                'advantages' => 'Отличная камера, быстрая работа, премиальный дизайн',
                'disadvantages' => 'Высокая цена',
                'is_approved' => true,
                'is_verified' => true,
            ],
            [
                'product_id' => $products->where('name', 'iPhone 15 Pro')->first()->id,
                'user_id' => $users->where('email', 'maria@example.com')->first()->id,
                'rating' => 4,
                'comment' => 'Хороший телефон, но цена завышена. Качество сборки на высоте.',
                'advantages' => 'Качество сборки, экран, производительность',
                'disadvantages' => 'Цена, тяжеловат',
                'is_approved' => true,
                'is_verified' => true,
            ],
            [
                'product_id' => $products->where('name', 'Samsung Galaxy S24 Ultra')->first()->id,
                'user_id' => $users->where('email', 'ivan@example.com')->first()->id,
                'rating' => 5,
                'comment' => 'S-Pen это просто бомба! Очень удобно делать заметки и редактировать документы.',
                'advantages' => 'S-Pen, большой экран, отличная камера',
                'disadvantages' => 'Большой размер',
                'is_approved' => true,
                'is_verified' => true,
            ],
            [
                'product_id' => $products->where('name', 'Nike Air Force 1')->first()->id,
                'user_id' => $users->where('email', 'maria@example.com')->first()->id,
                'rating' => 5,
                'comment' => 'Очень удобные кроссовки! Ношу каждый день, качество отличное.',
                'advantages' => 'Удобные, стильные, качественные',
                'disadvantages' => 'Нет',
                'is_approved' => true,
                'is_verified' => true,
            ],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }

        // Создаем дополнительные случайные отзывы
        Review::factory(30)->create();
    }
}
