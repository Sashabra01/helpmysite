<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            👤 Мой профиль
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Статистика -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-blue-50 p-6 rounded-lg text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ $ordersCount }}</div>
                            <div class="text-gray-600">Всего заказов</div>
                        </div>
                        <div class="bg-green-50 p-6 rounded-lg text-center">
                            <div class="text-3xl font-bold text-green-600">{{ $totalSpent }} ₽</div>
                            <div class="text-gray-600">Всего потрачено</div>
                        </div>
                        <div class="bg-purple-50 p-6 rounded-lg text-center">
                            <div class="text-3xl font-bold text-purple-600">
                                {{ \App\Models\Order::where('customer_email', $user->email)->where('status', 'completed')->count() }}
                            </div>
                            <div class="text-gray-600">Завершенных заказов</div>
                        </div>
                    </div>

                    <!-- Информация пользователя -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Личная информация</h3>
                        <div class="space-y-2">
                            <p><strong>Имя:</strong> {{ $user->name }}</p>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                            <p><strong>Зарегистрирован:</strong> {{ $user->created_at->format('d.m.Y') }}</p>
                        </div>
                    </div>

                    <!-- Быстрые действия -->
                    <div class="flex space-x-4">
                        <a href="{{ route('profile.orders') }}" 
                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                            📋 Мои заказы
                        </a>
                        <a href="/catalog" 
                           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                            🛍️ Продолжить покупки
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>