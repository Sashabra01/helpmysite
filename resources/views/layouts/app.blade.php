<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Store')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        .dropdown:hover .dropdown-menu {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Top Bar -->
            <div class="border-b border-gray-200">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ url('/') }}" class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-store text-white text-lg"></i>
                            </div>
                            <span class="text-xl font-bold text-gray-800">Laravel Store</span>
                        </a>
                    </div>

                    <!-- Search Bar -->
                    <div class="flex-1 max-w-2xl mx-8">
                        <form action="{{ route('search') }}" method="GET" class="relative">
                            <input type="text" 
                                   name="q" 
                                   placeholder="Поиск товаров..." 
                                   class="w-full pl-4 pr-12 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                   value="{{ request('q') }}">
                            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-indigo-600 transition">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- User Actions -->
                    <div class="flex items-center space-x-6">
                        <!-- Cart -->
                        <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-indigo-600 transition">
                            <i class="fas fa-shopping-cart text-lg"></i>
                            @auth
                                @if(Auth::user()->cart_count > 0)
                                    <span class="absolute -top-2 -right-2 bg-indigo-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                        {{ Auth::user()->cart_count }}
                                    </span>
                                @endif
                            @endauth
                        </a>

                        <!-- Wishlist -->
                        <a href="{{ route('wishlist.index') }}" class="text-gray-700 hover:text-indigo-600 transition">
                            <i class="far fa-heart text-lg"></i>
                        </a>

                        <!-- User Menu -->
                        @auth
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-indigo-600 transition">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-indigo-600 text-sm"></i>
                                    </div>
                                    <span class="font-medium">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="open" 
                                     @click.away="open = false" 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50 border border-gray-200">
                                     <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
    <i class="fas fa-user-circle mr-3"></i>Личный кабинет
</a>
                                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                        <i class="fas fa-user-circle mr-3"></i>Профиль
                                    </a>
                                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                        <i class="fas fa-shopping-bag mr-3"></i>Мои заказы
                                    </a>
                                    <a href="{{ route('wishlist.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                        <i class="far fa-heart mr-3"></i>Избранное
                                    </a>
                                    <div class="border-t border-gray-200 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                            <i class="fas fa-sign-out-alt mr-3"></i>Выйти
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium">
                                    Войти
                                </a>
                                <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                                    Регистрация
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex justify-between items-center h-12">
                <!-- Categories Menu -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-indigo-600 transition font-medium">
                        <i class="fas fa-bars"></i>
                        <span>Категории</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>

                    <!-- Categories Dropdown -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute left-0 mt-2 w-64 bg-white rounded-lg shadow-lg py-2 z-50 border border-gray-200">
                        @foreach($categories ?? [] as $category)
                            <a href="{{ url('/catalog/category/' . $category->slug) }}" 
                               class="flex items-center justify-between px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                <span>{{ $category->name }}</span>
                                <span class="text-gray-400 text-sm">{{ $category->products_count ?? 0 }}</span>
                            </a>
                        @endforeach
                        <div class="border-t border-gray-200 my-1"></div>
                        <a href="{{ url('/catalog') }}" class="block px-4 py-2 text-indigo-600 hover:bg-gray-50 transition font-medium">
                            <i class="fas fa-th-large mr-3"></i>Все категории
                        </a>
                    </div>
                </div>

                <!-- Main Navigation -->
                <div class="flex items-center space-x-8">
                    <a href="{{ url('/') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium">Главная</a>
                    <a href="{{ url('/catalog') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium">Каталог</a>
                    <a href="{{ url('/about') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium">О нас</a>
                    <a href="{{ url('/contact') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium">Контакты</a>
                </div>

                <!-- Promo Info -->
                <div class="text-sm text-gray-600">
                    <i class="fas fa-truck mr-2"></i>Бесплатная доставка от 3000 ₽
                </div>
            </nav>
        </div>
    </header>

    <!-- Wishlist -->
<a href="{{ route('wishlist.index') }}" class="relative text-gray-700 hover:text-indigo-600 transition">
    <i class="far fa-heart text-lg"></i>
    @auth
        @if(Auth::user()->wishlist_count > 0)
            <span class="absolute -top-2 -right-2 bg-pink-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center wishlist-counter">
                {{ Auth::user()->wishlist_count }}
            </span>
        @endif
    @endauth
</a>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    <span>Пожалуйста, исправьте следующие ошибки:</span>
                </div>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="min-h-screen py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-store text-white text-lg"></i>
                        </div>
                        <span class="text-xl font-bold">Laravel Store</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Интернет-магазин качественных товаров по доступным ценам. 
                        Мы заботимся о наших клиентах.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-vk text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-telegram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="font-semibold text-lg mb-4">Магазин</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/catalog') }}" class="text-gray-400 hover:text-white transition">Каталог товаров</a></li>
                        <li><a href="{{ url('/about') }}" class="text-gray-400 hover:text-white transition">О нас</a></li>
                        <li><a href="{{ url('/contact') }}" class="text-gray-400 hover:text-white transition">Контакты</a></li>
                        <li><a href="{{ url('/delivery') }}" class="text-gray-400 hover:text-white transition">Доставка и оплата</a></li>
                    </ul>
                </div>

                <!-- Customer Service -->
                <div>
                    <h3 class="font-semibold text-lg mb-4">Помощь</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/faq') }}" class="text-gray-400 hover:text-white transition">FAQ</a></li>
                        <li><a href="{{ url('/returns') }}" class="text-gray-400 hover:text-white transition">Возврат товара</a></li>
                        <li><a href="{{ url('/guarantee') }}" class="text-gray-400 hover:text-white transition">Гарантия</a></li>
                        <li><a href="{{ url('/support') }}" class="text-gray-400 hover:text-white transition">Техподдержка</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="font-semibold text-lg mb-4">Контакты</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-phone text-gray-400"></i>
                            <span class="text-gray-400">8 (800) 123-45-67</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-envelope text-gray-400"></i>
                            <span class="text-gray-400">info@laravel-store.ru</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-map-marker-alt text-gray-400"></i>
                            <span class="text-gray-400">г. Москва, ул. Примерная, 123</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-clock text-gray-400"></i>
                            <span class="text-gray-400">Ежедневно 9:00-21:00</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-700 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-400 text-sm mb-4 md:mb-0">
                    &copy; 2024 Laravel Store. Все права защищены.
                </div>
                <div class="flex space-x-6 text-sm text-gray-400">
                    <a href="{{ url('/privacy') }}" class="hover:text-white transition">Политика конфиденциальности</a>
                    <a href="{{ url('/terms') }}" class="hover:text-white transition">Пользовательское соглашение</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile menu toggle (if needed)
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Cart counter update
        function updateCartCounter(count) {
            const counter = document.querySelector('.cart-counter');
            if (counter) {
                counter.textContent = count;
                counter.classList.toggle('hidden', count === 0);
            }
        }

        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.querySelector('form[action*="search"]');
            if (searchForm) {
                const searchInput = searchForm.querySelector('input[name="q"]');
                searchInput.addEventListener('input', function() {
                    // Можно добавить автодополнение здесь
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
