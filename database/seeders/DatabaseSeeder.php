<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Отключаем проверку внешних ключей для ускорения
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Очищаем таблицы
        $this->truncateTables();

        // Запускаем сидеры
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            ReviewSeeder::class,
        ]);

        // Включаем проверку внешних ключей обратно
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Очистка таблиц перед заполнением
     */
    protected function truncateTables(): void
    {
        $tables = [
            'users',
            'categories',
            'brands',
            'products',
            'reviews',
            'carts',
            'wishlists',
            'orders',
            'order_items',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }
}
