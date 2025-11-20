<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->text(200),
            'advantages' => fake()->optional()->text(100),
            'disadvantages' => fake()->optional()->text(100),
            'is_approved' => fake()->boolean(80),
            'is_verified' => fake()->boolean(60),
        ];
    }
}