@extends('layouts.app')

@section('title', 'Заказ подтвержден - Laravel Store')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ url('/') }}" class="hover:text-indigo-600">Главная</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('cart.index') }}" class="hover:text-indigo-600">Корзина</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-400">Подтверждение заказа</li>
        </ol>
    </nav>

    <!-- Success Message -->
    <div class="bg-white rounded-lg shadow-sm p-8 mb-8 text-center">
        <div class="max-w-md mx-auto">
            <!-- Success Icon -->
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-green-600 text-3xl"></i>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Заказ подтвержден!</h1>
            <p class="text-gray-600 mb-6">
                Спасибо за ваш заказ. Мы отправили подтверждение на вашу электронную почту.
            </p>
            
            <!-- Order Number -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-600 mb-2">Номер вашего заказа:</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $order->order_number }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Детали заказа</h2>
                
                <!-- Order Items -->
                <div class="space-y-4 mb-6">
                    @foreach($order->items as $item)
                    <div class="flex items-center space-x-4 py-3 border-b border-gray-100 last:border-b-0">
                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            @if($item->product)
                                <img src="{{ Storage::url($item->product->image) }}" 
                                     alt="{{ $item->product->name }}"
                                     class="w-12 h-12 object-cover rounded">
                            @else
                                <i class="fas fa-image text-gray-300"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-800">{{ $item->product_name }}</h3>
                            <p class="text-sm text-gray-600">Артикул: {{ $item->product_sku }}</p>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-gray-800">
                                {{ number_format($item->price * $item->quantity, 0, ',', ' ') }} ₽
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $item->quantity }} × {{ number_format($item->price, 0, ',', ' ') }} ₽
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Order Totals -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="space-y-2">
                        <div class="flex justify-between text-gray-600">
                            <span>Стоимость товаров:</span>
                            <span>{{ number_format($order->total_amount, 0, ',', ' ') }} ₽</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Доставка:</span>
                            <span class="text-green-600">
                                @if($order->shipping_method === 'pickup')
                                    Бесплатно (самовывоз)
                                @else
                                    Бесплатно
                                @endif
                            </span>
                        </div>
                        <div class="border-t border-gray-200 pt-2">
                            <div class="flex justify-between text-lg font-bold text-gray-800">
                                <span>Итого:</span>
                                <span>{{ number_format($order->total_amount, 0, ',', ' ') }} ₽</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping & Payment Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Shipping Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-truck mr-3 text-indigo-600"></i>
                        Доставка
                    </h2>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Способ доставки:</p>
                            <p class="font-medium text-gray-800">
                                @if($order->shipping_method === 'pickup')
                                    Самовывоз
                                @else
                                    Курьерская доставка
                                @endif
                            </p>
                        </div>
                        
                        @if($order->shipping_method === 'delivery')
                        <div>
                            <p class="text-sm text-gray-600">Адрес доставки:</p>
                            <p class="font-medium text-gray-800">
                                {{ $order->city }}, {{ $order->address }}<br>
                                Индекс: {{ $order->postal_code }}
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Ориентировочное время доставки:</p>
                            <p class="font-medium text-gray-800">1-3 рабочих дня</p>
                        </div>
                        @else
                        <div>
                            <p class="text-sm text-gray-600">Адрес пункта выдачи:</p>
                            <p class="font-medium text-gray-800">
                                г. Москва, ул. Примерная, 123<br>
                                Пн-Пт: 9:00-21:00, Сб-Вс: 10:00-18:00
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-credit-card mr-3 text-indigo-600"></i>
                        Оплата
                    </h2>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Способ оплаты:</p>
                            <p class="font-medium text-gray-800">
                                @if($order->payment_method === 'card')
                                    Банковская карта
                                @elseif($order->payment_method === 'online')
                                    Онлайн-платеж
                                @else
                                    Наличными при получении
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Статус оплаты:</p>
                            <p class="font-medium text-yellow-600">
                                @if($order->payment_method === 'cash')
                                    Оплата при получении
                                @else
                                    Ожидает оплаты
                                @endif
                            </p>
                        </div>
                        
                        @if($order->payment_method !== 'cash')
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Ссылка для оплаты будет отправлена на ваш email
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status & Actions -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Order Status -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Статус заказа</h2>
                
                <div class="space-y-4">
                    <!-- Status Badge -->
                    <div class="text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            @if($order->status == 'new') bg-blue-100 text-blue-800
                            @elseif($order->status == 'processing') bg-yellow-100 text-yellow-800
                            @elseif($order->status == 'completed') bg-green-100 text-green-800
                            @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $order->status_text }}
                        </span>
                    </div>

                    <!-- Status Timeline -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">Заказ принят</p>
                                <p class="text-xs text-gray-500">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-600">Обработка</p>
                                <p class="text-xs text-gray-500">Ожидает подтверждения</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-600">Доставка</p>
                                <p class="text-xs text-gray-500">Подготовка к отправке</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-600">Завершен</p>
                                <p class="text-xs text-gray-500">Ожидает получения</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Support -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Нужна помощь?</h2>
                
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-phone text-indigo-600"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-800">8 (800) 123-45-67</p>
                            <p class="text-xs text-gray-600">Бесплатная горячая линия</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-envelope text-indigo-600"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-800">support@laravel-store.ru</p>
                            <p class="text-xs text-gray-600">Электронная почта</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-clock text-indigo-600"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Ежедневно 9:00-21:00</p>
                            <p class="text-xs text-gray-600">Время работы поддержки</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Действия</h2>
                
                <div class="space-y-3">
                    <a href="{{ route('orders.index') }}" 
                       class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition font-medium flex items-center justify-center">
                        <i class="fas fa-list mr-3"></i>Мои заказы
                    </a>
                    
                    <a href="{{ route('catalog') }}" 
                       class="w-full border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition font-medium flex items-center justify-center">
                        <i class="fas fa-shopping-bag mr-3"></i>Продолжить покупки
                    </a>
                    
                    @if(in_array($order->status, ['new', 'processing']))
                    <button onclick="cancelOrder({{ $order->id }})" 
                            class="w-full border border-red-300 text-red-600 py-3 rounded-lg hover:bg-red-50 transition font-medium flex items-center justify-center">
                        <i class="fas fa-times mr-3"></i>Отменить заказ
                    </button>
                    @endif
                </div>
            </div>

            <!-- Order PDF -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Документы</h2>
                
                <button class="w-full border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition font-medium flex items-center justify-center">
                    <i class="fas fa-file-pdf mr-3 text-red-500"></i>Скачать счет (PDF)
                </button>
            </div>
        </div>
    </div>

    <!-- Next Steps -->
    <div class="bg-white rounded-lg shadow-sm p-6 mt-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Что дальше?</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-envelope text-blue-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Подтверждение по email</h3>
                <p class="text-sm text-gray-600">Мы отправили детали заказа на ваш email</p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-shipping-fast text-green-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Обработка заказа</h3>
                <p class="text-sm text-gray-600">Мы свяжемся с вами для подтверждения деталей</p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-box-open text-purple-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Доставка</h3>
                <p class="text-sm text-gray-600">Отслеживайте статус доставки в личном кабинете</p>
            </div>
        </div>
    </div>
</div>

<script>
function cancelOrder(orderId) {
    if (!confirm('Вы уверены, что хотите отменить этот заказ?')) {
        return;
    }

    fetch(`/orders/${orderId}/cancel`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Заказ успешно отменен', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Ошибка при отмене заказа', 'error');
    });
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
</script>

<style>
.transition {
    transition: all 0.3s ease;
}

.border-b:last-child {
    border-bottom: none !important;
}
</style>
@endsection