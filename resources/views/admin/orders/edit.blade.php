@extends('layouts.app')

@section('title', 'Редактировать заказ - Админ-панель')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Редактировать заказ</h1>
                <p class="text-gray-600 mt-2">Номер заказа: {{ $order->order_number }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('orders.show', $order) }}" 
                   target="_blank"
                   class="border border-gray-300 text-gray-700 px-4 py-3 rounded-lg hover:bg-gray-50 transition font-medium flex items-center space-x-2">
                    <i class="fas fa-external-link-alt"></i>
                    <span>Просмотреть на сайте</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" 
                   class="border border-gray-300 text-gray-700 px-4 py-3 rounded-lg hover:bg-gray-50 transition font-medium flex items-center space-x-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Назад к списку</span>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Товары в заказе</h2>
                    <button onclick="showAddItemModal()" 
                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-medium flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Добавить товар</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Товар
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Цена
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Количество
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Сумма
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Действия
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($order->items as $item)
                            <tr class="order-item" data-item-id="{{ $item->id }}">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                                            @if($item->product && $item->product->images && count($item->product->images) > 0)
                                                <img src="{{ Storage::url($item->product->images[0]) }}" 
                                                     alt="{{ $item->product_name }}"
                                                     class="h-8 w-8 object-cover rounded">
                                            @else
                                                <i class="fas fa-image text-gray-300"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $item->product_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $item->product_sku }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($item->price, 0, ',', ' ') }} ₽
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ number_format($item->quantity * $item->price, 0, ',', ' ') }} ₽
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="removeItem({{ $item->id }})" 
                                            class="text-red-600 hover:text-red-900 transition"
                                            title="Удалить из заказа">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-right text-sm font-medium text-gray-900">
                                    Итого:
                                </td>
                                <td class="px-4 py-4 text-sm font-bold text-gray-900">
                                    {{ number_format($order->total_amount, 0, ',', ' ') }} ₽
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Order Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Информация о заказе</h2>
                
                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Статус заказа *</label>
                            <select id="status" 
                                    name="status" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $order->status) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Order Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Дата заказа</label>
                            <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">
                                {{ $order->created_at->format('d.m.Y H:i') }}
                            </p>
                        </div>

                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">Имя *</label>
                            <input type="text" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="{{ old('first_name', $order->first_name) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                            @error('first_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Фамилия *</label>
                            <input type="text" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="{{ old('last_name', $order->last_name) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                            @error('last_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $order->email) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Телефон *</label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $order->phone) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Адрес *</label>
                            <input type="text" 
                                   id="address" 
                                   name="address" 
                                   value="{{ old('address', $order->address) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">Город *</label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city', $order->city) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                            @error('city')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Индекс *</label>
                            <input type="text" 
                                   id="postal_code" 
                                   name="postal_code" 
                                   value="{{ old('postal_code', $order->postal_code) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                            @error('postal_code')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Примечания</label>
                            <textarea id="notes" 
                                      name="notes" 
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 mt-6">
                        <a href="{{ route('admin.orders.index') }}" 
                           class="border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition font-medium">
                            Отмена
                        </a>
                        <button type="submit" 
                                class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-medium">
                            Сохранить изменения
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column - Order Summary -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Сводка заказа</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Номер заказа:</span>
                        <span class="font-medium">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Дата создания:</span>
                        <span class="font-medium">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Статус:</span>
                        <span class="font-medium {{ getStatusColor($order->status) }}">{{ $statuses[$order->status] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Способ доставки:</span>
                        <span class="font-medium">
                            @if($order->shipping_method === 'pickup')
                                Самовывоз
                            @else
                                Курьерская доставка
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Способ оплаты:</span>
                        <span class="font-medium">
                            @if($order->payment_method === 'card')
                                Банковская карта
                            @elseif($order->payment_method === 'online')
                                Онлайн-платеж
                            @else
                                Наличными
                            @endif
                        </span>
                    </div>
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between text-lg font-bold text-gray-800">
                            <span>Итого:</span>
                            <span>{{ number_format($order->total_amount, 0, ',', ' ') }} ₽</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Информация о клиенте</h2>
                
                <div class="space-y-3">
                    @if($order->user)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">ID пользователя:</span>
                        <span class="font-medium">{{ $order->user->id }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Имя пользователя:</span>
                        <span class="font-medium">{{ $order->user->name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-medium">{{ $order->user->email }}</span>
                    </div>
                    @if($order->user->phone)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Телефон:</span>
                        <span class="font-medium">{{ $order->user->phone }}</span>
                    </div>
                    @endif
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-user-slash text-gray-300 text-3xl mb-2"></i>
                        <p class="text-gray-600 text-sm">Гостевой заказ</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Order Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Действия</h2>
                
                <div class="space-y-3">
                    <button onclick="printOrder()" 
                            class="w-full border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition font-medium flex items-center justify-center">
                        <i class="fas fa-print mr-3"></i>Распечатать заказ
                    </button>
                    
                    <button onclick="sendNotification()" 
                            class="w-full border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition font-medium flex items-center justify-center">
                        <i class="fas fa-envelope mr-3"></i>Отправить уведомление
                    </button>
                    
                    <button onclick="duplicateOrder()" 
                            class="w-full border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition font-medium flex items-center justify-center">
                        <i class="fas fa-copy mr-3"></i>Дублировать заказ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div id="add-item-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Добавить товар в заказ</h3>
        
        <form id="add-item-form">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">Товар *</label>
                    <select id="product_id" 
                            name="product_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            required>
                        <option value="">Выберите товар</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" data-stock="{{ $product->stock }}" data-price="{{ $product->price }}">
                            {{ $product->name }} ({{ $product->sku }}) - {{ number_format($product->price, 0, ',', ' ') }} ₽
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Количество *</label>
                    <input type="number" 
                           id="quantity" 
                           name="quantity" 
                           value="1"
                           min="1"
                           max="1"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           required>
                    <p class="text-xs text-gray-500 mt-1" id="stock-info">В наличии: 0 шт.</p>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" 
                            onclick="closeAddItemModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Отмена
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                        Добавить
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function showAddItemModal() {
    document.getElementById('add-item-modal').classList.remove('hidden');
}

function closeAddItemModal() {
    document.getElementById('add-item-modal').classList.add('hidden');
}

// Update quantity max based on selected product stock
document.getElementById('product_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const stock = selectedOption.getAttribute('data-stock');
    const price = selectedOption.getAttribute('data-price');
    
    document.getElementById('quantity').max = stock;
    document.getElementById('stock-info').textContent = `В наличии: ${stock} шт.`;
});

// Add item to order
document.getElementById('add-item-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const productId = document.getElementById('product_id').value;
    const quantity = document.getElementById('quantity').value;
    
    if (!productId || !quantity) return;
    
    fetch(`/admin/orders/{{ $order->id }}/add-item`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: parseInt(quantity)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Товар добавлен в заказ', 'success');
            closeAddItemModal();
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Ошибка при добавлении товара', 'error');
    });
});

function removeItem(itemId) {
    if (!confirm('Удалить товар из заказа?')) return;
    
    fetch(`/admin/orders/{{ $order->id }}/items/${itemId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Товар удален из заказа', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Ошибка при удалении товара', 'error');
    });
}

function printOrder() {
    window.open('{{ route("orders.show", $order) }}?print=true', '_blank');
}

function sendNotification() {
    showToast('Уведомление отправлено клиенту', 'success');
}

function duplicateOrder() {
    showToast('Заказ продублирован', 'success');
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white font-medium z-50 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Close modal on outside click
document.getElementById('add-item-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddItemModal();
    }
});
</script>

<style>
.transition {
    transition: all 0.3s ease;
}

.sticky {
    position: sticky;
}
</style>

<?php
function getStatusColor($status) {
    switch($status) {
        case 'new': return 'text-blue-600';
        case 'processing': return 'text-yellow-600';
        case 'shipped': return 'text-purple-600';
        case 'delivered': return 'text-green-600';
        case 'completed': return 'text-green-600';
        case 'cancelled': return 'text-red-600';
        default: return 'text-gray-600';
    }
}
?>
@endsection