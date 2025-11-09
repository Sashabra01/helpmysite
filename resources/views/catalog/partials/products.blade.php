<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="products-grid">
    @foreach($products as $product)
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <!-- Product Image -->
        <div class="bg-gradient-to-br from-blue-100 to-indigo-100 h-48 rounded-t-lg flex items-center justify-center relative">
            <i class="fas fa-tshirt text-indigo-400 text-4xl"></i>
            <div class="absolute top-3 right-3 flex flex-wrap gap-1">
                @foreach($product->attributeValues as $attributeValue)
                <span class="bg-white bg-opacity-90 text-xs px-2 py-1 rounded-full text-gray-700 border">
                    {{ $attributeValue->value }}
                </span>
                @endforeach
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="p-4">
            <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">{{ $product->name }}</h3>
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit($product->description, 60) }}</p>
            
            <div class="flex justify-between items-center mb-3">
                <span class="text-lg font-bold text-indigo-600">{{ number_format($product->price, 0, ',', ' ') }} ₽</span>
                <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded">
                    {{ $product->stock }} шт.
                </span>
            </div>
            
            <button class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                <i class="fas fa-cart-plus mr-2"></i>В корзину
            </button>
        </div>
    </div>
    @endforeach
</div>

@if($products->isEmpty())
<div class="col-span-full text-center py-12">
    <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-600 mb-2">Товары не найдены</h3>
    <p class="text-gray-500">Попробуйте изменить параметры фильтрации</p>
</div>
@endif

@if($products->hasPages())
<div class="mt-8">
    {{ $products->onEachSide(1)->links() }}
</div>
@endif