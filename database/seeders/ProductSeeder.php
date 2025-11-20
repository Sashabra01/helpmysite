<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Категории
        $men = Category::firstOrCreate([
            'name' => 'Мужская одежда', 
            'slug' => 'muzhskaya-odezhda'
        ], ['is_active' => true]);
        
        $women = Category::firstOrCreate([
            'name' => 'Женская одежда',
            'slug' => 'zhenskaya-odezhda' 
        ], ['is_active' => true]);
        
        // Бренд
        $brand = Brand::firstOrCreate([
            'name' => 'FashionBrand',
            'slug' => 'fashionbrand'
        ], ['is_active' => true]);
        
        // Товары
        $products = [
            ['name' => 'Футболка Basic', 'slug' => 'futbolka-basic', 'price' => 1200, 'category_id' => $men->id],
            ['name' => 'Джинсы Classic', 'slug' => 'dzhinsy-classic', 'price' => 3200, 'category_id' => $men->id],
            ['name' => 'Платье Elegant', 'slug' => 'plate-elegant', 'price' => 4500, 'category_id' => $women->id],
            ['name' => 'Рубашка Office', 'slug' => 'rubashka-office', 'price' => 2800, 'category_id' => $men->id],
            ['name' => 'Юбка Mini', 'slug' => 'yubka-mini', 'price' => 1900, 'category_id' => $women->id],
            ['name' => 'Куртка Winter', 'slug' => 'kurtka-winter', 'price' => 6500, 'category_id' => $men->id]
        ];
        
        foreach ($products as $product) {
            Product::firstOrCreate(
                ['slug' => $product['slug']],
                array_merge($product, [
                    'description' => 'Описание ' . $product['name'],
                    'brand_id' => $brand->id,
                    'color' => 'Черный',
                    'size' => 'M',
                    'stock' => rand(5, 20),
                    'is_active' => true
                ])
            );
        }
        
        echo "Создано " . count($products) . " тестовых товаров!\n";
    }
}
