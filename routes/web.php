<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Главная страница
Route::get('/', function () {
    return view('welcome');
});

// Маршруты аутентификации
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Маршруты регистрации
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Маршруты сброса пароля
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Поиск товаров
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Каталог товаров
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalog/category/{slug}', [CatalogController::class, 'category'])->name('catalog.category');
Route::get('/catalog/product/{slug}', [CatalogController::class, 'product'])->name('catalog.product');

// AJAX маршруты для каталога
Route::get('/catalog/quick-search', [CatalogController::class, 'quickSearch'])->name('catalog.quick-search');
Route::get('/catalog/filter-counts', [CatalogController::class, 'filterCounts'])->name('catalog.filter-counts');
Route::get('/catalog/load-more', [CatalogController::class, 'loadMore'])->name('catalog.load-more');

// Страница товара (альтернативный маршрут)
Route::get('/catalog/product/{product}', [ProductController::class, 'show'])->name('product.show');

// Корзина (только для авторизованных)
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cart}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
});

// Заказы (только для авторизованных)
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/confirmation/{order}', [OrderController::class, 'confirmation'])->name('orders.confirmation');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// Избранное (только для авторизованных)
Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add/{product}', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::post('/wishlist/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/wishlist/count', [WishlistController::class, 'count'])->name('wishlist.count');
});

// Админка - Товары
Route::prefix('admin/products')->name('admin.products.')->middleware(['auth'])->group(function () {
    Route::get('/', [AdminProductController::class, 'index'])->name('index');
    Route::get('/create', [AdminProductController::class, 'create'])->name('create');
    Route::post('/', [AdminProductController::class, 'store'])->name('store');
    Route::get('/{product}/edit', [AdminProductController::class, 'edit'])->name('edit');
    Route::put('/{product}', [AdminProductController::class, 'update'])->name('update');
    Route::delete('/{product}', [AdminProductController::class, 'destroy'])->name('destroy');
    Route::post('/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('toggle-status');
});

// Админка - Заказы
Route::prefix('admin/orders')->name('admin.orders.')->middleware(['auth'])->group(function () {
    Route::get('/', [AdminOrderController::class, 'index'])->name('index');
    Route::get('/{order}/edit', [AdminOrderController::class, 'edit'])->name('edit');
    Route::put('/{order}', [AdminOrderController::class, 'update'])->name('update');
    Route::post('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('update-status');
    Route::post('/{order}/add-item', [AdminOrderController::class, 'addItem'])->name('add-item');
    Route::delete('/{order}/items/{item}', [AdminOrderController::class, 'removeItem'])->name('remove-item');
});

// Тестовый маршрут для проверки редактирования заказа
Route::get('/test-order-edit/{id}', function ($id) {
    $order = \App\Models\Order::with('items')->find($id);
    
    if (!$order) {
        return 'Заказ не найден. ID: ' . $id;
    }
    
    $statuses = [
        'new' => 'Новый',
        'processing' => 'В обработке', 
        'completed' => 'Завершен',
        'cancelled' => 'Отменен'
    ];

    $products = \App\Models\Product::where('stock', '>', 0)->get();
    
    return view('admin.orders.edit', compact('order', 'statuses', 'products'));
})->name('test.order.edit');

// Профиль пользователя
Route::prefix('profile')->name('profile.')->middleware(['auth'])->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/orders', [ProfileController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [ProfileController::class, 'orderDetails'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [ProfileController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/orders/{order}/repeat', [ProfileController::class, 'repeatOrder'])->name('orders.repeat');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::get('/wishlist', [ProfileController::class, 'wishlist'])->name('wishlist');
    Route::get('/statistics', [ProfileController::class, 'statistics'])->name('statistics');
    Route::delete('/delete', [ProfileController::class, 'destroy'])->name('destroy');
});

// Маршруты для отзывов
Route::middleware(['auth'])->group(function () {
    Route::post('/reviews/{product}', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::get('/user/reviews', [ReviewController::class, 'userReviews'])->name('reviews.user');
});

// Публичные маршруты для отзывов
Route::get('/reviews/{product}', [ReviewController::class, 'index'])->name('reviews.index');