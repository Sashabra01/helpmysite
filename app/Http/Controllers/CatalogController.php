<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Display catalog with filters
     */
    public function index(Request $request)
    {
        // Основной запрос
        $productsQuery = Product::where('is_active', true);
        
        // Получаем параметры фильтрации
        $categorySlug = $request->input('category');
        $brandIds = $request->input('brands', []);
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $colors = $request->input('colors', []);
        $sizes = $request->input('sizes', []);
        $search = $request->input('search');

        // Фильтр по категории
        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $productsQuery->where('category_id', $category->id);
            }
        }

        // Фильтр по брендам
        if (!empty($brandIds) && is_array($brandIds)) {
            $productsQuery->whereIn('brand_id', $brandIds);
        }

        // Фильтр по цене
        if ($minPrice) {
            $productsQuery->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $productsQuery->where('price', '<=', $maxPrice);
        }

        // Фильтр по цвету
        if (!empty($colors) && is_array($colors)) {
            $productsQuery->whereIn('color', $colors);
        }

        // Фильтр по размеру
        if (!empty($sizes) && is_array($sizes)) {
            $productsQuery->whereIn('size', $sizes);
        }

        // Поиск
        if ($search) {
            $productsQuery->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Сортировка
        $sortBy = $request->input('sort_by', 'created_at');
        switch ($sortBy) {
            case 'price_asc':
                $productsQuery->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $productsQuery->orderBy('price', 'desc');
                break;
            case 'name':
                $productsQuery->orderBy('name', 'asc');
                break;
            default:
                $productsQuery->orderBy('created_at', 'desc');
                break;
        }

        // Пагинация
        $products = $productsQuery->paginate(12)->withQueryString();

        
if ($request->ajax()) {
    return response()->json([
        'html' => view('catalog.partials.products', compact('products'))->render(),
        'total' => $products->total()
    ]);
}

        // Данные для фильтров
        $categories = Category::all();
        $brands = Brand::all();

        // Получаем доступные цвета и размеры
        $availableColors = Product::where('is_active', true)
            ->whereNotNull('color')
            ->where('color', '!=', '')
            ->distinct()
            ->pluck('color')
            ->filter()
            ->values();

        $availableSizes = Product::where('is_active', true)
            ->whereNotNull('size')
            ->where('size', '!=', '')
            ->distinct()
            ->pluck('size')
            ->filter()
            ->values();

        // Диапазон цен
        $priceRange = [
            'min_price' => Product::min('price') ?? 0,
            'max_price' => Product::max('price') ?? 10000
        ];

        return view('catalog.index', compact(
            'products', 
            'categories', 
            'brands', 
            'priceRange',
            'availableColors',
            'availableSizes'
        ));
    }

    /**
     * Display products by category
     */
    public function category($slug, Request $request)
    {
        $category = Category::where('slug', $slug)->first();
        
        if (!$category) {
            abort(404);
        }

        // Добавляем параметр категории к запросу
        $request->merge(['category' => $slug]);
        
        return $this->index($request);
    }

    /**
     * Display single product
     */
    public function product($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            abort(404);
        }

        // Похожие товары
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

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
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'slug', 'price')
            ->limit(5)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'url' => route('catalog.product', $product->slug),
                    'formatted_price' => number_format($product->price, 0, '', ' ') . ' ₽'
                ];
            });

        return response()->json($products);
    }

    /**
     * Get filter data for AJAX
     */
    public function getFilterData(Request $request)
    {
        $categorySlug = $request->input('category');
        
        $query = Product::where('is_active', true);

        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        $colors = $query->whereNotNull('color')
            ->where('color', '!=', '')
            ->distinct()
            ->pluck('color');

        $sizes = $query->whereNotNull('size')
            ->where('size', '!=', '')
            ->distinct()
            ->pluck('size');

        return response()->json([
            'colors' => $colors,
            'sizes' => $sizes
        ]);
    }
}
