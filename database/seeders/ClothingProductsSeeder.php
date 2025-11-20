<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClothingProductsSeeder extends Seeder
{
    public function run()
    {
        // Очищаем таблицы перед созданием
        DB::table('products')->delete();
        DB::table('categories')->delete();
        DB::table('brands')->delete();

        // Создаем категории
        $categories = [
            ['name' => 'Футболки', 'slug' => 't-shirts', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Джинсы', 'slug' => 'jeans', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Платья', 'slug' => 'dresses', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Куртки', 'slug' => 'jackets', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Обувь', 'slug' => 'shoes', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('categories')->insert($categories);

        // Создаем бренды
        $brands = [
            ['name' => 'Nike', 'slug' => 'nike', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Adidas', 'slug' => 'adidas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Zara', 'slug' => 'zara', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'H&M', 'slug' => 'h&m', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Levi\'s', 'slug' => 'levis', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('brands')->insert($brands);

        // Получаем ID категорий
        $catTshirts = DB::table('categories')->where('slug', 't-shirts')->value('id');
        $catJeans = DB::table('categories')->where('slug', 'jeans')->value('id');
        $catDresses = DB::table('categories')->where('slug', 'dresses')->value('id');
        $catJackets = DB::table('categories')->where('slug', 'jackets')->value('id');
        $catShoes = DB::table('categories')->where('slug', 'shoes')->value('id');

        // Получаем ID брендов
        $brandNike = DB::table('brands')->where('slug', 'nike')->value('id');
        $brandAdidas = DB::table('brands')->where('slug', 'adidas')->value('id');
        $brandZara = DB::table('brands')->where('slug', 'zara')->value('id');
        $brandHM = DB::table('brands')->where('slug', 'h&m')->value('id');
        $brandLevis = DB::table('brands')->where('slug', 'levis')->value('id');

        // Создаем товары с разными цветами и размерами
        $products = [
            // Футболки (разные цвета и размеры)
            [
                'name' => 'Футболка Nike Classic Черная',
                'slug' => 'nike-classic-t-shirt-black',
                'sku' => 'NK-TS-CL-BL-001',
                'description' => 'Классическая футболка Nike из 100% хлопка. Удобная и стильная повседневная одежда.',
                'price' => 2499.00,
                'stock' => 50,
                'category_id' => $catTshirts,
                'brand_id' => $brandNike,
                'color' => 'Черный',
                'size' => 'M',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Футболка Nike Classic Белая',
                'slug' => 'nike-classic-t-shirt-white',
                'sku' => 'NK-TS-CL-WH-002',
                'description' => 'Классическая футболка Nike белого цвета. Идеальна для спорта и повседневной носки.',
                'price' => 2499.00,
                'stock' => 35,
                'category_id' => $catTshirts,
                'brand_id' => $brandNike,
                'color' => 'Белый',
                'size' => 'L',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Футболка Adidas Originals',
                'slug' => 'adidas-originals-t-shirt',
                'sku' => 'AD-TS-OR-BL-001',
                'description' => 'Футболка Adidas Originals с фирменными полосками. Стиль и комфорт.',
                'price' => 2199.00,
                'stock' => 40,
                'category_id' => $catTshirts,
                'brand_id' => $brandAdidas,
                'color' => 'Синий',
                'size' => 'S',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Футболка H&M Basic',
                'slug' => 'hm-basic-t-shirt',
                'sku' => 'HM-TS-BS-GR-001',
                'description' => 'Базовая футболка H&M из мягкого хлопка. Отличное качество по доступной цене.',
                'price' => 999.00,
                'stock' => 100,
                'category_id' => $catTshirts,
                'brand_id' => $brandHM,
                'color' => 'Серый',
                'size' => 'XL',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Джинсы (разные цвета и размеры)
            [
                'name' => 'Джинсы Levi\'s 501 Синие',
                'slug' => 'levis-501-jeans-blue',
                'sku' => 'LV-501-BL-001',
                'description' => 'Классические джинсы Levi\'s 501 прямого кроя. Вечная классика и непревзойденное качество.',
                'price' => 5999.00,
                'stock' => 30,
                'category_id' => $catJeans,
                'brand_id' => $brandLevis,
                'color' => 'Синий',
                'size' => '32/32',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Джинсы Levi\'s 501 Черные',
                'slug' => 'levis-501-jeans-black',
                'sku' => 'LV-501-BK-002',
                'description' => 'Джинсы Levi\'s 501 черного цвета. Универсальные джинсы для любого образа.',
                'price' => 5999.00,
                'stock' => 25,
                'category_id' => $catJeans,
                'brand_id' => $brandLevis,
                'color' => 'Черный',
                'size' => '30/32',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Джинсы Zara Slim',
                'slug' => 'zara-slim-jeans',
                'sku' => 'ZR-JN-SL-BL-001',
                'description' => 'Слим джинсы Zara современного кроя. Подчеркивают фигуру и обеспечивают комфорт.',
                'price' => 3499.00,
                'stock' => 40,
                'category_id' => $catJeans,
                'brand_id' => $brandZara,
                'color' => 'Синий',
                'size' => '28/32',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Обувь (разные цвета и размеры)
            [
                'name' => 'Кроссовки Adidas Ultraboost Белые',
                'slug' => 'adidas-ultraboost-shoes-white',
                'sku' => 'AD-UB-WH-001',
                'description' => 'Беговые кроссовки Adidas Ultraboost с технологией Boost. Максимальный комфорт и амортизация.',
                'price' => 12999.00,
                'stock' => 25,
                'category_id' => $catShoes,
                'brand_id' => $brandAdidas,
                'color' => 'Белый',
                'size' => '42',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Кроссовки Adidas Ultraboost Черные',
                'slug' => 'adidas-ultraboost-shoes-black',
                'sku' => 'AD-UB-BK-002',
                'description' => 'Беговые кроссовки Adidas Ultraboost черного цвета. Стиль и технологии для бега.',
                'price' => 12999.00,
                'stock' => 20,
                'category_id' => $catShoes,
                'brand_id' => $brandAdidas,
                'color' => 'Черный',
                'size' => '43',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Кроссовки Nike Air Force 1',
                'slug' => 'nike-air-force-1',
                'sku' => 'NK-AF1-WH-001',
                'description' => 'Культовые кроссовки Nike Air Force 1 белого цвета. Уличная классика.',
                'price' => 8999.00,
                'stock' => 35,
                'category_id' => $catShoes,
                'brand_id' => $brandNike,
                'color' => 'Белый',
                'size' => '41',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Кроссовки Nike Air Max',
                'slug' => 'nike-air-max-shoes',
                'sku' => 'NK-AM-RD-001',
                'description' => 'Кроссовки Nike Air Max с видимой амортизацией. Яркий дизайн и комфорт.',
                'price' => 10999.00,
                'stock' => 15,
                'category_id' => $catShoes,
                'brand_id' => $brandNike,
                'color' => 'Красный',
                'size' => '44',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Платья (разные цвета и размеры)
            [
                'name' => 'Платье Zara Вечернее',
                'slug' => 'zara-evening-dress',
                'sku' => 'ZR-DR-EV-BL-001',
                'description' => 'Элегантное вечернее платье от Zara. Идеально для торжественных мероприятий.',
                'price' => 4599.00,
                'stock' => 15,
                'category_id' => $catDresses,
                'brand_id' => $brandZara,
                'color' => 'Черный',
                'size' => 'S',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Платье H&M Летнее',
                'slug' => 'hm-summer-dress',
                'sku' => 'HM-DR-SU-FL-001',
                'description' => 'Легкое летнее платье H&M с цветочным принтом. Невероятно комфортное и стильное.',
                'price' => 1999.00,
                'stock' => 28,
                'category_id' => $catDresses,
                'brand_id' => $brandHM,
                'color' => 'Разноцветный',
                'size' => 'M',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Куртки (разные цвета и размеры)
            [
                'name' => 'Куртка Nike Windrunner',
                'slug' => 'nike-windrunner-jacket',
                'sku' => 'NK-JK-WN-BL-001',
                'description' => 'Ветровка Nike Windrunner с защитой от ветра и воды. Легкая и функциональная.',
                'price' => 7999.00,
                'stock' => 20,
                'category_id' => $catJackets,
                'brand_id' => $brandNike,
                'color' => 'Синий',
                'size' => 'L',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Куртка Adidas Track',
                'slug' => 'adidas-track-jacket',
                'sku' => 'AD-JK-TR-BL-001',
                'description' => 'Спортивная куртка Adidas в стиле ретро. Мягкий материал и стильный дизайн.',
                'price' => 4999.00,
                'stock' => 22,
                'category_id' => $catJackets,
                'brand_id' => $brandAdidas,
                'color' => 'Синий',
                'size' => 'M',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('products')->insert($products);

        $this->command->info('Успешно создано 15 товаров с разными цветами и размерами для тестирования фильтров!');
    }
}