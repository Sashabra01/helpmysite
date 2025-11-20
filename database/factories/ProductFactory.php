<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'sku' => strtoupper(Str::random(8)),
            'description' => fake()->paragraph(),
            'full_description' => fake()->text(500),
            'price' => fake()->numberBetween(1000, 100000),
            'sale_price' => fake()->optional(0.3)->numberBetween(500, 50000),
            'cost_price' => fake()->numberBetween(500, 30000),
            'stock' => fake()->numberBetween(0, 100),
            'category_id' => Category::inRandomOrder()->first()->id,
            'brand_id' => Brand::inRandomOrder()->first()->id,
            'weight' => fake()->randomFloat(2, 0.1, 10),
            'dimensions' => fake()->randomNumber(2).'x'.fake()->randomNumber(2).'x'.fake()->randomNumber(1),
            'is_active' => fake()->boolean(90),
            'is_featured' => fake()->boolean(20),
            'meta_title' => fake()->optional()->sentence(),
            'meta_description' => fake()->optional()->text(160),
            'views' => fake()->numberBetween(0, 1000),
        ];
    }
}