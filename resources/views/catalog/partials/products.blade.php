<div class="products-grid">
    @foreach($products as $product)
        <div class="product-card">
            <div class="product-image">
                <!-- Заглушка для изображения -->
                <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                           display: flex; align-items: center; justify-content: center; color: white; 
                           border-radius: 8px; font-weight: bold; font-size: 16px;">
                    {{ $product->name }}
                </div>
            </div>
            
            <div class="product-info">
                <h3 class="product-title">{{ $product->name }}</h3>
                <p class="product-description">{{ Str::limit($product->description, 80) }}</p>
                
                <div class="product-price">
                    <span class="price">{{ number_format($product->price, 0, '', ' ') }} ₽</span>
                </div>

                <div class="product-meta">
                    <span class="product-stock">В наличии: {{ $product->stock }} шт.</span>
                    @if($product->brand)
                        <span class="product-brand">Бренд: {{ $product->brand->name }}</span>
                    @endif
                </div>
                
                <div class="product-actions">
                    <button class="btn-add-to-cart" data-product-id="{{ $product->id }}">
                        🛒 В корзину
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($products->isEmpty())
    <div class="no-products">
        <h3>Товары не найдены</h3>
        <p>Попробуйте изменить параметры фильтрации</p>
    </div>
@endif

<style>
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 25px;
    padding: 20px 0;
}

.product-card {
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    background: white;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.product-image {
    margin-bottom: 15px;
    border-radius: 8px;
    overflow: hidden;
}

.product-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 10px;
    color: #2c3e50;
    line-height: 1.3;
}

.product-description {
    font-size: 14px;
    color: #7f8c8d;
    margin-bottom: 15px;
    line-height: 1.4;
}

.product-price {
    margin-bottom: 15px;
}

.price {
    font-size: 20px;
    font-weight: 700;
    color: #e74c3c;
}

.sale-price {
    font-size: 20px;
    font-weight: 700;
    color: #e74c3c;
}

.original-price {
    font-size: 16px;
    color: #95a5a6;
    text-decoration: line-through;
    margin-left: 8px;
}

.product-meta {
    margin-bottom: 15px;
    font-size: 12px;
    color: #7f8c8d;
}

.product-stock, .product-brand {
    display: block;
    margin-bottom: 5px;
}

.btn-add-to-cart {
    width: 100%;
    padding: 12px 20px;
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-add-to-cart:hover {
    background: linear-gradient(135deg, #2980b9, #1f618d);
    transform: translateY(-2px);
}

.no-products {
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
}

.no-products h3 {
    font-size: 24px;
    margin-bottom: 10px;
    color: #2c3e50;
}
</style>