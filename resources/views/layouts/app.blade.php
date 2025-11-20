<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel Store') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        indigo: {
                            50: '#eef2ff',
                            100: '#e0e7ff', 
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .cart-counter, .wishlist-counter {
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: -5px;
            right: -5px;
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .transition {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ url('/') }}" class="text-xl font-bold text-indigo-600">
                                <i class="fas fa-store mr-2"></i>Laravel Store
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ url('/') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition">
                                Главная
                            </a>
                            <a href="{{ route('catalog') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition">
                                Каталог
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Cart -->
                        <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-indigo-600 transition">
                            <i class="fas fa-shopping-cart text-lg"></i>
                            @php
                                $cartCount = \App\Models\Cart::where('user_id', auth()->id() ?? 1)->count();
                            @endphp
                            <span class="cart-counter {{ $cartCount > 0 ? '' : 'hidden' }}">
                                {{ $cartCount }}
                            </span>
                        </a>

                        <!-- Auth Links -->
                        @auth
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 text-sm text-gray-700 hover:text-gray-900 focus:outline-none transition">
                                    <span>{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>

                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50" style="display: none;">
                                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                        <i class="fas fa-shopping-bag mr-2"></i>Мои заказы
                                    </a>
                                    <a href="{{ route('wishlist.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                        <i class="fas fa-heart mr-2"></i>Избранное
                                    </a>
                                    <hr class="my-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Выйти
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 transition">
                                    Войти
                                </a>
                                <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition">
                                    Регистрация
                                </a>
                                <!-- Быстрая авторизация для тестирования -->
                                <a href="{{ url('/auto-login') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition">
                                    Быстрый вход (тест)
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t mt-12">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-500 text-sm">
                    <p>Laravel Store - Дипломный проект</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    
    <script>
        // Обновление счетчика корзины
        function updateCartCounter(count) {
            const counter = document.querySelector('.cart-counter');
            if (counter) {
                counter.textContent = count;
                if (count > 0) {
                    counter.classList.remove('hidden');
                } else {
                    counter.classList.add('hidden');
                }
            }
        }

        // Глобальный обработчик для добавления в корзину
        document.addEventListener('DOMContentLoaded', function() {
            // Обработчик для всех форм добавления в корзину
            document.addEventListener('submit', function(e) {
                if (e.target.closest('form[action*="cart/add"]')) {
                    e.preventDefault();
                    
                    const form = e.target.closest('form');
                    const formData = new FormData(form);
                    
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Cart response:', data);
                        if (data.success) {
                            updateCartCounter(data.cart_count);
                            // Показываем уведомление
                            alert('Товар добавлен в корзину! В корзине: ' + data.cart_count + ' товаров');
                        } else {
                            alert(data.message || 'Ошибка при добавлении в корзину');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ошибка при добавлении в корзину');
                    });
                }
            });
        });
    </script>
</body>
</html>
