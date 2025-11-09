@extends('layouts.app')

@section('title', $product->name . ' - Laravel Store')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6" aria-label="Хлебные крошки">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('catalog') }}" class="hover:text-indigo-600">Каталог</a></li>
            <li><i class="fas fa-chevron-right text-xs" aria-hidden="true"></i></li>
            <li><a href="{{ route('catalog.category', $product->category->slug) }}" class="hover:text-indigo-600">{{ $product->category->name }}</a></li>
            <li><i class="fas fa-chevron-right text-xs" aria-hidden="true"></i></li>
            <li class="text-gray-400" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <!-- Product Details -->
    <div class="bg-white rounded-lg shadow-sm p-8 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Images -->
            <div class="space-y-4">
                <!-- Main Image -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-gray-200 h-96 flex items-center justify-center">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" 
                             alt="{{ $product->name }}" 
                             class="max-h-full max-w-full object-contain"
                             id="mainImage">
                    @else
                        <i class="fas fa-image text-indigo-400 text-8xl" aria-hidden="true"></i>
                    @endif
                </div>
                
                <!-- Image Thumbnails -->
                @if($product->images && count($product->images) > 1)
                <div class="flex space-x-3 overflow-x-auto py-2">
                    @foreach($product->images as $index => $image)
                    <button class="flex-shrink-0 w-20 h-20 border-2 border-transparent rounded-lg hover:border-indigo-400 transition focus:border-indigo-400 focus:outline-none {{ $index === 0 ? 'border-indigo-400' : '' }}"
                            onclick="changeImage('{{ Storage::url($image) }}', this)">
                        <img src="{{ Storage::url($image) }}" 
                             alt="{{ $product->name }} - изображение {{ $index + 1 }}" 
                             class="w-full h-full object-cover rounded-lg">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $product->name }}</h1>
                
                <!-- Brand -->
                @if($product->brand)
                <div class="mb-4">
                    <span class="text-sm text-gray-600">Бренд: </span>
                    <span class="text-sm font-medium text-indigo-600">{{ $product->brand->name }}</span>
                </div>
                @endif

                <!-- Rating -->
                <div class="flex items-center mb-6">
                    <div class="flex items-center space-x-2">
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                        <span class="text-gray-600 text-sm">
                            {{ $product->average_rating > 0 ? number_format($product->average_rating, 1) : 'Нет' }} 
                            ({{ $product->reviews_count }} {{ trans_choice('отзыв|отзыва|отзывов', $product->reviews_count) }})
                        </span>
                    </div>
                </div>
                
                <p class="text-gray-600 mb-6 leading-relaxed">{{ $product->description }}</p>

                <!-- Price & Stock -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="text-3xl font-bold text-indigo-600 mb-2">
                        @if($product->sale_price && $product->sale_price < $product->price)
                            {{ number_format($product->sale_price, 0, ',', ' ') }} ₽
                            <span class="text-lg text-gray-500 line-through ml-2">
                                {{ number_format($product->price, 0, ',', ' ') }} ₽
                            </span>
                        @else
                            {{ number_format($product->price, 0, ',', ' ') }} ₽
                        @endif
                    </div>
                    <div class="flex items-center text-sm">
                        @if($product->stock > 0)
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                            <span class="text-gray-600">В наличии: <span class="font-semibold">{{ $product->stock }} шт.</span></span>
                        @else
                            <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                            <span class="text-gray-600 font-semibold">Нет в наличии</span>
                        @endif
                    </div>
                </div>

                <!-- Add to Cart -->
                <div class="space-y-4">
                    @if($product->stock > 0)
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="add-to-cart-form">
                        @csrf
                        <div class="flex space-x-4 mb-4">
                            <div class="flex items-center border border-gray-300 rounded-lg">
                                <button type="button" class="px-4 py-2 text-gray-600 hover:bg-gray-100" onclick="decreaseQuantity()">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" 
                                       class="w-16 text-center border-0 focus:ring-0" id="quantityInput">
                                <button type="button" class="px-4 py-2 text-gray-600 hover:bg-gray-100" onclick="increaseQuantity()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" 
                                class="w-full bg-indigo-600 text-white py-4 rounded-lg hover:bg-indigo-700 transition font-semibold text-lg flex items-center justify-center">
                            <i class="fas fa-cart-plus mr-3" aria-hidden="true"></i>Добавить в корзину
                        </button>
                    </form>
                    @else
                    <button disabled class="w-full bg-gray-400 text-white py-4 rounded-lg font-semibold text-lg cursor-not-allowed">
                        <i class="fas fa-cart-plus mr-3" aria-hidden="true"></i>Нет в наличии
                    </button>
                    @endif
                    
                    <div class="flex space-x-3">
                        <!-- Wishlist Button with AJAX -->
                        <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="flex-1" id="wishlist-form">
                            @csrf
                            <button type="submit" 
                                    class="w-full border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition font-medium flex items-center justify-center wishlist-btn">
                                <i class="{{ $product->is_in_wishlist ? 'fas' : 'far' }} fa-heart mr-3 {{ $product->is_in_wishlist ? 'text-pink-500' : '' }}"></i>
                                <span class="wishlist-text">
                                    {{ $product->is_in_wishlist ? 'В избранном' : 'В избранное' }}
                                </span>
                            </button>
                        </form>
                        
                        <button class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition font-medium flex items-center justify-center">
                            <i class="fas fa-share-alt mr-3" aria-hidden="true"></i>Поделиться
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="bg-white rounded-lg shadow-sm mb-8">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button class="py-4 px-6 border-b-2 border-indigo-600 text-indigo-600 font-medium text-sm tab-button active" data-tab="description">
                    Описание
                </button>
                <button class="py-4 px-6 border-b-2 border-transparent text-gray-600 hover:text-gray-800 font-medium text-sm tab-button" data-tab="specifications">
                    Характеристики
                </button>
                <button class="py-4 px-6 border-b-2 border-transparent text-gray-600 hover:text-gray-800 font-medium text-sm tab-button" data-tab="reviews">
                    Отзывы ({{ $product->reviews_count }})
                </button>
            </nav>
        </div>
        
        <!-- Tab Content -->
        <div class="p-6">
            <!-- Description Tab -->
            <div id="description-tab" class="tab-content active">
                <div class="prose max-w-none">
                    <p class="text-gray-700">{{ $product->description }}</p>
                    @if($product->full_description)
                    <div class="mt-4 text-gray-700">
                        {!! $product->full_description !!}
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Specifications Tab -->
            <div id="specifications-tab" class="tab-content hidden">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Технические характеристики</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Basic specs -->
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Артикул:</span>
                            <span class="font-medium text-gray-800">{{ $product->sku ?? 'Не указан' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Бренд:</span>
                            <span class="font-medium text-gray-800">{{ $product->brand->name ?? 'Не указан' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Категория:</span>
                            <span class="font-medium text-gray-800">{{ $product->category->name }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Наличие:</span>
                            <span class="font-medium {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $product->stock > 0 ? 'В наличии' : 'Нет в наличии' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Reviews Tab -->
            <div id="reviews-tab" class="tab-content hidden">
                <div class="space-y-8">
                    <!-- Reviews Summary -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Average Rating -->
                        <div class="text-center bg-gray-50 rounded-lg p-6">
                            <div class="text-4xl font-bold text-gray-800 mb-2">
                                {{ $product->average_rating > 0 ? number_format($product->average_rating, 1) : '0.0' }}
                            </div>
                            <div class="flex justify-center text-yellow-400 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                            <p class="text-gray-600 text-sm">
                                {{ $product->reviews_count }} {{ trans_choice('отзыв|отзыва|отзывов', $product->reviews_count) }}
                            </p>
                        </div>

                        <!-- Rating Distribution -->
                        <div class="lg:col-span-2">
                            <h4 class="font-semibold text-gray-800 mb-4">Распределение оценок</h4>
                            <div class="space-y-2">
                                @for($rating = 5; $rating >= 1; $rating--)
                                @php
                                    $count = $product->rating_distribution[$rating] ?? 0;
                                    $percentage = $product->reviews_count > 0 ? round(($count / $product->reviews_count) * 100) : 0;
                                @endphp
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm text-gray-600 w-8">{{ $rating }} ★</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 w-12">{{ $percentage }}%</span>
                                </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Add Review Form -->
                    @auth
                        @if(!$product->hasUserReview(auth()->id()))
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Оставить отзыв</h4>
                            <form id="review-form">
                                @csrf
                                <div class="space-y-4">
                                    <!-- Rating -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Ваша оценка</label>
                                        <div class="flex space-x-1" id="rating-stars">
                                            @for($i = 1; $i <= 5; $i++)
                                            <button type="button" 
                                                    class="text-2xl text-gray-300 hover:text-yellow-400 transition"
                                                    data-rating="{{ $i }}"
                                                    onmouseover="highlightStars({{ $i }})"
                                                    onmouseout="resetStars()"
                                                    onclick="setRating({{ $i }})">
                                                <i class="fas fa-star"></i>
                                            </button>
                                            @endfor
                                        </div>
                                        <input type="hidden" name="rating" id="rating-input" value="5" required>
                                    </div>

                                    <!-- Comment -->
                                    <div>
                                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Комментарий *</label>
                                        <textarea id="comment" 
                                                  name="comment" 
                                                  rows="4"
                                                  placeholder="Поделитесь вашим мнением о товаре..."
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                  required></textarea>
                                    </div>

                                    <!-- Advantages & Disadvantages -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="advantages" class="block text-sm font-medium text-gray-700 mb-2">Достоинства</label>
                                            <textarea id="advantages" 
                                                      name="advantages" 
                                                      rows="2"
                                                      placeholder="Что вам понравилось..."
                                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                        </div>
                                        <div>
                                            <label for="disadvantages" class="block text-sm font-medium text-gray-700 mb-2">Недостатки</label>
                                            <textarea id="disadvantages" 
                                                      name="disadvantages" 
                                                      rows="2"
                                                      placeholder="Что можно улучшить..."
                                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div>
                                        <button type="submit" 
                                                class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-medium">
                                            Оставить отзыв
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @else
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                                <p class="text-blue-800">Вы уже оставляли отзыв на этот товар</p>
                            </div>
                        </div>
                        @endif
                    @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                        <i class="fas fa-user-circle text-gray-400 text-4xl mb-4"></i>
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Авторизуйтесь, чтобы оставить отзыв</h4>
                        <p class="text-gray-600 mb-4">Только зарегистрированные пользователи могут оставлять отзывы</p>
                        <div class="flex justify-center space-x-4">
                            <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                                Войти
                            </a>
                            <a href="{{ route('register') }}" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-50 transition font-medium">
                                Регистрация
                            </a>
                        </div>
                    </div>
                    @endauth

                    <!-- Reviews List -->
                    <div id="reviews-container">
                        <div class="flex justify-center items-center py-12">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                        </div>
                    </div>

                    <!-- Reviews Pagination -->
                    <div id="reviews-pagination" class="hidden"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Похожие товары</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-indigo-300 transition hover:shadow-md">
                <div class="bg-white rounded-lg p-3 mb-3 flex items-center justify-center h-32">
                    @if($relatedProduct->image)
                        <img src="{{ Storage::url($relatedProduct->image) }}" 
                             alt="{{ $relatedProduct->name }}" 
                             class="max-h-full max-w-full object-contain">
                    @else
                        <i class="fas fa-image text-indigo-400 text-3xl" aria-hidden="true"></i>
                    @endif
                </div>
                <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">{{ $relatedProduct->name }}</h3>
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-indigo-600">{{ number_format($relatedProduct->price, 0, ',', ' ') }} ₽</span>
                    <a href="{{ route('catalog.product', $relatedProduct->slug) }}" 
                       class="text-indigo-600 hover:text-indigo-800 text-sm font-medium transition"
                       aria-label="Подробнее о товаре {{ $relatedProduct->name }}">
                        Подробнее →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
// Product page JavaScript
let currentRating = 5;

function changeImage(src, element) {
    document.getElementById('mainImage').src = src;
    
    // Remove active class from all thumbnails
    document.querySelectorAll('.flex.space-x-3 button').forEach(btn => {
        btn.classList.remove('border-indigo-400');
        btn.classList.add('border-transparent');
    });
    
    // Add active class to clicked thumbnail
    element.classList.remove('border-transparent');
    element.classList.add('border-indigo-400');
}

function increaseQuantity() {
    const input = document.getElementById('quantityInput');
    const max = parseInt(input.max);
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantityInput');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-indigo-600', 'text-indigo-600');
                btn.classList.add('border-transparent', 'text-gray-600');
            });
            
            tabContents.forEach(content => {
                content.classList.remove('active');
                content.classList.add('hidden');
            });
            
            // Add active class to current button and content
            this.classList.add('active', 'border-indigo-600', 'text-indigo-600');
            this.classList.remove('border-transparent', 'text-gray-600');
            
            document.getElementById(`${tabId}-tab`).classList.add('active');
            document.getElementById(`${tabId}-tab`).classList.remove('hidden');

            // Load reviews when reviews tab is activated
            if (tabId === 'reviews') {
                loadReviews();
            }
        });
    });

    // Initialize rating stars
    setRating(5);
    
    // Add to cart AJAX
    const addToCartForm = document.querySelector('.add-to-cart-form');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            
            // Show loading
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i>Добавление...';
            submitBtn.disabled = true;
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart counter in header
                    updateCartCounter(data.cart_count);
                    
                    // Show success message
                    showToast('Товар добавлен в корзину!', 'success');
                    
                    // Reset button after delay
                    setTimeout(() => {
                        submitBtn.innerHTML = originalHtml;
                        submitBtn.disabled = false;
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Ошибка при добавлении в корзину', 'error');
                submitBtn.innerHTML = originalHtml;
                submitBtn.disabled = false;
            });
        });
    }
    
    // Wishlist toggle with AJAX
    const wishlistForm = document.getElementById('wishlist-form');
    if (wishlistForm) {
        wishlistForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const wishlistBtn = this.querySelector('.wishlist-btn');
            const wishlistIcon = wishlistBtn.querySelector('i');
            const wishlistText = wishlistBtn.querySelector('.wishlist-text');
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update icon and text
                    if (data.added) {
                        wishlistIcon.classList.remove('far');
                        wishlistIcon.classList.add('fas', 'text-pink-500');
                        wishlistText.textContent = 'В избранном';
                    } else {
                        wishlistIcon.classList.remove('fas', 'text-pink-500');
                        wishlistIcon.classList.add('far');
                        wishlistText.textContent = 'В избранное';
                    }
                    
                    // Update counter in header
                    const wishlistCounter = document.querySelector('.wishlist-counter');
                    if (wishlistCounter) {
                        if (data.wishlist_count > 0) {
                            wishlistCounter.textContent = data.wishlist_count;
                            wishlistCounter.classList.remove('hidden');
                        } else {
                            wishlistCounter.classList.add('hidden');
                        }
                    }
                    
                    // Show toast message
                    showToast(data.message, 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Ошибка при обновлении избранного', 'error');
            });
        });
    }

    // Review form submission
    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('{{ route("reviews.store", $product) }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Отзыв успешно добавлен!', 'success');
                    this.reset();
                    setRating(5);
                    loadReviews(); // Reload reviews
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Ошибка при добавлении отзыва', 'error');
            });
        });
    }
});

