<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .order-detail { border: 1px solid #ddd; padding: 20px; border-radius: 5px; }
        .order-header { background: #f8f9fa; padding: 15px; margin: -20px -20px 20px; }
        .order-item { padding: 10px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; }
        .customer-info { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .status-{{ $order->status }} { font-weight: bold; }
    </style>
</head>
<body>
    <h1>📄 Детали заказа #{{ $order->id }}</h1>
    
    <div class="order-detail">
        <div class="order-header">
            <strong>Заказ #{{ $order->id }}</strong> | 
            {{ $order->created_at->format('d.m.Y H:i') }} | 
            <span class="status-{{ $order->status }}">{{ $order->status_text }}</span>
        </div>
        
        <div class="customer-info">
            <h3>Информация о доставке</h3>
            <p><strong>Получатель:</strong> {{ $order->customer_name }}</p>
            <p><strong>Email:</strong> {{ $order->customer_email }}</p>
            <p><strong>Телефон:</strong> {{ $order->customer_phone }}</p>
            <p><strong>Адрес:</strong> {{ $order->customer_address }}</p>
        </div>
        
        <div class="order-items">
            <h3>Состав заказа</h3>
            @foreach($order->items as $item)
            <div class="order-item">
                <span>{{ $item->product_name }}</span>
                <span>{{ $item->quantity }} × {{ $item->product_price }} руб. = {{ $item->quantity * $item->product_price }} руб.</span>
            </div>
            @endforeach
            
            <div class="order-item" style="border-top: 2px solid #000; font-weight: bold; margin-top: 10px;">
                <span>Общая сумма:</span>
                <span>{{ $order->total_amount }} руб.</span>
            </div>
        </div>
    </div>
    
    <br>
    <a href="{{ route('profile.orders') }}">← Назад к списку заказов</a> |
    <a href="/catalog">В каталог</a>
</body>
</html>