@extends('layouts.app')

@section('title', 'Добавить товар - Админ-панель')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Добавить товар</h1>
                <p class="text-gray-600 mt-2">Создание нового товара в каталоге</p>
            </div>
            <a href="{{ route('admin.products.index') }}" 
               class="border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition font-medium flex items-center space-x-2">
                <i class="fas fa-arrow-left"></i>
                <span>Назад к списку</span>
            </a>
        </div>
    </div>

    <!-- Product Form -->
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Основная информация</h2>
                    
                    <div class="space-y-4">
                        <!-- Product Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Название товара *</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- SKU -->
                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">Артикул *</label>
                            <input type="text" 
                                   id="sku" 
                                   name="sku" 
                                   value="{{ old('sku') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                            @error('sku')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Краткое описание *</label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Full Description -->
                        <div>
                            <label for="full_description" class="block text-sm font-medium text-gray-700 mb-2">Полное описание</label>
                            <textarea id="full_description" 
                                      name="full_description" 
                                      rows="5"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('full_description') }}</textarea>
                            @error('full_description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Цены и наличие</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Price -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Цена *</label>
                            <input type="number" 
                                   id="price" 
                                   name="price" 
                                   value="{{ old('price') }}"
                                   step="0.01"
                                   min="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                            @error('price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sale Price -->
                        <div>
                            <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">Цена со скидкой</label>
                            <input type="number" 
                                   id="sale_price" 
                                   name="sale_price" 
                                   value="{{ old('sale_price') }}"
                                   step="0.01"
                                   min="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('sale_price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cost Price -->
                        <div>
                            <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-2">Себестоимость</label>
                            <input type="number" 
                                   id="cost_price" 
                                   name="cost_price" 
                                   value="{{ old('cost_price') }}"
                                   step="0.01"
                                   min="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('cost_price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock -->
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Количество на складе *</label>
                            <input type="number" 
                                   id="stock" 
                                   name="stock" 
                                   value="{{ old('stock', 0) }}"
                                   min="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                            @error('stock')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Weight -->
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Вес (кг)</label>
                            <input type="number" 
                                   id="weight" 
                                   name="weight" 
                                   value="{{ old('weight') }}"
                                   step="0.01"
                                   min="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('weight')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dimensions -->
                        <div>
                            <label for="dimensions" class="block text-sm font-medium text-gray-700 mb-2">Размеры (см)</label>
                            <input type="text" 
                                   id="dimensions" 
                                   name="dimensions" 
                                   value="{{ old('dimensions') }}"
                                   placeholder="10x20x30"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('dimensions')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Categories & Brands -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Категория и бренд</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Категория *</label>
                            <select id="category_id" 
                                    name="category_id" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                <option value="">Выберите категорию</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Brand -->
                        <div>
                            <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-2">Бренд</label>
                            <select id="brand_id" 
                                    name="brand_id" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Выберите бренд</option>
                                @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('brand_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Изображения товара</h2>
                    
                    <div class="space-y-4">
                        <!-- Image Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Загрузить изображения</label>
                            <input type="file" 
                                   id="images" 
                                   name="images[]" 
                                   multiple
                                   accept="image/*"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-500 mt-2">Можно выбрать несколько файлов. Максимальный размер: 2MB на файл</p>
                            @error('images.*')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image Preview -->
                        <div id="image-preview" class="grid grid-cols-2 md:grid-cols-4 gap-4 hidden">
                            <!-- Preview will be inserted here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Settings & SEO -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Status & Settings -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Настройки</h2>
                    
                    <div class="space-y-4">
                        <!-- Active Status -->
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="is_active" class="text-sm font-medium text-gray-700">Активный товар</label>
                                <p class="text-xs text-gray-500">Товар будет отображаться в каталоге</p>
                            </div>
                            <input type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </div>

                        <!-- Featured Status -->
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="is_featured" class="text-sm font-medium text-gray-700">Рекомендуемый товар</label>
                                <p class="text-xs text-gray-500">Показывать в рекомендуемых</p>
                            </div>
                            <input type="checkbox" 
                                   id="is_featured" 
                                   name="is_featured" 
                                   value="1"
                                   {{ old('is_featured') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">SEO настройки</h2>
                    
                    <div class="space-y-4">
                        <!-- Meta Title -->
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                            <input type="text" 
                                   id="meta_title" 
                                   name="meta_title" 
                                   value="{{ old('meta_title') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Рекомендуется 50-60 символов</p>
                            @error('meta_title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meta Description -->
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                            <textarea id="meta_description" 
                                      name="meta_description" 
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('meta_description') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Рекомендуется 150-160 символов</p>
                            @error('meta_description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                    <div class="space-y-4">
                        <button type="submit" 
                                class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition font-semibold flex items-center justify-center">
                            <i class="fas fa-save mr-3"></i>Создать товар
                        </button>
                        
                        <a href="{{ route('admin.products.index') }}" 
                           class="w-full border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition font-medium flex items-center justify-center">
                            <i class="fas fa-times mr-3"></i>Отмена
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Image preview functionality
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    preview.classList.add('hidden');
    
    const files = e.target.files;
    
    if (files.length > 0) {
        preview.classList.remove('hidden');
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg border border-gray-200">
                    <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">
                        ${i + 1}
                    </div>
                `;
                preview.appendChild(div);
            }
            
            reader.readAsDataURL(file);
        }
    }
});

// Auto-generate SKU if empty
document.getElementById('name').addEventListener('blur', function() {
    const skuField = document.getElementById('sku');
    const name = this.value;
    
    if (name && !skuField.value) {
        // Generate simple SKU from name
        const sku = name.toUpperCase()
            .replace(/[^A-Z0-9]/g, '')
            .substring(0, 10) + Math.floor(Math.random() * 1000);
        skuField.value = sku;
    }
});

// Validate sale price
document.getElementById('sale_price').addEventListener('blur', function() {
    const price = parseFloat(document.getElementById('price').value);
    const salePrice = parseFloat(this.value);
    
    if (salePrice && salePrice >= price) {
        alert('Цена со скидкой должна быть меньше обычной цены');
        this.value = '';
        this.focus();
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const price = parseFloat(document.getElementById('price').value);
    const salePrice = parseFloat(document.getElementById('sale_price').value);
    
    if (salePrice && salePrice >= price) {
        e.preventDefault();
        alert('Цена со скидкой должна быть меньше обычной цены');
        document.getElementById('sale_price').focus();
    }
});
</script>

<style>
.transition {
    transition: all 0.3s ease;
}

.sticky {
    position: sticky;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
}
</style>
@endsection