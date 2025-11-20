<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админка - Заказы</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .order { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .order-header { background: #f8f9fa; padding: 10px; margin: -15px -15px 15px; display: flex; justify-content: space-between; align-items: center; }
        .order-item { padding: 5px 0; border-bottom: 1px solid #eee; }
        .status-new { color: #28a745; }
        .status-processing { color: #ffc107; }
        .status-completed { color: #17a2b8; }
        .status-cancelled { color: #dc3545; }
        .filters { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .filters input, .filters select { padding: 8px; margin: 0 10px 0 5px; border: 1px solid #ddd; border-radius: 4px; }
        .status-select { padding: 5px; border: 1px solid #ddd; border-radius: 3px; }
        .btn { padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 14px; margin-left: 10px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-success { background: #28a745; color: white; }
        .order-actions { display: flex; gap: 10px; align-items: center; }
    </style>
</head>
<body>
    <h1>📦 Управление заказами</h1>
    
    <!-- Фильтры и поиск -->
    <div class="filters">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Поиск по ID, имени или email" value="{{ request('search') }}">
            <select name="status">
                <option value="">Все статусы</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit">Применить</button>
            <a href="/admin/orders">Сбросить</a>
        </form>
    </div>
    
    @if($orders->count() > 0)
        @foreach($orders as $order)
        <div class="order">
            <div class="order-header">
                <div>
                    <strong>Заказ #{{ $order->id }}</strong> | 
                    {{ $order->created_at->format('d.m.Y H:i') }}
                </div>
                <div class="order-actions">
                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-primary">✏️ Редактировать</a>
                    <select class="status-select" data-order-id="{{ $order->id }}">
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ $order->status == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="status-{{ $order->status }}" id="status-{{ $order->id }}">
                        ({{ $statuses[$order->status] ?? $order->status }})
                    </span>
                </div>
            </div>
            
            <div class="customer-info">
                <strong>Клиент:</strong> {{ $order->customer_name ?? 'Не указано' }}<br>
                <strong>Email:</strong> {{ $order->customer_email ?? 'Не указано' }}<br>
                <strong>Телефон:</strong> {{ $order->customer_phone ?? 'Не указано' }}<br>
                <strong>Адрес:</strong> {{ $order->customer_address ?? 'Не указано' }}
            </div>
            
            <div class="order-items" style="margin-top: 10px;">
                <strong>Товары:</strong>
                @forelse($order->items as $item)
                <div class="order-item">
                    {{ $item->product_name ?? 'Товар' }} - {{ $item->product_price ?? 0 }} руб. × {{ $item->quantity ?? 1 }}
                    @if($item->product_price && $item->quantity)
                        = {{ $item->product_price * $item->quantity }} руб.
                    @endif
                </div>
                @empty
                <div class="order-item">Товары не найдены</div>
                @endforelse
            </div>
            
            <div class="order-total" style="margin-top: 10px;">
                <strong>Итого: {{ $order->total_amount ?? 0 }} руб.</strong>
            </div>
        </div>
        @endforeach
    @else
        <p>Заказов не найдено</p>
    @endif
    
    <br>
    <a href="/catalog" class="btn btn-secondary">← Вернуться в каталог</a>

    <script>
        // Обновление статуса заказа
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', function() {
                const orderId = this.dataset.orderId;
                const newStatus = this.value;
                
                fetch(`/admin/orders/${orderId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const statusSpan = document.getElementById(`status-${orderId}`);
                        statusSpan.textContent = `(${data.status_text})`;
                        statusSpan.className = `status-${newStatus}`;
                        alert('Статус обновлен!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при обновлении статуса');
                });
            });
        });
    </script>
</body>
</html>