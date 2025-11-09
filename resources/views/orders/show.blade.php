@extends('layouts.app')

@section('title', 'Заказ ' . $order->order_number . ' - Laravel Store')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ url('/') }}" class="hover:text-indigo-600">Главная</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('orders.index') }}" class="hover:text-indigo-600">Мои заказы</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-400">Заказ {{ $order->order_number }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Заказ {{ $order->order_number }}</h1>
                <p class="text-gray-600">Оформлен {{ $order->formatted_created_at }}</p>
            </div>
            <div class="mt-4 lg:mt-0">
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full 
                    @if($order->status == 'new') bg-blue-100 text-blue-800
                    @elseif($order->status == 'processing') bg-yellow-100 text-yellow-800
                    @elseif($order->status == 'shipped') bg-indigo-100 text-indigo-800
                    @elseif($order->status == 'delivered') bg-green-100 text-green-800
                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ $order->status_text }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Товары в заказе</h2>
                
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                        <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                            @if($item->product && $item->product->images && count($item->product->images) > 0)
                                <img src="{{ asset('storage/' . $item->product->images[0]) }}" 
                                     alt="{{ $item->product_name }}" 
                                     class="max-h-full max-w-full object-contain">
                            @else
                                <i class="fas fa-tshirt text-indigo-400 text-xl"></i>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800 mb-1">
                                @if($item->product)
                                    <a href="{{ route('product.show', $item->product->slug) }}" class="hover:text-indigo-600">
                                        {{ $item->product_name }}
                                    </a>
                                @else
                                    {{ $item->product_name }}
                                @endif
                            </h3>
                            <p class="text-gray-600 text-sm">Артикул: {{ $item->product_sku }}</p>
                            <p class="text-gray-600 text-sm">Цена: {{ number_format($item->price, 0, ',', ' ') }} ₽</p>
                        </div>
                        
                        <div class="text-right">
                            <div class="text-gray-600 mb-1">Количество: {{ $item->quantity }}</div>
                            <div class="font-bold text-gray-800 text-lg">
                                {{ number_format($item->quantity * $item->price, 0, ',', ' ') }} ₽
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Order Total -->
                <div class="border-t border-gray-200 mt-6 pt-6">
                    <div class="flex justify-between items-center text-lg font-bold">
                        <span>Общая сумма:</span>
                        <span class="text-indigo-600 text-xl">{{ $order->formatted_total }}</span>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            @if($order->notes)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Примечания к заказу</h2>
                <p class="text-gray-600">{{ $order->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Order Actions -->
            @if($order->canBeCancelled())
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Действия с заказом</h3>
                <form action="{{ route('orders.cancel', $order) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            onclick="return confirm('Вы уверены, что хотите отменить этот заказ?')"
                            class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition font-semibold flex items-center justify-center">
                        <i class="fas fa-times mr-3"></i>Отменить заказ
                    </button>
                </form>
            </div>
            @endif

            <!-- Order Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Информация о заказе</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-600 text-sm">Способ оплаты:</span>
                        <p class="font-medium">{{ $order->payment_method_text }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm">Способ доставки:</span>
                        <p class="font-medium">{{ $order->shipping_method_text }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm">Статус оплаты:</span>
                        <p class="font-medium">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                Оплачен
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Информация о покупателе</h3>
                <div class="space-y-2">
                    <p class="font-medium">{{ $order->full_name }}</p>
                    <p class="text-gray-600 text-sm">{{ $order->email }}</p>
                    <p class="text-gray-600 text-sm">{{ $order->phone }}</p>
                    <p class="text-gray-600 text-sm mt-2">
                        {{ $order->address }},<br>
                        {{ $order->city }}, {{ $order->postal_code }}
                    </p>
                </div>
            </div>

            <!-- Help Section -->
            <div class="bg-blue-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-3">Нужна помощь?</h3>
                <p class="text-blue-700 text-sm mb-4">
                    Если у вас есть вопросы по заказу, свяжитесь с нашей службой поддержки.
                </p>
                <div class="space-y-2 text-sm text-blue-700">
                    <div class="flex items-center">
                        <i class="fas fa-phone mr-3 text-sm"></i>
                        <span>8 (800) 123-45-67</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-envelope mr-3 text-sm"></i>
                        <span>support@laravel-store.ru</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection