@extends('layouts.app')

@section('title', 'Управление заказами - Админ-панель')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Управление заказами</h1>
                <p class="text-gray-600 mt-2">Все заказы вашего магазина</p>
            </div>
            <button onclick="loadStatistics()" 
                    class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-medium flex items-center space-x-2">
                <i class="fas fa-chart-bar"></i>
                <span>Статистика</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-shopping-cart text-indigo-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Всего заказов</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-orders">0</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-money-bill-wave text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Общая выручка</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-revenue">0 ₽</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Ожидают обработки</p>
                    <p class="text-2xl font-bold text-gray-900" id="pending-orders">0</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Завершено</p>
                    <p class="text-2xl font-bold text-gray-900" id="completed-orders">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Поиск</label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Номер заказа, email, телефон..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Статус</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Все статусы</option>
                    @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Дата с</label>
                <input type="date" 
                       name="date_from" 
                       value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Дата по</label>
                <input type="date" 
                       name="date_to" 
                       value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Filter Actions -->
            <div class="md:col-span-4 flex space-x-4 pt-2">
                <button type="submit" 
                        class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                    Применить фильтры
                </button>
                <a href="{{ route('admin.orders.index') }}" 
                   class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-50 transition font-medium">
                    Сбросить
                </a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($orders->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Заказ
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Клиент
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Сумма
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Статус
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Дата
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Действия
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $order->order_number }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $order->items->count() }} товар(ов)
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $order->first_name }} {{ $order->last_name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $order->email }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $order->phone }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ number_format($order->total_amount, 0, ',', ' ') }} ₽
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <select onchange="updateOrderStatus({{ $order->id }}, this.value)" 
                                        class="text-sm border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                            @if($order->status == 'new') bg-blue-100 text-blue-800
                                            @elseif($order->status == 'processing') bg-yellow-100 text-yellow-800
                                            @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                                            @elseif($order->status == 'delivered') bg-green-100 text-green-800
                                            @elseif($order->status == 'completed') bg-green-100 text-green-800
                                            @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                    @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ $order->status == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ $order->created_at->format('d.m.Y') }}</div>
                            <div>{{ $order->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.orders.edit', $order) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 transition"
                                   title="Редактировать заказ">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('orders.show', $order) }}" 
                                   target="_blank"
                                   class="text-gray-600 hover:text-gray-900 transition"
                                   title="Просмотреть заказ">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button onclick="showOrderDetails({{ $order->id }})" 
                                        class="text-green-600 hover:text-green-900 transition"
                                        title="Быстрый просмотр">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
        @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <i class="fas fa-shopping-bag text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Заказы не найдены</h3>
            <p class="text-gray-500 mb-6">Попробуйте изменить параметры фильтрации</p>
        </div>
        @endif
    </div>
</div>

<!-- Order Details Modal -->
<div id="order-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Детали заказа</h3>
            <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="order-details-content">
            <!-- Content will be loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Statistics Modal -->
<div id="stats-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-800">Статистика заказов</h3>
            <button onclick="closeStatsModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="stats-content">
            <div class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
            </div>
        </div>
    </div>
</div>

<script>
// Load statistics on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
});

function loadStatistics() {
    fetch('/admin/orders/statistics', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update quick stats
        document.getElementById('total-orders').textContent = data.stats.total_orders;
        document.getElementById('total-revenue').textContent = formatPrice(data.stats.total_revenue);
        document.getElementById('pending-orders').textContent = data.stats.pending_orders;
        document.getElementById('completed-orders').textContent = data.stats.completed_orders;
    })
    .catch(error => {
        console.error('Error loading statistics:', error);
    });
}

