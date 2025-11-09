@extends('layouts.app')

@section('title', 'Личный кабинет - Laravel Store')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ url('/') }}" class="hover:text-indigo-600">Главная</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-400">Личный кабинет</li>
        </ol>
    </nav>

    <h1 class="text-3xl font-bold text-gray-800 mb-8">Личный кабинет</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                <!-- User Info -->
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user text-indigo-600 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">{{ $user->name }}</h3>
                    <p class="text-gray-600 text-sm">{{ $user->email }}</p>
                </div>

                <!-- Navigation -->
                <nav class="space-y-2">
                    <a href="{{ route('profile.index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 bg-indigo-50 text-indigo-700 rounded-lg font-medium">
                        <i class="fas fa-user-circle"></i>
                        <span>Общая информация</span>
                    </a>
                    <a href="{{ route('profile.orders') }}" 
                       class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Мои заказы</span>
                    </a>
                    <a href="{{ route('profile.wishlist') }}" 
                       class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <i class="far fa-heart"></i>
                        <span>Избранное</span>
                        @if($wishlistCount > 0)
                        <span class="bg-pink-500 text-white text-xs rounded-full px-2 py-1 ml-auto">
                            {{ $wishlistCount }}
                        </span>
                        @endif
                    </a>
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <i class="fas fa-cog"></i>
                        <span>Настройки</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Welcome Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Добро пожаловать, {{ $user->name }}!</h2>
                <p class="text-gray-600">Здесь вы можете управлять своими заказами, избранными товарами и настройками профиля.</p>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <i class="fas fa-shopping-bag text-indigo-600 text-2xl mb-3"></i>
                    <div class="text-2xl font-bold text-gray-800 mb-1">{{ $user->orders->count() }}</div>
                    <div class="text-gray-600 text-sm">Всего заказов</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <i class="far fa-heart text-pink-600 text-2xl mb-3"></i>
                    <div class="text-2xl font-bold text-gray-800 mb-1">{{ $wishlistCount }}</div>
                    <div class="text-gray-600 text-sm">В избранном</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <i class="fas fa-star text-yellow-500 text-2xl mb-3"></i>
                    <div class="text-2xl font-bold text-gray-800 mb-1">0</div>
                    <div class="text-gray-600 text-sm">Отзывов</div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Последние заказы</h3>
                    <a href="{{ route('profile.orders') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        Все заказы →
                    </a>
                </div>

                @if($recentOrders->count() > 0)
                <div class="space-y-4">
                    @foreach($recentOrders as $order)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-800">{{ $order->order_number }}</div>
                            <div class="text-sm text-gray-600">{{ $order->formatted_created_at }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-indigo-600">{{ $order->formatted_total }}</div>
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($order->status == 'new') bg-blue-100 text-blue-800
                                @elseif($order->status == 'processing') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'completed') bg-green-100 text-green-800
                                @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $order->status_text }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-shopping-bag text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-600">У вас еще нет заказов</p>
                    <a href="{{ route('catalog') }}" class="text-indigo-600 hover:text-indigo-800 font-medium mt-2 inline-block">
                        Сделать первый заказ →
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection