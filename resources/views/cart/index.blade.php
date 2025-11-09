@extends('layouts.app')

@section('title', 'Корзина - Laravel Store')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ url('/') }}" class="hover:text-indigo-600">Главная</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-400">Корзина</li>
        </ol>
    </nav>

    <h1 class="text-3xl font-bold text-gray-800 mb-8">Корзина покупок</h1>

    @if($cartItems->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
            <!-- Cart Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Товары в корзине</h2>
                    <button onclick="clearCart()" 
                            class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center space-x-2">
                        <i class="fas fa-trash"></i>
                        <span>Очистить корзину</span>
                    </button>
                </div>
            </div>

            <!-- Cart Items List -->
            <div class="space-y-4">
                @foreach($cartItems as $item)
                <div class="bg-white rounded-lg shadow-sm p-6 cart-item" data-cart-id="{{ $item->id }}">
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Product Image -->
                        <div class="flex-shrink-0">
                            <a href="{{ route('catalog.product', $item->product->slug) }}" class="block">
                                <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center">
                                    @if($item->product->image)
                                        <img src="{{ Storage::url($item->product->image) }}" 
                                             alt="{{ $item->product->name }}"
                                             class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <i class="fas fa-image text-gray-300 text-xl"></i>
                                    @endif
                                </div>
                            </a>
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-1">
                                        <a href="{{ route('catalog.product', $item->product->slug) }}" 
                                           class="hover:text-indigo-600">
                                            {{ $item->product->name }}
                                        </a>
                                    </h3>
                                    @if($item->product->brand)
                                    <p class="text-sm text-gray-600">Бренд: {{ $item->product->brand->name }}</p>
                                    @endif
                                    <p class="text-sm text-gray-600">Артикул: {{ $item->product->sku }}</p>
                                </div>
                                <button onclick="removeFromCart({{ $item->id }})" 
                                        class="text-gray-400 hover:text-red-600 transition"
                                        title="Удалить из корзины">
                                    <i class="fas fa-times text-lg"></i>
                                </button>
                            </div>

                            <!-- Price and Quantity -->
                            <div class="flex justify-between items-center mt-4">
                                <div class="flex items-center space-x-4">
                                    <!-- Quantity Selector -->
                                    <div class="flex items-center border border-gray-300 rounded-lg">
                                        <button type="button" 
                                                onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                                class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-l-lg"
                                                {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                            <i class="fas fa-minus text-xs"></i>
                                        </button>
                                        <span class="px-4 py-1 text-gray-800 font-medium quantity-display">
                                            {{ $item->quantity }}
                                        </span>
                                        <button type="button" 
                                                onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                                class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-r-lg"
                                                {{ $item->quantity >= $item->product->stock ? 'disabled' : '' }}>
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                    </div>

                                    <!-- Stock Info -->
                                    <div class="text-sm text-gray-500">
                                        @if($item->product->stock > 0)
                                            В наличии: {{ $item->product->stock }} шт.
                                        @else
                                            <span class="text-red-600">Нет в наличии</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="text-right">
                                    <div class="text-lg font-bold text-indigo-600 item-total">
                                        {{ number_format($item->quantity * $item->product->price, 0, ',', ' ') }} ₽
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ number_format($item->product->price, 0, ',', ' ') }} ₽ / шт.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Итого</h2>
                
                <!-- Summary Details -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-gray-600">
                        <span>Товары ({{ $cartItems->sum('quantity') }})</span>
                        <span id="subtotal">{{ number_format($total, 0, ',', ' ') }} ₽</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Доставка</span>
                        <span class="text-green-600">Бесплатно</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between text-lg font-bold text-gray-800">
                            <span>К оплате</span>
                            <span id="total-amount">{{ number_format($total, 0, ',', ' ') }} ₽</span>
                        </div>
                    </div>
                </div>

                <!-- Checkout Button -->
                <a href="{{ route('checkout') }}" 
                   class="w-full bg-indigo-600 text-white py-4 rounded-lg hover:bg-indigo-700 transition font-semibold text-lg flex items-center justify-center mb-4">
                    <i class="fas fa-lock mr-3"></i>Перейти к оформлению
                </a>

                <!-- Additional Info -->
                <div class="text-xs text-gray-500 space-y-2">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-shield-alt text-green-500"></i>
                        <span>Безопасная оплата</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-truck text-green-500"></i>
                        <span>Бесплатная доставка от 3 000 ₽</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-undo text-green-500"></i>
                        <span>Возврат в течение 14 дней</span>
                    </div>
                </div>
            </div>

            <!-- Continue Shopping -->
            <div class="mt-6 text-center">
                <a href="{{ route('catalog') }}" 
                   class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center justify-center space-x-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Продолжить покупки</span>
                </a>
            </div>
        </div>
    </div>
    @else
    <!-- Empty Cart -->
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <div class="max-w-md mx-auto">
            <i class="fas fa-shopping-cart text-gray-300 text-6xl mb-6"></i>
            <h3 class="text-xl font-semibold text-gray-800 mb-3">Ваша корзина пуста</h3>
            <p class="text-gray-600 mb-6">
                Добавьте товары из каталога, чтобы сделать заказ
            </p>
            <a href="{{ route('catalog') }}" 
               class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition font-medium inline-flex items-center space-x-2">
                <i class="fas fa-store"></i>
                <span>Перейти в каталог</span>
            </a>
        </div>
    </div>
    @endif
