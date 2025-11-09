@extends('layouts.app')

@section('title', 'Каталог товаров - Laravel Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ url('/') }}" class="hover:text-indigo-600">Главная</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-400">Каталог</li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <div class="w-full lg:w-1/4 space-y-6">
            <!-- Categories -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4 text-gray-800">
                    <i class="fas fa-list mr-2"></i>Категории
                </h2>
                <div class="space-y-2">
                    @foreach($categories as $category)
                    <a href="{{ route('catalog.category', $category->slug) }}" 
                       class="flex items-center justify-between p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition group">
                        <span class="font-medium">{{ $category->name }}</span>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full group-hover:bg-indigo-100 group-hover:text-indigo-600 transition">
                            {{ $category->products_count }}
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- Price Filter -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4 text-gray-800">
                    <i class="fas fa-tag mr-2"></i>Цена
                </h2>
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <input type="number" 
                               name="min_price" 
                               placeholder="От" 
                               value="{{ request('min_price') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 price-filter">
                        <span class="text-gray-400">-</span>
                        <input type="number" 
                               name="max_price" 
                               placeholder="До" 
                               value="{{ request('max_price') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 price-filter">
                    </div>
                    <div class="text-xs text-gray-500 text-center">
                        Диапазон: {{ number_format($priceRange->min_price ?? 0, 0, ',', ' ') }} ₽ - 
                        {{ number_format($priceRange->max_price ?? 0, 0, ',', ' ') }} ₽
                    </div>
                </div>
            </div>

            <!-- Brands Filter -->
            @if($brands->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4 text-gray-800">
                    <i class="fas fa-copyright mr-2"></i>Бренды
                </h2>
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    @foreach($brands as $brand)
                    <label class="flex items-center space-x-3 cursor-pointer group">
                        <input type="checkbox" 
                               name="brands[]" 
                               value="{{ $brand->id }}"
                               {{ in_array($brand->id, request('brands', [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 brand-filter">
                        <span class="text-sm text-gray-600 group-hover:text-gray-800 flex-1">{{ $brand->name }}</span>
                        <span class="text-xs text-gray-400">({{ $brand->products_count }})</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Additional Filters -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4 text-gray-800">
                    <i class="fas fa-filter mr-2"></i>Дополнительно
                </h2>
                <div class="space-y-3">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" 
                               name="in_stock" 
                               value="1"
                               {{ request('in_stock') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 stock-filter">
                        <span class="text-sm text-gray-600">В наличии</span>
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" 
                               name="on_sale" 
                               value="1"
                               {{ request('on_sale') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 sale-filter">
                        <span class="text-sm text-gray-600">Со скидкой</span>
                    </label>
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <button onclick="applyFilters()" 
                        class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition font-medium mb-3">
                    Применить фильтры
                </button>
                <button onclick="resetFilters()" 
                        class="w-full border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition font-medium">
                    Сбросить фильтры
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="w-full lg:w-3/4">
            <!-- Header with Sort -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">Каталог товаров</h1>
                        <p class="text-gray-600">Найдено товаров: <span id="products-count">{{ $products->total() }}</span></p>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">Сортировка:</span>
                        <select name="sort_by" 
                                onchange="applyFilters()" 
                                class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>По новизне</option>
                            <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>По цене (возр.)</option>
                            <option value="price_desc" {{ request('sort_by') == 'price_desc' ? 'selected' : '' }}>По цене (убыв.)</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>По названию</option>
                            <option value="popularity" {{ request('sort_by') == 'popularity' ? 'selected' : '' }}>По популярности</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="loading-indicator" class="hidden flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                <span class="ml-3 text-gray-600">Загрузка товаров...</span>
            </div>

            <!-- Products Grid -->
            <div id="products-container">
                @include('catalog.partials.products', ['products' => $products])
            </div>
        </div>
    </div>
</div>

<script>
// Global filter functions
function applyFilters() {
    const loadingIndicator = document.getElementById('loading-indicator');
    const productsContainer = document.getElementById('products-container');
    
    // Show loading
    loadingIndicator.classList.remove('hidden');
    productsContainer.classList.add('opacity-50');

    // Collect filter data
    const params = new URLSearchParams();
    
    // Price filters
    const minPrice = document.querySelector('input[name="min_price"]').value;
    const maxPrice = document.querySelector('input[name="max_price"]').value;
    if (minPrice) params.append('min_price', minPrice);
    if (maxPrice) params.append('max_price', maxPrice);
    
    // Brand filters
    const brandCheckboxes = document.querySelectorAll('input[name="brands[]"]:checked');
    brandCheckboxes.forEach(checkbox => {
        params.append('brands[]', checkbox.value);
    });
    
    // Stock filter
    const inStock = document.querySelector('input[name="in_stock"]');
    if (inStock && inStock.checked) {
        params.append('in_stock', '1');
    }
    
    // Sale filter
    const onSale = document.querySelector('input[name="on_sale"]');
    if (onSale && onSale.checked) {
        params.append('on_sale', '1');
    }
    
    // Sort
    const sortSelect = document.querySelector('select[name="sort_by"]');
    if (sortSelect && sortSelect.value) {
        params.append('sort_by', sortSelect.value);
    }

    // AJAX request
    fetch(`/catalog?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network error');
        return response.json();
    })
    .then(data => {
        productsContainer.innerHTML = data.html;
        document.getElementById('products-count').textContent = data.total;
    })
    .catch(error => {
        console.error('Error:', error);
        productsContainer.innerHTML = `
            <div class="text-center py-12 text-red-600">
                <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                <p>Ошибка при загрузке товаров</p>
                <button onclick="applyFilters()" class="mt-4 text-indigo-600 hover:text-indigo-800">
                    Попробовать снова
                </button>
            </div>
        `;
    })
    .finally(() => {
        loadingIndicator.classList.add('hidden');
        productsContainer.classList.remove('opacity-50');
    });
}

function resetFilters() {
    // Reset all inputs
    document.querySelectorAll('input[type="number"]').forEach(input => input.value = '');
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => checkbox.checked = false);
    document.querySelector('select[name="sort_by"]').value = 'created_at';
    
    // Apply filters (show all products)
    applyFilters();
}

// Auto-apply filters with debounce
let filterTimeout;
function setupFilterListeners() {
    const filterInputs = document.querySelectorAll('.price-filter, .brand-filter, .stock-filter, .sale-filter');
    
    filterInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(applyFilters, 500);
        });
        
        input.addEventListener('change', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(applyFilters, 300);
        });
    });
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    setupFilterListeners();
    
    // Handle pagination clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            
            const loadingIndicator = document.getElementById('loading-indicator');
            const productsContainer = document.getElementById('products-container');
            
            loadingIndicator.classList.remove('hidden');
            productsContainer.classList.add('opacity-50');
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                productsContainer.innerHTML = data.html;
                document.getElementById('products-count').textContent = data.total;
            })
            .finally(() => {
                loadingIndicator.classList.add('hidden');
                productsContainer.classList.remove('opacity-50');
            });
        }
    });
});
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.transition {
    transition: all 0.3s ease;
}

#products-container {
    transition: opacity 0.3s ease;
}
</style>
@endsection
