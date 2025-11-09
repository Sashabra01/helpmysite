@extends('layouts.app')

@section('title', 'Результаты поиска - Laravel Store')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ url('/') }}" class="hover:text-indigo-600">Главная</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-400">Результаты поиска</li>
        </ol>
    </nav>

    <h1 class="text-3xl font-bold text-gray-800 mb-2">Результаты поиска</h1>
    <p class="text-gray-600 mb-8">По запросу: "<span class="font-semibold">{{ $query }}</span>"</p>

    @if($products->count() > 0)
        <div class="mb-6">
            <p class="text-gray-600">Найдено товаров: <span class="font-semibold">{{ $products->total() }}</span></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition duration-300">
                <!-- Product Image -->
                <div class="relative">
                    <a href="{{ route('product.show', $product->slug) }}" class="block">
                        <div class="h-48 bg-gray-100 rounded-t-lg flex items-center justify-center">
                            @if($product->images && count($product->images) > 0)
                                <img src="{{ asset('storage/' . $product->images[0]) }}" 
                                     alt="{{ $product->name }}" 
                                     class="max-h-full max-w-full object-contain p-4">
                            @else
                                <i class="fas fa-tshirt text-indigo-400 text-4xl"></i>
                            @endif
                        </div>
                    </a>
                </div>

                <!-- Product Info -->
                <div class="p-4">
                    <!-- Category -->
                    <div class="mb-2">
                        <span class="text-xs text-gray-500 uppercase tracking-wide">{{ $product->category->name }}</span>
                    </div>

                    <!-- Product Name -->
                    <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">
                        <a href="{{ route('product.show', $product->slug) }}" class="hover:text-indigo-600">
                            {{ $product->name }}
                        </a>
                    </h3>

                    <!-- Price -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-lg font-bold text-indigo-600">
                            {{ number_format($product->price, 0, ',', ' ') }} ₽
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        @if($product->stock > 0)
                        <form action="{{ route('cart.add', $product) }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" 
                                    class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition text-sm font-medium flex items-center justify-center">
                                <i class="fas fa-cart-plus mr-2"></i>В корзину
                            </button>
                        </form>
                        @else
                        <button disabled 
                                class="w-full bg-gray-400 text-white py-2 px-4 rounded-lg text-sm font-medium cursor-not-allowed">
                            Нет в наличии
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <i class="fas fa-search text-gray-300 text-6xl mb-6"></i>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Ничего не найдено</h2>
            <p class="text-gray-600 mb-8">Попробуйте изменить поисковый запрос или воспользуйтесь каталогом</p>
            <a href="{{ route('catalog') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold">
                <i class="fas fa-shopping-bag mr-3"></i>Перейти в каталог
            </a>
        </div>
    @endif
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection