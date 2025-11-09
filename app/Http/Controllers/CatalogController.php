<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogController extends Controller
{
    /**
     * Display catalog with filters
     */
    public function index(Request $request)
    {
        // Получаем параметры фильтрации
        $categorySlug = $request->input('category');
        $brandIds = $request->input('brands', []);
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = 'desc';
        $search = $request->input('search');
        $inStock = $request->input('in_stock');
        $onSale = $request->input('on_sale');

        // Обработка сортировки
        if ($sortBy === 'price_desc') {
            $sortBy = 'price';
            $sortOrder = 'desc';
        } elseif ($sortBy === 'price') {
            $sortOrder = 'asc';
        }

        // Основной запрос товаров
        $productsQuery = Product::with(['category', 'brand'])
            ->where('is_active', true);

        // Фильтр по категории
        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $productsQuery->where('category_id', $category->id);
            }
        }

        // Фильтр по брендам
        if (!empty($brandIds)) {
            $productsQuery->whereIn('brand_id', $brandIds);
        }

        // Фильтр по цене
        if ($minPrice) {
            $productsQuery->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $productsQuery->where('price', '<=', $maxPrice);
        }

        // Фильтр по наличию
        if ($inStock) {
            $productsQuery->where('stock', '>', 0);
        }

        // Фильтр по акции
        if ($onSale) {
            $productsQuery->where(function($query) {
                $query->where('sale_price', '>', 0)
                      ->whereColumn('sale_price', '<', 'price');
            });
        }

        // Поиск
        if ($search) {
            $productsQuery->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Сортировка
        $allowedSortFields = ['name', 'price', 'created_at', 'rating', 'popularity'];
        $sortBy = in_array($sortBy, $allowedSortFields) ? $sortBy : 'created_at';

        $productsQuery->orderBy($sortBy, $sortOrder);

        // Пагинация
        $products = $productsQuery->paginate(12)->withQueryString();

        // Получаем данные для фильтров
        $categories = Category::withCount(['products' => function($query) {
            $query->where('is_active', true);
        }])->where('is_active', true)->get();

        $brands = Brand::where('is_active', true)->get();

foreach ($brands as $brand) {
    try {
        $brand->products_count = $brand->products()->where('is_active', true)->count();
    } catch (\Exception $e) {
        $brand->products_count = 0;
    }
}

        // Минимальная и максимальная цена в каталоге
        $priceRange = Product::where('is_active', true)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        // Если это AJAX запрос, возвращаем JSON
        if ($request->ajax()) {
            return response()->json([
                'html' => view('catalog.partials.products', compact('products'))->render(),
                'total' => $products->total()
            ]);
        }

        return view('catalog.index', compact(
            'products', 
            'categories', 
            'brands', 
            'priceRange',
            'categorySlug'
        ));
    }

    /**
     * Display products by category
     */
    public function category($slug, Request $request)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Добавляем параметр категории к запросу
        $request->merge(['category' => $slug]);

        // Получаем дочерние категории для сайдбара
        $categories = Category::with(['children' => function($query) {
            $query->where('is_active', true)
                  ->withCount(['products' => function($q) {
                      $q->where('is_active', true);
                  }]);
        }])->where('is_active', true)
           ->whereNull('parent_id')
           ->withCount(['products' => function($query) {
               $query->where('is_active', true);
           }])->get();

        // Выполняем основной запрос из index метода
        $response = $this->index($request);
        
        // Если это AJAX, возвращаем JSON
        if ($request->ajax()) {
            return $response;
        }

        // Иначе рендерим страницу категории
        $data = $response->getData();
        
        return view('catalog.category', array_merge((array)$data, [
            'category' => $category,
            'categories' => $categories
        ]));
    }

    /**
     * Display single product
     */
    public function product($slug)
    {
        $product = Product::with(['category', 'brand', 'reviews.user'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Проверяем, есть ли товар в избранном у текущего пользователя
        if (auth()->check()) {
            $product->is_in_wishlist = auth()->user()->wishlist()
                ->where('product_id', $product->id)
                ->exists();
        } else {
            $product->is_in_wishlist = false;
        }

        // Похожие товары (из той же категории)
        $relatedProducts = Product::with(['category', 'brand'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // Увеличиваем счетчик просмотров
        $product->increment('views');

        return view('catalog.product', compact('product', 'relatedProducts'));
    }

    /**
     * Quick search for autocomplete
     */
    public function quickSearch(Request $request)
    {
        $query = $request->input('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'slug', 'price', 'sale_price', 'image')
            ->limit(8)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'sale_price' => $product->sale_price,
                    'image' => $product->image ? Storage::url($product->image) : null,
                    'url' => route('catalog.product', $product->slug),
                    'formatted_price' => number_format($product->price, 0, ',', ' ') . ' ₽'
                ];
            });

        return response()->json($products);
    }

    /**
     * Get filter counts for AJAX updates
     */
    public function filterCounts(Request $request)
    {
        $categorySlug = $request->input('category');
        $brandIds = $request->input('brands', []);
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        $countsQuery = Product::where('is_active', true);

        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $countsQuery->where('category_id', $category->id);
            }
        }

        if (!empty($brandIds)) {
            $countsQuery->whereIn('brand_id', $brandIds);
        }

        if ($minPrice) {
            $countsQuery->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $countsQuery->where('price', '<=', $maxPrice);
        }

        $totalCount = $countsQuery->count();

        // Получаем количество товаров по брендам с текущими фильтрами
        $brandCounts = Brand::withCount(['products' => function($query) use ($categorySlug, $minPrice, $maxPrice) {
            $query->where('is_active', true);
            
            if ($categorySlug) {
                $category = Category::where('slug', $categorySlug)->first();
                if ($category) {
                    $query->where('category_id', $category->id);
                }
            }
            
            if ($minPrice) {
                $query->where('price', '>=', $minPrice);
            }
            if ($maxPrice) {
                $query->where('price', '<=', $maxPrice);
            }
        }])->get()->pluck('products_count', 'id');

        return response()->json([
            'total_count' => $totalCount,
            'brand_counts' => $brandCounts
        ]);
    }

    /**
     * Get products for infinite scroll
     */
    public function loadMore(Request $request)
    {
        $page = $request->input('page', 2);
        
        $productsQuery = Product::with(['category', 'brand'])
            ->where('is_active', true);

        // Применяем те же фильтры что и в основном запросе
        $this->applyFilters($productsQuery, $request);

        $products = $productsQuery->paginate(12, ['*'], 'page', $page);

        if ($products->count() > 0) {
            return response()->json([
                'html' => view('catalog.partials.products-grid', ['products' => $products])->render(),
                'hasMore' => $products->hasMorePages()
            ]);
        }

        return response()->json(['html' => '', 'hasMore' => false]);
    }

    /**
     * Apply filters to query (helper method)
     */
    private function applyFilters($query, $request)
    {
        $categorySlug = $request->input('category');
        $brandIds = $request->input('brands', []);
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $inStock = $request->input('in_stock');
        $onSale = $request->input('on_sale');
        $search = $request->input('search');

        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if (!empty($brandIds)) {
            $query->whereIn('brand_id', $brandIds);
        }

        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        if ($inStock) {
            $query->where('stock', '>', 0);
        }

        if ($onSale) {
            $query->where(function($q) {
                $q->where('sale_price', '>', 0)
                  ->whereColumn('sale_price', '<', 'price');
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = 'desc';

        if ($sortBy === 'price_desc') {
            $sortBy = 'price';
            $sortOrder = 'desc';
        } elseif ($sortBy === 'price') {
            $sortOrder = 'asc';
        }

        $query->orderBy($sortBy, $sortOrder);
    }
}
