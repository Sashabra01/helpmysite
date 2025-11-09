<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📋 История заказов
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Фильтры и поиск -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <form method="GET" action="{{ route('profile.orders') }}" class="space-y-4 md:space-y-0 md:flex md:space-x-4">
                        <!-- Поиск по ID -->
                        <div class="flex-1">
                            <label for="search" class="block text-sm font-medium text-gray-700">Поиск по номеру заказа</label>
                            <input type="text" 
                                   name="search" 
                                   id="search"
                                   value="{{ request('search') }}"
                                   placeholder="Введите номер заказа"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <!-- Фильтр по статусу -->
                        <div class="flex-1">
                            <label for="status" class="block text-sm font-medium text-gray-700">Статус заказа</label>
                            <select name="status" 
                                    id="status"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Все статусы</option>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Фильтр по дате от -->
                        <div class="flex-1">
                            <label for="date_from" class="block text-sm font-medium text-gray-700">Дата от</label>
                            <input type="date" 
                                   name="date_from" 
                                   id="date_from"
                                   value="{{ request('date_from') }}"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <!-- Фильтр по дате до -->
                        <div class="flex-1">
                            <label for="date_to" class="block text-sm font-medium text-gray-700">Дата до</label>
                            <input type="date" 
                                   name="date_to" 
                                   id="date_to"
                                   value="{{ request('date_to') }}"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <!-- Кнопки -->
                        <div class="flex items-end space-x-2">
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md text-sm font-medium">
                                Применить
                            </button>
                            <a href="{{ route('profile.orders') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md text-sm font-medium">
                                Сбросить
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Статистика -->
            @if($orders->count() > 0)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex flex-wrap justify-between items-center">
                    <div class="text-sm text-blue-800">
                        Найдено заказов: <strong>{{ $orders->count() }}</strong> | 
                        Общая сумма: <strong>{{ $orders->sum('total_amount') }} ₽</strong>
                    </div>
                    <div class="text-sm text-blue-800">
                        @php
                            $statusCounts = $orders->groupBy('status')->map->count();
                        @endphp
                        @foreach($statusCounts as $status => $count)
                            <span class="mr-3">
                                {{ $statuses[$status] ?? $status }}: <strong>{{ $count }}</strong>
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Список заказов -->
            @if($orders->count() > 0)
                <div class="space-y-6">
                    @foreach($orders as $order)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                        <div class="p-6">
                            <!-- Заголовок заказа -->
                            <div class="flex flex-col md:flex-row md:justify-between md:items-start mb-4">
                                <div class="mb-4 md:mb-0">
                                    <h3 class="text-lg font-semibold text-gray-900">Заказ #{{ $order->id }}</h3>
                                    <p class="text-gray-600 text-sm mt-1">
                                        {{ $order->created_at->format('d.m.Y в H:i') }} | 
                                        {{ $order->items->count() }} товар(а)
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold 
                                        @if($order->status == 'new') bg-green-100 text-green-800 border border-green-200
                                        @elseif($order->status == 'processing') bg-yellow-100 text-yellow-800 border border-yellow-200
                                        @elseif($order->status == 'completed') bg-blue-100 text-blue-800 border border-blue-200
                                        @else bg-red-100 text-red-800 border border-red-200 @endif">
                                        {{ $order->status_text }}
                                    </span>
                                    <p class="text-xl font-bold mt-2 text-gray-900">{{ number_format($order->total_amount, 0, ',', ' ') }} ₽</p>
                                </div>
                            </div>
                            
                            <!-- Товары в заказе -->
                            <div class="mb-4 border-t border-gray-100 pt-4">
                                <h4 class="font-semibold text-gray-700 mb-3">Состав заказа:</h4>
                                <div class="space-y-2">
                                    @foreach($order->items as $item)
                                    <div class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded">
                                        <span class="text-gray-800">{{ $item->product_name }}</span>
                                        <span class="text-gray-600 font-medium">
                                            {{ $item->quantity }} × {{ number_format($item->product_price, 0, ',', ' ') }} ₽ = 
                                            <strong>{{ number_format($item->quantity * $item->product_price, 0, ',', ' ') }} ₽</strong>
                                        </span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Информация о доставке -->
                            <div class="mb-4 border-t border-gray-100 pt-4">
                                <h4 class="font-semibold text-gray-700 mb-2">Информация о доставке:</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-600">
                                    <div><strong>Получатель:</strong> {{ $order->customer_name }}</div>
                                    <div><strong>Телефон:</strong> {{ $order->customer_phone }}</div>
                                    <div class="md:col-span-2"><strong>Адрес:</strong> {{ $order->customer_address }}</div>
                                </div>
                            </div>
                            
                            <!-- Действия -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-t border-gray-100 pt-4">
                                <div class="mb-3 sm:mb-0">
                                    <a href="{{ route('profile.order-details', $order->id) }}" 
                                       class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                                        📄 Подробная информация о заказе
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                                
                                <div class="flex space-x-3">
                                    @if(in_array($order->status, ['new', 'processing']))
                                    <form action="{{ route('profile.orders.cancel', $order->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 font-medium text-sm"
                                                onclick="return confirm('Вы уверены, что хотите отменить заказ #{{ $order->id }}?')">
                                            ❌ Отменить заказ
                                        </button>
                                    </form>
                                    @endif
                                    
                                    @if($order->status == 'completed')
                                    <form action="{{ route('profile.orders.repeat', $order->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="text-green-600 hover:text-green-800 font-medium text-sm"
                                                onclick="return confirm('Повторить заказ #{{ $order->id }}?')">
                                            🔄 Повторить заказ
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- Сообщение если заказов нет -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Заказов не найдено</h3>
                        <p class="text-gray-600 mb-6">
                            @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                                Попробуйте изменить параметры поиска или сбросить фильтры
                            @else
                                У вас пока нет заказов. Сделайте первый заказ в нашем каталоге!
                            @endif
                        </p>
                        <div class="space-x-3">
                            <a href="/catalog" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-md font-medium">
                                🛍️ Перейти в каталог
                            </a>
                            @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                                <a href="{{ route('profile.orders') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-md font-medium">
                                    Сбросить фильтры
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Навигация -->
            <div class="mt-8 flex justify-between items-center">
                <a href="{{ route('profile') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    ← Назад в профиль
                </a>
                <a href="/catalog" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                    🛒 Продолжить покупки
                </a>
            </div>
        </div>
    </div>
</x-app-layout>