@extends('layouts.app')

@section('title', 'Мои заказы - Laravel Store')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ url('/') }}" class="hover:text-indigo-600">Главная</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-400">Мои заказы</li>
        </ol>
    </nav>

    <h1 class="text-3xl font-bold text-gray-800 mb-8">Мои заказы</h1>

    @if($orders->count() > 0)
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Orders Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Заказ</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Товары</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Сумма</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $order->order_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600">{{ $order->formatted_created_at }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($order->status == 'new') bg-blue-100 text-blue-800
                                @elseif($order->status == 'processing') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'shipped') bg-indigo-100 text-indigo-800
                                @elseif($order->status == 'delivered') bg-green-100 text-green-800
                                @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $order->status_text }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">
                                {{ $order->items->sum('quantity') }} товар(ов)
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->formatted_total }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('orders.show', $order) }}" 
                               class="text-indigo-600 hover:text-indigo-900 mr-4">
                                <i class="fas fa-eye mr-1"></i>Подробнее
                            </a>
                            @if($order->canBeCancelled())
                            <form action="{{ route('orders.cancel', $order) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('Вы уверены, что хотите отменить этот заказ?')"
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-times mr-1"></i>Отменить
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Empty State (should not show but just in case) -->
    @else
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <i class="fas fa-shopping-bag text-gray-300 text-6xl mb-6"></i>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">У вас еще нет заказов</h2>
        <p class="text-gray-600 mb-8">Сделайте свой первый заказ и он появится здесь</p>
        <a href="{{ route('catalog') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold">
            <i class="fas fa-shopping-bag mr-3"></i>Перейти в каталог
        </a>
    </div>
    @endif
</div>
@endsection