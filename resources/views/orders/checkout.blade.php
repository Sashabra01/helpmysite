@extends('layouts.app')

@section('title', 'Оформление заказа - Laravel Store')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ url('/') }}" class="hover:text-indigo-600">Главная</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('cart.index') }}" class="hover:text-indigo-600">Корзина</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-400">Оформление заказа</li>
        </ol>
    </nav>

    <h1 class="text-3xl font-bold text-gray-800 mb-8">Оформление заказа</h1>

    <form id="checkout-form" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf
        
        <!-- Left Column - Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user-circle mr-3 text-indigo-600"></i>
                    Контактная информация
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">Имя *</label>
                        <input type="text" 
                               id="first_name" 
                               name="first_name" 
                               value="{{ old('first_name', $user->name ? explode(' ', $user->name)[0] ?? '' : '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                        <span class="text-red-500 text-sm hidden" id="first_name_error"></span>
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Фамилия *</label>
                        <input type="text" 
                               id="last_name" 
                               name="last_name" 
                               value="{{ old('last_name', $user->name ? explode(' ', $user->name)[1] ?? '' : '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                        <span class="text-red-500 text-sm hidden" id="last_name_error"></span>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                        <span class="text-red-500 text-sm hidden" id="email_error"></span>
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Телефон *</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $user->phone) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="+7 (999) 999-99-99"
                               required>
                        <span class="text-red-500 text-sm hidden" id="phone_error"></span>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-truck mr-3 text-indigo-600"></i>
                    Адрес доставки
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Адрес *</label>
                        <input type="text" 
                               id="address" 
                               name="address" 
                               value="{{ old('address', $user->address) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Улица, дом, квартира"
                               required>
                        <span class="text-red-500 text-sm hidden" id="address_error"></span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">Город *</label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city', $user->city) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                            <span class="text-red-500 text-sm hidden" id="city_error"></span>
                        </div>
                        
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Индекс *</label>
                            <input type="text" 
                                   id="postal_code" 
                                   name="postal_code" 
                                   value="{{ old('postal_code', $user->postal_code) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                            <span class="text-red-500 text-sm hidden" id="postal_code_error"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Method -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-shipping-fast mr-3 text-indigo-600"></i>
                    Способ доставки
                </h2>
                
                <div class="space-y-3">
                    <label class="flex items-start space-x-3 p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-400 transition shipping-method">
                        <input type="radio" name="shipping_method" value="delivery" class="mt-1 text-indigo-600 focus:ring-indigo-500" checked>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-medium text-gray-800">Курьерская доставка</span>
                                    <p class="text-sm text-gray-600 mt-1">Доставка курьером по указанному адресу</p>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-indigo-600" id="shipping-cost">300 ₽</span>
                                    <p class="text-sm text-green-600" id="free-shipping-message">
                                        Бесплатно при заказе от 3 000 ₽
                                    </p>
                                </div>
                            </div>
                        </div>
                    </label>
                    
                    <label class="flex items-start space-x-3 p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-400 transition shipping-method">
                        <input type="radio" name="shipping_method" value="pickup" class="mt-1 text-indigo-600 focus:ring-indigo-500">
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-medium text-gray-800">Самовывоз</span>
                                    <p class="text-sm text-gray-600 mt-1">г. Москва, ул. Примерная, 123</p>
                                    <p class="text-xs text-gray-500 mt-1">Пн-Пт: 9:00-21:00, Сб-Вс: 10:00-18:00</p>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-green-600">Бесплатно</span>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-credit-card mr-3 text-indigo-600"></i>
                    Способ оплаты
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex flex-col items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-400 transition payment-method">
                        <input type="radio" name="payment_method" value="card" class="text-indigo-600 focus:ring-indigo-500" checked>
                        <i class="fas fa-credit-card text-3xl text-indigo-600 mt-3 mb-2"></i>
                        <span class="font-medium text-gray-800">Банковская карта</span>
                        <p class="text-xs text-gray-600 text-center mt-1">Оплата онлайн</p>
                    </label>
                    
                    <label class="flex flex-col items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-400 transition payment-method">
                        <input type="radio" name="payment_method" value="online" class="text-indigo-600 focus:ring-indigo-500">
                        <i class="fas fa-mobile-alt text-3xl text-indigo-600 mt-3 mb-2"></i>
                        <span class="font-medium text-gray-800">Онлайн-платеж</span>
                        <p class="text-xs text-gray-600 text-center mt-1">ЮMoney, СБП и др.</p>
                    </label>
                    
                    <label class="flex flex-col items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-400 transition payment-method">
                        <input type="radio" name="payment_method" value="cash" class="text-indigo-600 focus:ring-indigo-500">
                        <i class="fas fa-money-bill-wave text-3xl text-indigo-600 mt-3 mb-2"></i>
                        <span class="font-medium text-gray-800">Наличными</span>
                        <p class="text-xs text-gray-600 text-center mt-1">При получении</p>
                    </label>
                </div>
            </div>

            <!-- Order Notes -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-sticky-note mr-3 text-indigo-600"></i>
                    Примечание к заказу
                </h2>
                
                <textarea name="notes" 
                          rows="3" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="Комментарии к заказу (необязательно)"></textarea>
            </div>
        </div>

        <!-- Right Column - Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Ваш заказ</h2>
                
                <!-- Order Items -->
                <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                    @foreach($cartItems as $item)
                    <div class="flex items-center space-x-3 py-2 border-b border-gray-100">
                        <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center flex-shrink-0">
                            @if($item->product->image)
                                <img src="{{ Storage::url($item->product->image) }}" 
                                     alt="{{ $item->product->name }}"
                                     class="w-10 h-10 object-cover rounded">
                            @else
                                <i class="fas fa-image text-gray-300"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-800 truncate">{{ $item->product->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $item->quantity }} × {{ number_format($item->product->price, 0, ',', ' ') }} ₽</p>
                        </div>
                        <div class="text-sm font-semibold text-gray-800">
                            {{ number_format($item->quantity * $item->product->price, 0, ',', ' ') }} ₽
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Order Summary -->
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-gray-600">
                        <span>Товары ({{ $cartItems->sum('quantity') }})</span>
                        <span id="subtotal">{{ number_format($total, 0, ',', ' ') }} ₽</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Доставка</span>
                        <span id="shipping-cost-display">300 ₽</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2">
                        <div class="flex justify-between text-lg font-bold text-gray-800">
                            <span>Итого</span>
                            <span id="total-amount">{{ number_format($total + 300, 0, ',', ' ') }} ₽</span>
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="mb-4">
                    <label class="flex items-start space-x-3">
                        <input type="checkbox" 
                               name="terms" 
                               class="mt-1 text-indigo-600 focus:ring-indigo-500"
                               required>
                        <span class="text-sm text-gray-600">
                            Я соглашаюсь с 
                            <a href="{{ url('/terms') }}" class="text-indigo-600 hover:text-indigo-800">условиями использования</a> 
                            и 
                            <a href="{{ url('/privacy') }}" class="text-indigo-600 hover:text-indigo-800">политикой конфиденциальности</a>
                        </span>
                    </label>
                    <span class="text-red-500 text-sm hidden" id="terms_error"></span>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        id="submit-order"
                        class="w-full bg-indigo-600 text-white py-4 rounded-lg hover:bg-indigo-700 transition font-semibold text-lg flex items-center justify-center">
                    <i class="fas fa-lock mr-3"></i>Оформить заказ
                </button>

                <!-- Security Info -->
                <div class="mt-4 text-center">
                    <div class="flex items-center justify-center space-x-2 text-xs text-gray-500">
                        <i class="fas fa-shield-alt text-green-500"></i>
                        <span>Безопасная оплата • SSL защита</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Оформление заказа</h3>
        <p class="text-gray-600">Пожалуйста, подождите...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkout-form');
    const submitBtn = document.getElementById('submit-order');
    const loadingOverlay = document.getElementById('loading-overlay');
    
    // Calculate initial shipping
    calculateShipping();
    
    // Shipping method change
    document.querySelectorAll('input[name="shipping_method"]').forEach(radio => {
        radio.addEventListener('change', calculateShipping);
    });
    
    // City input change (debounced)
    let cityTimeout;
    document.getElementById('city').addEventListener('input', function() {
        clearTimeout(cityTimeout);
        cityTimeout = setTimeout(calculateShipping, 500);
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        submitOrder();
    });
    
    // Style selected shipping methods
    document.querySelectorAll('.shipping-method').forEach(label => {
        const radio = label.querySelector('input[type="radio"]');
        
        radio.addEventListener('change', function() {
            document.querySelectorAll('.shipping-method').forEach(l => {
                l.classList.remove('border-indigo-500', 'bg-indigo-50');
            });
            
            if (this.checked) {
                label.classList.add('border-indigo-500', 'bg-indigo-50');
            }
        });
        
        // Set initial state
        if (radio.checked) {
            label.classList.add('border-indigo-500', 'bg-indigo-50');
        }
    });
    
    // Style selected payment methods
    document.querySelectorAll('.payment-method').forEach(label => {
        const radio = label.querySelector('input[type="radio"]');
        
        radio.addEventListener('change', function() {
            document.querySelectorAll('.payment-method').forEach(l => {
                l.classList.remove('border-indigo-500', 'bg-indigo-50');
            });
            
            if (this.checked) {
                label.classList.add('border-indigo-500', 'bg-indigo-50');
            }
        });
        
        // Set initial state
        if (radio.checked) {
            label.classList.add('border-indigo-500', 'bg-indigo-50');
        }
    });
});