// Rating stars functionality
function highlightStars(rating) {
    const stars = document.querySelectorAll('#rating-stars button');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.querySelector('i').classList.add('text-yellow-400');
            star.querySelector('i').classList.remove('text-gray-300');
        } else {
            star.querySelector('i').classList.remove('text-yellow-400');
            star.querySelector('i').classList.add('text-gray-300');
        }
    });
}

function resetStars() {
    const stars = document.querySelectorAll('#rating-stars button');
    stars.forEach((star, index) => {
        if (index < currentRating) {
            star.querySelector('i').classList.add('text-yellow-400');
            star.querySelector('i').classList.remove('text-gray-300');
        } else {
            star.querySelector('i').classList.remove('text-yellow-400');
            star.querySelector('i').classList.add('text-gray-300');
        }
    });
}

function setRating(rating) {
    currentRating = rating;
    document.getElementById('rating-input').value = rating;
    resetStars();
}

// Load reviews
function loadReviews(page = 1) {
    const container = document.getElementById('reviews-container');
    const pagination = document.getElementById('reviews-pagination');
    
    container.innerHTML = `
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
        </div>
    `;
    
    fetch(`{{ route('reviews.index', $product) }}?page=${page}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.reviews.data.length > 0) {
            container.innerHTML = data.reviews.data.map(review => `
                <div class="border-b border-gray-200 pb-6 mb-6 last:border-b-0 last:mb-0">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                <span class="text-indigo-600 font-semibold text-sm">
                                    ${review.user.name.substring(0, 2).toUpperCase()}
                                </span>
                            </div>
                            <div>
                                <h5 class="font-semibold text-gray-800">${review.user.name}</h5>
                                <div class="flex items-center space-x-2 text-sm text-gray-600">
                                    <div class="flex text-yellow-400">
                                        ${'★'.repeat(review.rating)}${'☆'.repeat(5 - review.rating)}
                                    </div>
                                    <span>${review.formatted_date}</span>
                                    ${review.is_verified ? '<span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Проверенная покупка</span>' : ''}
                                </div>
                            </div>
                        </div>
                        ${review.user_id === {{ auth()->id() ?? 0 }} ? `
                        <div class="flex space-x-2">
                            <button onclick="editReview(${review.id})" class="text-indigo-600 hover:text-indigo-800 text-sm">Редактировать</button>
                            <button onclick="deleteReview(${review.id})" class="text-red-600 hover:text-red-800 text-sm">Удалить</button>
                        </div>
                        ` : ''}
                    </div>
                    
                    <p class="text-gray-700 mb-3">${review.comment}</p>
                    
                    ${review.advantages ? `
                    <div class="mb-2">
                        <span class="text-sm font-medium text-green-600">Достоинства:</span>
                        <p class="text-sm text-gray-600">${review.advantages}</p>
                    </div>
                    ` : ''}
                    
                    ${review.disadvantages ? `
                    <div class="mb-2">
                        <span class="text-sm font-medium text-red-600">Недостатки:</span>
                        <p class="text-sm text-gray-600">${review.disadvantages}</p>
                    </div>
                    ` : ''}
                </div>
            `).join('');
            
            // Add pagination if needed
            if (data.reviews.links) {
                pagination.innerHTML = data.reviews.links;
                pagination.classList.remove('hidden');
            } else {
                pagination.classList.add('hidden');
            }
        } else {
            container.innerHTML = `
                <div class="text-center py-12">
                    <i class="far fa-comment text-gray-300 text-4xl mb-4"></i>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Пока нет отзывов</h4>
                    <p class="text-gray-600">Будьте первым, кто оставит отзыв об этом товаре</p>
                </div>
            `;
            pagination.classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Error loading reviews:', error);
        container.innerHTML = `
            <div class="text-center py-12 text-red-600">
                <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                <p>Ошибка при загрузке отзывов</p>
            </div>
        `;
    });
}

function updateCartCounter(count) {
    const cartCounter = document.querySelector('.cart-counter');
    if (cartCounter) {
        if (count > 0) {
            cartCounter.textContent = count;
            cartCounter.classList.remove('hidden');
        } else {
            cartCounter.classList.add('hidden');
        }
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white font-medium z-50 transform transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => toast.classList.add('translate-x-0'), 10);
    
    // Auto remove
    setTimeout(() => {
        toast.classList.remove('translate-x-0');
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Handle review pagination
document.addEventListener('click', function(e) {
    if (e.target.matches('#reviews-pagination a, #reviews-pagination a *')) {
        e.preventDefault();
        const url = new URL(e.target.closest('a').href);
        const page = url.searchParams.get('page') || 1;
        loadReviews(page);
    }
});
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.prose {
    line-height: 1.6;
}

.prose p {
    margin-bottom: 1rem;
}

.tab-content {
    transition: opacity 0.3s ease;
}

.tab-content:not(.active) {
    display: none;
}

.tab-content.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.transition {
    transition: all 0.3s ease;
}
</style>
@endsection