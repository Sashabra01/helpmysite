<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог товаров</title>
    <style>
        /* Существующие стили остаются */
        .filter-btn {
            cursor: pointer;
            padding: 8px 15px;
            margin: 5px;
            border: 2px solid #ddd;
            border-radius: 5px;
            display: inline-block;
        }
        .filter-btn.active {
            background-color: #007bff;
            color: white;
            border-color: #0056b3;
        }
        .product-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            margin: 10px;
            display: inline-block;
            width: 200px;
        }
        .hidden {
            display: none !important;
        }
        
        /* Стили для корзины */
        .cart-header {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }

        #cart-sidebar {
            position: fixed;
            right: 0;
            top: 0;
            width: 300px;
            height: 100vh;
            background: white;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            transition: transform 0.3s;
        }

        .cart-hidden {
            transform: translateX(100%);
        }

        .cart-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .add-to-cart {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .add-to-cart:hover {
            background: #218838;
        }

        /* Новые стили для поиска */
        .search-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .search-input {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 16px;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #007bff;
        }
        
        .search-stats {
            color: #6c757d;
            font-size: 14px;
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        
        .quick-filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        
        .quick-filter {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 5px 12px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .quick-filter:hover {
            background: #e9ecef;
        }
    </style>
</head>
<body>
    <!-- Хедер с корзиной -->
    <div class="cart-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                🛒 Корзина: <span id="cart-count">0</span> товаров | 
                <span id="cart-total">0</span> руб.
                <button onclick="toggleCart()" style="margin-left: 15px;">Показать корзину</button>
            </div>
            <div>
                <a href="/profile/orders" style="background: #6c757d; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; margin-right: 10px;">
                    📋 Мои заказы
                </a>
                <a href="/admin/orders" style="background: #dc3545; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; margin-right: 10px;">
                    ⚙️ Админка
                </a>
                <a href="/admin/products" style="background: #17a2b8; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none;">
                    🛍️ Управление товарами
                </a>
            </div>
        </div>
    </div>

    <!-- Боковая панель корзины -->
    <div id="cart-sidebar" class="cart-hidden">
        <h3>Ваша корзина</h3>
        <div id="cart-items"></div>
        <div id="cart-empty">Корзина пуста</div>
        
        <!-- Форма оформления заказа -->
        <div id="checkout-form" style="display: none;">
            <h4>Оформление заказа</h4>
            <form id="order-form">
                <input type="text" name="customer_name" placeholder="Ваше имя" required>
                <input type="email" name="customer_email" placeholder="Email" required>
                <input type="tel" name="customer_phone" placeholder="Телефон" required>
                <textarea name="customer_address" placeholder="Адрес доставки" required></textarea>
                <button type="submit">Подтвердить заказ</button>
            </form>
        </div>
        
        <button id="checkout-btn">Оформить заказ</button>
        <button id="back-to-cart" style="display: none;">← Назад к корзине</button>
    </div>

    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <h1>🛍️ Каталог товаров</h1>
        
        <!-- ПОИСК ТОВАРОВ -->
        <div class="search-container">
            <div class="search-box">
                <input type="text" 
                       id="search-input" 
                       class="search-input" 
                       placeholder="Поиск товаров по названию..."
                       onkeyup="searchProducts()">
                <button onclick="clearSearch()" style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; background: #f8f9fa;">
                    Очистить
                </button>
            </div>
            
            <div class="quick-filters">
                <div class="quick-filter" onclick="setSearch('футболка')">Футболки</div>
                <div class="quick-filter" onclick="setSearch('черный')">Черные</div>
                <div class="quick-filter" onclick="setSearch('белый')">Белые</div>
                <div class="quick-filter" onclick="setSearch('хлопок')">Хлопок</div>
                <div class="quick-filter" onclick="setSearch('синтетика')">Синтетика</div>
            </div>
            
            <div class="search-stats" id="search-stats">
                Всего товаров: <span id="total-products">0</span> | 
                Показано: <span id="visible-products">0</span>
            </div>
        </div>

        <div style="display: flex; gap: 20px;">
            <!-- Фильтры -->
            <div class="filters" style="flex: 0 0 250px;">
                <h3>Фильтры</h3>
                
                <div class="filter-group" style="margin-bottom: 20px; padding: 15px; border: 1px solid #eee; border-radius: 8px;">
                    <strong>Цвет</strong><br>
                    <div class="filter-btn" data-filter="color" data-value="черный">Чёрный</div>
                    <div class="filter-btn" data-filter="color" data-value="белый">Белый</div>
                    <div class="filter-btn" data-filter="color" data-value="синий">Синий</div>
                    <div class="filter-btn" data-filter="color" data-value="красный">Красный</div>
                    <div class="filter-btn" data-filter="color" data-value="зеленый">Зеленый</div>
                </div>

                <div class="filter-group" style="margin-bottom: 20px; padding: 15px; border: 1px solid #eee; border-radius: 8px;">
                    <strong>Материал</strong><br>
                    <div class="filter-btn" data-filter="material" data-value="хлопок">Хлопок</div>
                    <div class="filter-btn" data-filter="material" data-value="синтетика">Синтетика</div>
                </div>

                <div class="filter-group" style="margin-bottom: 20px; padding: 15px; border: 1px solid #eee; border-radius: 8px;">
                    <strong>Размер</strong><br>
                    <div class="filter-btn" data-filter="size" data-value="S">S</div>
                    <div class="filter-btn" data-filter="size" data-value="M">M</div>
                    <div class="filter-btn" data-filter="size" data-value="L">L</div>
                </div>
            </div>

            <!-- Товары -->
            <div class="products-grid" style="flex: 1;">
                <div id="products-container">
                    @foreach($products as $product)
                    <div class="product-card" 
                         data-color="{{ $product->color }}" 
                         data-material="{{ $product->material }}" 
                         data-size="{{ $product->size }}"
                         data-name="{{ $product->name }}">
                        <h4>{{ $product->name }}</h4>
                        <p><strong>Цвет:</strong> {{ $product->color }}</p>
                        <p><strong>Материал:</strong> {{ $product->material }}</p>
                        <p><strong>Размер:</strong> {{ $product->size }}</p>
                        <p><strong>Цена:</strong> {{ number_format($product->price, 0, ',', ' ') }} руб.</p>
                        <button class="add-to-cart" 
                                data-product-id="{{ $product->id }}" 
                                data-product-name="{{ $product->name }}" 
                                data-product-price="{{ $product->price }}">
                            В корзину
                        </button>
                    </div>
                    @endforeach
                </div>
                
                @if($products->count() === 0)
                <div id="no-results" class="no-results">
                    <h3>😔 Товары не найдены</h3>
                    <p>В каталоге пока нет товаров</p>
                    <a href="/admin/products/create" style="display: inline-block; background: #007bff; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; margin-top: 10px;">
                        ➕ Добавить первый товар
                    </a>
                </div>
                @else
                <div id="no-results" class="no-results" style="display: none;">
                    <h3>😔 Товары не найдены</h3>
                    <p>Попробуйте изменить поисковый запрос или сбросить фильтры</p>
                    <button onclick="clearSearch()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; margin-top: 10px;">
                        Показать все товары
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // ================== ПОИСК ТОВАРОВ ==================
        let searchTerm = '';
        
        function searchProducts() {
            const searchInput = document.getElementById('search-input');
            searchTerm = searchInput.value.toLowerCase().trim();
            
            applyFiltersAndSearch();
        }
        
        function setSearch(term) {
            const searchInput = document.getElementById('search-input');
            searchInput.value = term;
            searchTerm = term.toLowerCase();
            applyFiltersAndSearch();
        }
        
        function clearSearch() {
            const searchInput = document.getElementById('search-input');
            searchInput.value = '';
            searchTerm = '';
            applyFiltersAndSearch();
        }
        
        // ================== ОБНОВЛЕННЫЕ ФИЛЬТРЫ С ПОИСКОМ ==================
        const filterButtons = document.querySelectorAll('.filter-btn');
        const productCards = document.querySelectorAll('.product-card');
        let activeFilters = {
            color: [],
            material: [],
            size: []
        };

        // Обновляем функцию применения фильтров
        function applyFiltersAndSearch() {
            let visibleCount = 0;
            const totalProducts = productCards.length;
            
            productCards.forEach(card => {
                const cardColor = card.getAttribute('data-color');
                const cardMaterial = card.getAttribute('data-material');
                const cardSize = card.getAttribute('data-size');
                const cardName = card.getAttribute('data-name').toLowerCase();
                
                // Проверяем фильтры
                const matchesColor = activeFilters.color.length === 0 || activeFilters.color.includes(cardColor);
                const matchesMaterial = activeFilters.material.length === 0 || activeFilters.material.includes(cardMaterial);
                const matchesSize = activeFilters.size.length === 0 || activeFilters.size.includes(cardSize);
                
                // Проверяем поиск
                const matchesSearch = searchTerm === '' || cardName.includes(searchTerm);
                
                // Показываем только если все условия выполнены
                const shouldShow = matchesColor && matchesMaterial && matchesSize && matchesSearch;
                
                if (shouldShow) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });
            
            // Обновляем статистику
            updateSearchStats(totalProducts, visibleCount);
            
            // Показываем/скрываем сообщение "нет результатов"
            const noResults = document.getElementById('no-results');
            if (visibleCount === 0) {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        }
        
        function updateSearchStats(total, visible) {
            document.getElementById('total-products').textContent = total;
            document.getElementById('visible-products').textContent = visible;
        }

        // Инициализация фильтров
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filterType = this.getAttribute('data-filter');
                const filterValue = this.getAttribute('data-value');
                
                this.classList.toggle('active');
                
                if (this.classList.contains('active')) {
                    if (!activeFilters[filterType].includes(filterValue)) {
                        activeFilters[filterType].push(filterValue);
                    }
                } else {
                    activeFilters[filterType] = activeFilters[filterType].filter(v => v !== filterValue);
                }
                
                applyFiltersAndSearch();
            });
        });

        // ================== КОРЗИНА ==================
        let cart = [];

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-to-cart')) {
                const product = {
                    id: e.target.dataset.productId,
                    name: e.target.dataset.productName,
                    price: parseInt(e.target.dataset.productPrice),
                    quantity: 1
                };
                addToCart(product);
            }
        });

        function addToCart(product) {
            const existingItem = cart.find(item => item.id === product.id);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push(product);
            }
            
            updateCartDisplay();
            showNotification('Товар добавлен в корзину!');
        }

        function updateCartDisplay() {
            const cartCount = cart.reduce((sum, item) => sum + item.quantity, 0);
            const cartTotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            document.getElementById('cart-count').textContent = cartCount;
            document.getElementById('cart-total').textContent = cartTotal;
            
            const cartItems = document.getElementById('cart-items');
            const cartEmpty = document.getElementById('cart-empty');
            
            if (cart.length === 0) {
                cartEmpty.style.display = 'block';
                cartItems.innerHTML = '';
            } else {
                cartEmpty.style.display = 'none';
                cartItems.innerHTML = cart.map(item => `
                    <div class="cart-item">
                        <span>${item.name}</span>
                        <div>
                            ${item.price} руб. × ${item.quantity}
                            <button onclick="removeFromCart('${item.id}')" style="margin-left: 10px; background: none; border: none; cursor: pointer;">❌</button>
                        </div>
                    </div>
                `).join('');
            }
        }

        window.removeFromCart = function(productId) {
            cart = cart.filter(item => item.id !== productId);
            updateCartDisplay();
        }

        window.toggleCart = function() {
            const sidebar = document.getElementById('cart-sidebar');
            sidebar.classList.toggle('cart-hidden');
        }

        function showNotification(message) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #28a745;
                color: white;
                padding: 15px 20px;
                border-radius: 5px;
                z-index: 1000;
                animation: slideIn 0.3s ease;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 2000);
        }

        // Оформление заказа
        document.getElementById('checkout-btn').addEventListener('click', function() {
            if (cart.length === 0) {
                showNotification('Корзина пуста!');
                return;
            }
            
            document.getElementById('checkout-form').style.display = 'block';
            document.getElementById('checkout-btn').style.display = 'none';
            document.getElementById('back-to-cart').style.display = 'inline-block';
            document.getElementById('cart-items').style.display = 'none';
        });

        document.getElementById('back-to-cart').addEventListener('click', function() {
            document.getElementById('checkout-form').style.display = 'none';
            document.getElementById('checkout-btn').style.display = 'inline-block';
            document.getElementById('back-to-cart').style.display = 'none';
            document.getElementById('cart-items').style.display = 'block';
        });

        document.getElementById('order-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const orderData = {
                customer_name: formData.get('customer_name'),
                customer_email: formData.get('customer_email'),
                customer_phone: formData.get('customer_phone'),
                customer_address: formData.get('customer_address'),
                items: cart,
                total_amount: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0)
            };
            
            fetch('/order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 'demo-token'
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Заказ №' + data.order_id + ' успешно оформлен!');
                    cart = [];
                    updateCartDisplay();
                    toggleCart();
                    document.getElementById('order-form').reset();
                    document.getElementById('back-to-cart').click();
                } else {
                    showNotification('Ошибка при оформлении заказа');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Ошибка при отправке заказа');
            });
        });

        // Инициализация
        document.addEventListener('DOMContentLoaded', function() {
            updateSearchStats(productCards.length, productCards.length);
            
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>