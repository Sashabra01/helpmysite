@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <!-- Боковая панель категорий -->
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-list me-2"></i> Категории
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach($categories as $category)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ route('products.index', ['category' => $category->id]) }}" class="text-decoration-none text-dark">
                                        {{ $category->name }}
                                    </a>
                                    <span class="badge bg-secondary rounded-pill">{{ $category->products_count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Фильтр по цене -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-tag me-2"></i> Цена
                    </div>
                    <div class="card-body">
                        <form action="{{ route('products.index') }}" method="GET" id="price-filter-form">
                            <div class="input-group mb-3">
                                <input type="number" name="price_from" class="form-control" placeholder="От" value="{{ request('price_from') }}">
                                <span class="input-group-text">-</span>
                                <input type="number" name="price_to" class="form-control" placeholder="До" value="{{ request('price_to') }}">
                            </div>
                            <button type="submit" class="btn btn-outline-primary w-100">Применить</button>
                        </form>
                        <small class="text-muted mt-2 d-block">
                            Диапазон: {{ $minPrice }} ₽ - {{ $maxPrice }} ₽
                        </small>
                    </div>
                </div>
            </div>

            <!-- Основной контент -->
            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h5 m-0">Каталог товаров</h2>
                        <div class="d-flex align-items-center">
                            <span class="me-3">Найдено товаров: {{ $products->total() }}</span>
                            <label for="sort" class="me-2">Сортировка:</label>
                            <select id="sort" class="form-select form-select-sm" onchange="window.location.href = this.value;">
                                <option value="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'newest'])) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>По новизне</option>
                                <option value="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'price_asc'])) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>По цене (возрастание)</option>
                                <option value="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'price_desc'])) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>По цене (убывание)</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @foreach($products as $product)
                                <div class="col-md-4">
                                    <div class="card h-100">
                                        <!-- Изображение товара -->
                                        <img src="{{ asset('images/' . $product->image) }}"
                                             alt="{{ $product->name }}"
                                             class="card-img-top"
                                             style="height: 200px; object-fit: cover;">

                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title">{{ $product->name }}</h5>
                                            <p class="card-text flex-grow-1">{{ $product->description }}</p>
                                            <p class="card-text fw-bold">{{ number_format($product->price, 0, ',', ' ') }} ₽</p>
                                            <a href="{{ route('cart.add', $product->id) }}" class="btn btn-outline-primary mt-auto">В корзину</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Пагинация -->
                        <div class="mt-4">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Для обработки фильтра по цене без перезагрузки страницы (опционально)
        document.getElementById('price-filter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            let url = new URL(window.location.href);
            url.searchParams.set('price_from', formData.get('price_from'));
            url.searchParams.set('price_to', formData.get('price_to');
            window.location.href = url.toString();
        });
    </script>
@endpush