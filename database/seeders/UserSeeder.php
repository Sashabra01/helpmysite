<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Создаем администратора
        User::create([
            'name' => 'Администратор',
            'email' => 'admin@laravel-store.ru',
            'password' => Hash::make('password'),
            'phone' => '+7 (999) 123-45-67',
            'address' => 'г. Москва, ул. Примерная, 123',
            'city' => 'Москва',
            'postal_code' => '123456',
            'email_verified_at' => now(),
        ]);

        // Создаем тестовых пользователей
        User::create([
            'name' => 'Иван Петров',
            'email' => 'ivan@example.com',
            'password' => Hash::make('password'),
            'phone' => '+7 (999) 111-22-33',
            'address' => 'г. Москва, ул. Тестовая, 45',
            'city' => 'Москва',
            'postal_code' => '123457',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Мария Сидорова',
            'email' => 'maria@example.com',
            'password' => Hash::make('password'),
            'phone' => '+7 (999) 444-55-66',
            'address' => 'г. Санкт-Петербург, ул. Образцовая, 67',
            'city' => 'Санкт-Петербург',
            'postal_code' => '198456',
            'email_verified_at' => now(),
        ]);

        // Создаем несколько случайных пользователей
        User::factory(10)->create();
    }
}
