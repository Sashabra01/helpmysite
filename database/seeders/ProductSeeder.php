<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $brands = Brand::all();

        $products = [
            // Смартфоны
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'Новый iPhone 15 Pro с революционной камерой и процессором A17 Pro.',
                'full_description' => 'iPhone 15 Pro представляет собой новый стандарт в мире смартфонов. С титановым дизайном, кнопкой действия и мощнейшим чипом A17 Pro.',
                'price' => 99990,
                'sale_price' => 89990,
                'stock' => 50,
                'sku' => 'IP15PRO256',
                'category_id' => $categories->where('slug', 'smartphones')->first()->id,
                'brand_id' => $brands->where('slug', 'apple')->first()->id,
                'weight' => 0.187,
                'dimensions' => '146.6x70.6x8.25',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'description' => 'Флагманский смартфон Samsung с S-Pen и улучшенной камерой.',
                'full_description' => 'Galaxy S24 Ultra оснащен мощным процессором, улучшенной камерой 200 Мп и встроенным S-Pen для максимальной продуктивности.',
                'price' => 89990,
                'sale_price' => 84990,
                'stock' => 35,
                'sku' => 'SGS24ULTRA',
                'category_id' => $categories->where('slug', 'smartphones')->first()->id,
                'brand_id' => $brands->where('slug', 'samsung')->first()->id,
                'weight' => 0.232,
                'dimensions' => '162.3x79x8.6',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Xiaomi 14 Pro',
                'description' => 'Мощный смартфон с камерой Leica и быстрой зарядкой.',
                'full_description' => 'Xiaomi 14 Pro предлагает премиальную камеру от Leica, молниеносную зарядку 120 Вт и потрясающий AMOLED-дисплей.',
                'price' => 69990,
                'stock' => 25,
                'sku' => 'XM14PRO512',
                'category_id' => $categories->where('slug', 'smartphones')->first()->id,
                'brand_id' => $brands->where('slug', 'xiaomi')->first()->id,
                'weight' => 0.210,
                'dimensions' => '161.4x75.3x8.5',
                'is_active' => true,
                'is_featured' => false,
            ],

            // Ноутбуки
            [
                'name' => 'MacBook Pro 16" M3',
                'description' => 'Профессиональный ноутбук для творческих задач.',
                'full_description' => 'MacBook Pro 16" с чипом M3 Pro или M3 Max представляет невероятную производительность для профессиональных рабочих процессов.',
                'price' => 249990,
                'stock' => 15,
                'sku' => 'MBP16M3',
                'category_id' => $categories->where('slug', 'laptops')->first()->id,
                'brand_id' => $brands->where('slug', 'apple')->first()->id,
                'weight' => 2.16,
                'dimensions' => '35.57x24.81x1.68',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Samsung Galaxy Book3 Ultra',
                'description' => 'Ультрабук с дискретной графикой и OLED-дисплеем.',
                'full_description' => 'Galaxy Book3 Ultra сочетает в себе мощь процессора Intel Core i9 и видеокарты NVIDIA GeForce RTX для мобильных рабочих станций.',
                'price' => 199990,
                'sale_price' => 179990,
                'stock' => 10,
                'sku' => 'SGBOOK3ULTRA',
                'category_id' => $categories->where('slug', 'laptops')->first()->id,
                'brand_id' => $brands->where('slug', 'samsung')->first()->id,
                'weight' => 1.79,
                'dimensions' => '35.54x25.04x1.69',
                'is_active' => true,
                'is_featured' => false,
            ],

            // Одежда
            [
                'name' => 'Nike Air Force 1',
                'description' => 'Классические кроссовки от Nike.',
                'full_description' => 'Легендарные кроссовки Nike Air Force 1 с кожаным верхом и амортизацией Air.',
                'price' => 12990,
                'sale_price' => 10990,
                'stock' => 100,
                'sku' => 'NIKEAF1WHITE',
                'category_id' => $categories->where('slug', 'mens-clothing')->first()->id,
                'brand_id' => $brands->where('slug', 'nike')->first()->id,
                'weight' => 0.8,
                'dimensions' => '30x20x10',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Adidas Ultraboost 22',
                'description' => 'Беговые кроссовки с технологией Boost.',
                'full_description' => 'Adidas Ultraboost 22 с технологией Boost для максимальной амортизации и возврата энергии.',
                'price' => 14990,
                'stock' => 75,
                'sku' => 'ADIDASUB22',
                'category_id' => $categories->where('slug', 'womens-clothing')->first()->id,
                'brand_id' => $brands->where('slug', 'adidas')->first()->id,
                'weight' => 0.7,
                'dimensions' => '28x18x8',
                'is_active' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($products as $productData) {
            // Генерируем slug из названия
            $productData['slug'] = Str::slug($productData['name']);
            
            // Проверяем уникальность slug
            $originalSlug = $productData['slug'];
            $counter = 1;
            while (Product::where('slug', $productData['slug'])->exists()) {
                $productData['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }

            Product::create($productData);
        }

        // Создаем дополнительные случайные товары
        Product::factory(20)->create();
    }
}