</div>

<script>
// Cart management functions
function updateQuantity(cartId, newQuantity) {
    if (newQuantity < 1) return;
    
    const cartItem = document.querySelector(`.cart-item[data-cart-id="${cartId}"]`);
    const quantityDisplay = cartItem.querySelector('.quantity-display');
    const buttons = cartItem.querySelectorAll('button');
    
    // Disable buttons during update
    buttons.forEach(btn => btn.disabled = true);
    
    fetch(`/cart/update/${cartId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            quantity: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update display
            quantityDisplay.textContent = newQuantity;
            cartItem.querySelector('.item-total').textContent = 
                formatPrice(data.item_total);
            
            // Update totals
            document.getElementById('subtotal').textContent = formatPrice(data.total);
            document.getElementById('total-amount').textContent = formatPrice(data.total);
            
            // Update cart counter in header
            updateCartCounter(data.cart_count);
            
            // Re-enable buttons
            buttons.forEach(btn => btn.disabled = false);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        buttons.forEach(btn => btn.disabled = false);
    });
}

function removeFromCart(cartId) {
    if (!confirm('Удалить товар из корзины?')) return;
    
    const cartItem = document.querySelector(`.cart-item[data-cart-id="${cartId}"]`);
    
    fetch(`/cart/remove/${cartId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove item from DOM
            cartItem.style.opacity = '0';
            setTimeout(() => cartItem.remove(), 300);
            
            // Update totals
            document.getElementById('subtotal').textContent = formatPrice(data.total);
            document.getElementById('total-amount').textContent = formatPrice(data.total);
            
            // Update cart counter
            updateCartCounter(data.cart_count);
            
            // If cart is empty, reload page to show empty state
            if (data.cart_count === 0) {
                setTimeout(() => location.reload(), 500);
            }
            
            showToast('Товар удален из корзины', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Ошибка при удалении товара', 'error');
    });
}

function clearCart() {
    if (!confirm('Очистить всю корзину?')) return;
    
    fetch('/cart/clear', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove all items with animation
            document.querySelectorAll('.cart-item').forEach(item => {
                item.style.opacity = '0';
                setTimeout(() => item.remove(), 300);
            });
            
            // Update cart counter
            updateCartCounter(data.cart_count);
            
            // Reload to show empty state
            setTimeout(() => location.reload(), 500);
            
            showToast('Корзина очищена', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Ошибка при очистке корзины', 'error');
    });
}

function updateCartCounter(count) {
    const cartCounter = document.querySelector('.cart-counter');
    if (cartCounter) {
        if (count > 0) {
            cartCounter.textContent = count;
            cartCounter.classList.remove('hidden');
        } else {
            cartCounter.classList.add('hidden');
        }
    }
}

function formatPrice(amount) {
    return new Intl.NumberFormat('ru-RU').format(amount) + ' ₽';
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white font-medium z-50 transform transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => toast.classList.add('translate-x-0'), 10);
    
    // Auto remove
    setTimeout(() => {
        toast.classList.remove('translate-x-0');
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Initialize quantity buttons
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to cart items
    document.querySelectorAll('.cart-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>

<style>
.cart-item {
    transition: all 0.3s ease;
}

.quantity-display {
    min-width: 40px;
    text-align: center;
}

button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

button:disabled:hover {
    background-color: transparent !important;
}

.transition {
    transition: all 0.3s ease;
}
</style>
@endsection