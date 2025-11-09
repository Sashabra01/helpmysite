<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Электроника',
                'slug' => 'electronics',
                'description' => 'Современная электроника и гаджеты',
                'is_active' => true,
                'sort_order' => 1,
                'children' => [
                    [
                        'name' => 'Смартфоны',
                        'slug' => 'smartphones',
                        'description' => 'Мобильные телефоны и смартфоны',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Ноутбуки',
                        'slug' => 'laptops',
                        'description' => 'Портативные компьютеры',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Планшеты',
                        'slug' => 'tablets',
                        'description' => 'Планшетные компьютеры',
                        'is_active' => true,
                        'sort_order' => 3,
                    ],
                ]
            ],
            [
                'name' => 'Одежда',
                'slug' => 'clothing',
                'description' => 'Модная одежда для всех',
                'is_active' => true,
                'sort_order' => 2,
                'children' => [
                    [
                        'name' => 'Мужская одежда',
                        'slug' => 'mens-clothing',
                        'description' => 'Одежда для мужчин',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Женская одежда',
                        'slug' => 'womens-clothing',
                        'description' => 'Одежда для женщин',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                ]
            ],
            [
                'name' => 'Дом и сад',
                'slug' => 'home-garden',
                'description' => 'Товары для дома и сада',
                'is_active' => true,
                'sort_order' => 3,
                'children' => [
                    [
                        'name' => 'Мебель',
                        'slug' => 'furniture',
                        'description' => 'Мебель для дома и офиса',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Текстиль',
                        'slug' => 'textiles',
                        'description' => 'Постельное белье, шторы, ковры',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = Category::create($categoryData);

            foreach ($children as $childData) {
                $childData['parent_id'] = $category->id;
                Category::create($childData);
            }
        }
    }
}