function showStatistics() {
    document.getElementById('stats-modal').classList.remove('hidden');
    
    fetch('/admin/orders/statistics', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        const statsContent = document.getElementById('stats-content');
        
        let html = `
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-indigo-600 mb-2">${data.stats.total_orders}</div>
                    <div class="text-sm text-gray-600">Всего заказов</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600 mb-2">${formatPrice(data.stats.total_revenue)}</div>
                    <div class="text-sm text-gray-600">Общая выручка</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-yellow-600 mb-2">${data.stats.pending_orders}</div>
                    <div class="text-sm text-gray-600">Ожидают обработки</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600 mb-2">${data.stats.completed_orders}</div>
                    <div class="text-sm text-gray-600">Завершено</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-red-600 mb-2">${data.stats.cancelled_orders}</div>
                    <div class="text-sm text-gray-600">Отменено</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600 mb-2">${data.stats.today_orders}</div>
                    <div class="text-sm text-gray-600">Заказов сегодня</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Статистика по статусам</h4>
                    <div class="space-y-3">
        `;

        // Status statistics
        const statusLabels = {
            'new': 'Новые',
            'processing': 'В обработке',
            'shipped': 'Отправлены',
            'delivered': 'Доставлены',
            'completed': 'Завершены',
            'cancelled': 'Отменены'
        };

        for (const [status, count] of Object.entries(data.status_stats)) {
            const label = statusLabels[status] || status;
            html += `
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">${label}</span>
                    <span class="font-medium">${count}</span>
                </div>
            `;
        }

        html += `
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Активность за 7 дней</h4>
                    <div class="space-y-3">
        `;

        // Daily statistics
        data.daily_stats.forEach(stat => {
            const date = new Date(stat.date).toLocaleDateString('ru-RU');
            html += `
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">${date}</span>
                    <span class="font-medium">${stat.count} заказов</span>
                </div>
            `;
        });

        html += `
                    </div>
                </div>
            </div>
        `;

        statsContent.innerHTML = html;
    })
    .catch(error => {
        console.error('Error loading statistics:', error);
        document.getElementById('stats-content').innerHTML = `
            <div class="text-center py-8 text-red-600">
                <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                <p>Ошибка при загрузке статистики</p>
            </div>
        `;
    });
}

function closeStatsModal() {
    document.getElementById('stats-modal').classList.add('hidden');
}

function updateOrderStatus(orderId, newStatus) {
    fetch(`/admin/orders/${orderId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Статус заказа обновлен', 'success');
            // Update the select styling
            const select = event.target;
            select.className = `text-sm border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 ${getStatusClass(newStatus)}`;
        } else {
            showToast(data.message, 'error');
            // Reset to original value
            event.target.value = event.target.getAttribute('data-original-value');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Ошибка при обновлении статуса', 'error');
        event.target.value = event.target.getAttribute('data-original-value');
    });
}

function getStatusClass(status) {
    switch(status) {
        case 'new': return 'bg-blue-100 text-blue-800';
        case 'processing': return 'bg-yellow-100 text-yellow-800';
        case 'shipped': return 'bg-purple-100 text-purple-800';
        case 'delivered': return 'bg-green-100 text-green-800';
        case 'completed': return 'bg-green-100 text-green-800';
        case 'cancelled': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function showOrderDetails(orderId) {
    const modal = document.getElementById('order-modal');
    const content = document.getElementById('order-details-content');
    
    modal.classList.remove('hidden');
    content.innerHTML = `
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
        </div>
    `;

    // In a real implementation, you would fetch order details via AJAX
    // For now, we'll just show a message
    setTimeout(() => {
        content.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-info-circle text-indigo-400 text-4xl mb-4"></i>
                <p class="text-gray-600">Детальная информация о заказе</p>
                <p class="text-sm text-gray-500 mt-2">Для просмотра полной информации перейдите к редактированию заказа</p>
                <a href="/admin/orders/${orderId}/edit" 
                   class="inline-block mt-4 bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                    Перейти к редактированию
                </a>
            </div>
        `;
    }, 1000);
}

function closeOrderModal() {
    document.getElementById('order-modal').classList.add('hidden');
}

function formatPrice(amount) {
    return new Intl.NumberFormat('ru-RU').format(amount) + ' ₽';
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

// Close modals on outside click
document.getElementById('order-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeOrderModal();
    }
});

document.getElementById('stats-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStatsModal();
    }
});
</script>

<style>
.transition {
    transition: all 0.3s ease;
}

.hover\:bg-gray-50:hover {
    background-color: #f9fafb;
}

.max-h-\[90vh\] {
    max-height: 90vh;
}
</style>
@endsection