function calculateShipping() {
    const city = document.getElementById('city').value;
    const shippingMethod = document.querySelector('input[name="shipping_method"]:checked').value;
    
    fetch('/orders/calculate-shipping', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            city: city,
            shipping_method: shippingMethod
        })
    })
    .then(response => response.json())
    .then(data => {
        // Update shipping cost display
        const shippingCostDisplay = document.getElementById('shipping-cost-display');
        const shippingCostElement = document.getElementById('shipping-cost');
        const totalAmountElement = document.getElementById('total-amount');
        const freeShippingMessage = document.getElementById('free-shipping-message');
        
        if (data.shipping_cost === 0) {
            shippingCostDisplay.textContent = 'Бесплатно';
            shippingCostElement.textContent = 'Бесплатно';
            freeShippingMessage.classList.remove('hidden');
        } else {
            shippingCostDisplay.textContent = formatPrice(data.shipping_cost);
            shippingCostElement.textContent = formatPrice(data.shipping_cost);
            freeShippingMessage.classList.add('hidden');
        }
        
        // Update total amount
        totalAmountElement.textContent = formatPrice(data.total);
    })
    .catch(error => {
        console.error('Error calculating shipping:', error);
    });
}

function submitOrder() {
    const submitBtn = document.getElementById('submit-order');
    const loadingOverlay = document.getElementById('loading-overlay');
    
    // Show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i>Обработка...';
    loadingOverlay.classList.remove('hidden');
    
    // Clear previous errors
    document.querySelectorAll('.text-red-500').forEach(el => {
        el.classList.add('hidden');
    });
    
    const formData = new FormData(document.getElementById('checkout-form'));
    
    fetch('/orders', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to confirmation page
            window.location.href = data.redirect_url;
        } else {
            // Show error message
            showError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Произошла ошибка при оформлении заказа');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-lock mr-3"></i>Оформить заказ';
        loadingOverlay.classList.add('hidden');
    });
}

function showError(message) {
    // Simple error display
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    errorDiv.textContent = message;
    document.body.appendChild(errorDiv);
    
    setTimeout(() => {
        errorDiv.remove();
    }, 5000);
}

function formatPrice(amount) {
    return new Intl.NumberFormat('ru-RU').format(amount) + ' ₽';
}
</script>

<style>
.shipping-method, .payment-method {
    transition: all 0.3s ease;
}

input[type="radio"]:checked + div {
    border-color: #4F46E5;
    background-color: #EEF2FF;
}

.max-h-64 {
    max-height: 16rem;
}

.sticky {
    position: sticky;
}
</style>
@endsection