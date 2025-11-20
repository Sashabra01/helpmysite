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
            <form id="filter-form" method="GET" action="{{ url()->current() }}">
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
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                {{ $category->products_count ?? 0 }}
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
                            Диапазон: {{ number_format($priceRange['min_price'] ?? 0, 0, '', ' ') }} ₽ - 
                            {{ number_format($priceRange['max_price'] ?? 0, 0, '', ' ') }} ₽
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
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Color Filter -->
                @if(isset($availableColors) && $availableColors->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4 text-gray-800">
                        <i class="fas fa-palette mr-2"></i>Цвет
                    </h2>
                    <div class="space-y-2 max-h-60 overflow-y-auto">
                        @foreach($availableColors as $color)
                        <label class="flex items-center space-x-3 cursor-pointer group">
                            <input type="checkbox" 
                                   name="colors[]" 
                                   value="{{ $color }}"
                                   {{ in_array($color, request('colors', [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 color-filter">
                            <span class="text-sm text-gray-600 group-hover:text-gray-800 flex-1">{{ $color }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Size Filter -->
                @if(isset($availableSizes) && $availableSizes->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4 text-gray-800">
                        <i class="fas fa-ruler mr-2"></i>Размер
                    </h2>
                    <div class="space-y-2 max-h-60 overflow-y-auto">
                        @foreach($availableSizes as $size)
                        <label class="flex items-center space-x-3 cursor-pointer group">
                            <input type="checkbox" 
                                   name="sizes[]" 
                                   value="{{ $size }}"
                                   {{ in_array($size, request('sizes', [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 size-filter">
                            <span class="text-sm text-gray-600 group-hover:text-gray-800 flex-1">{{ $size }}</span>
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
                    </div>
                </div>

                <!-- Hidden sort field -->
                <input type="hidden" name="sort_by" id="sort-input" value="{{ request('sort_by', 'created_at') }}">

                <!-- Filter Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <button type="button" onclick="applyFilters()" 
                            class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition font-medium mb-3">
                        Применить фильтры
                    </button>
                    <button type="button" onclick="resetFilters()" 
                            class="w-full border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition font-medium">
                        Сбросить фильтры
                    </button>
                </div>
            </form>
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
                        <select onchange="updateSort(this.value)" 
                                class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>По новизне</option>
                            <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>По цене (возр.)</option>
                            <option value="price_desc" {{ request('sort_by') == 'price_desc' ? 'selected' : '' }}>По цене (убыв.)</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>По названию</option>
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

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="mt-8">
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function updateSort(value) {
    document.getElementById('sort-input').value = value;
    applyFilters();
}

function applyFilters() {
    const loadingIndicator = document.getElementById('loading-indicator');
    const productsContainer = document.getElementById('products-container');
    
    // Show loading
    loadingIndicator.classList.remove('hidden');
    productsContainer.style.opacity = '0.5';

    // Collect all form data
    const formData = new FormData(document.getElementById('filter-form'));
    const params = new URLSearchParams();
    
    // Add all form data to params
    for (const [key, value] of formData.entries()) {
        if (value) {
            if (key.includes('[]')) {
                params.append(key, value);
            } else {
                params.set(key, value);
            }
        }
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
        if (data.html) {
            productsContainer.innerHTML = data.html;
            document.getElementById('products-count').textContent = data.total;
            
            // Update URL without page reload
            const newUrl = `${window.location.pathname}?${params.toString()}`;
            window.history.pushState({ path: newUrl }, '', newUrl);
        }
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
        productsContainer.style.opacity = '1';
    });
}

function resetFilters() {
    // Reset all inputs
    document.querySelectorAll('input[type="number"]').forEach(input => input.value = '');
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => checkbox.checked = false);
    document.querySelector('select').value = 'created_at';
    document.getElementById('sort-input').value = 'created_at';
    
    // Apply filters (show all products)
    applyFilters();
}

// Auto-apply filters with debounce
let filterTimeout;
function setupFilterListeners() {
    const filterInputs = document.querySelectorAll('.price-filter, .brand-filter, .color-filter, .size-filter, .stock-filter');
    
    filterInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(applyFilters, 800);
        });
        
        input.addEventListener('change', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(applyFilters, 500);
        });
    });
}

// Handle pagination with AJAX
function setupPagination() {
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            
            const loadingIndicator = document.getElementById('loading-indicator');
            const productsContainer = document.getElementById('products-container');
            
            loadingIndicator.classList.remove('hidden');
            productsContainer.style.opacity = '0.5';
            
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
                
                // Update URL
                window.history.pushState({ path: url }, '', url);
            })
            .finally(() => {
                loadingIndicator.classList.add('hidden');
                productsContainer.style.opacity = '1';
            });
        }
    });
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    setupFilterListeners();
    setupPagination();
});

// Handle browser back/forward buttons
window.addEventListener('popstate', function() {
    applyFilters();
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

.max-h-60 {
    max-height: 15rem;
}

.overflow-y-auto {
    overflow-y: auto;
}
</style>
@endsection
