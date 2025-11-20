<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Категории
        $men = Category::firstOrCreate(['name' => 'Мужская одежда', 'slug' => 'muzhskaya-odezhda'], ['is_active' => true]);
        $women = Category::firstOrCreate(['name' => 'Женская одежда', 'slug' => 'zhenskaya-odezhda'], ['is_active' => true]);
        
        // Бренды
        $brand = Brand::firstOrCreate(['name' => 'FashionBrand', 'slug' => 'fashionbrand'], ['is_active' => true]);
        
        // Товары
        $products = [
            ['name' => 'Футболка Basic', 'slug' => 'futbolka-basic', 'price' => 1200, 'category_id' => $men->id],
            ['name' => 'Джинсы Classic', 'slug' => 'dzhinsy-classic', 'price' => 3200, 'category_id' => $men->id],
            ['name' => 'Платье Elegant', 'slug' => 'plate-elegant', 'price' => 4500, 'category_id' => $women->id],
        ];
        
        foreach ($products as $product) {
            Product::firstOrCreate(
                ['slug' => $product['slug']],
                array_merge($product, [
                    'description' => 'Описание ' . $product['name'],
                    'brand_id' => $brand->id,
                    'stock' => 10,
                    'is_active' => true
                ])
            );
        }
    }
}
