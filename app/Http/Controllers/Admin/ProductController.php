<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand']);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'out_of_stock') {
                $query->where('stock', 0);
            } elseif ($request->status === 'low_stock') {
                $query->where('stock', '>', 0)->where('stock', '<=', 5);
            }
        }

        // Filter by brand
        if ($request->has('brand') && $request->brand) {
            $query->where('brand_id', $request->brand);
        }

        $products = $query->latest()->paginate(20);

        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('admin.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'description' => 'required|string',
            'full_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Generate slug from name
        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;

        // Ensure slug is unique
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $product = Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'sku' => $request->sku,
            'description' => $request->description,
            'full_description' => $request->full_description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'cost_price' => $request->cost_price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'weight' => $request->weight,
            'dimensions' => $request->dimensions,
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        // Handle image upload
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
            }
            $product->update(['images' => $imagePaths]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Товар успешно создан');
    }

    /**
     * Show the form for editing the product.
     */
    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the product.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'description' => 'required|string',
            'full_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Update slug if name changed
        if ($product->name !== $request->name) {
            $slug = Str::slug($request->name);
            $originalSlug = $slug;
            $counter = 1;

            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        } else {
            $slug = $product->slug;
        }

        $product->update([
            'name' => $request->name,
            'slug' => $slug,
            'sku' => $request->sku,
            'description' => $request->description,
            'full_description' => $request->full_description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'cost_price' => $request->cost_price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'weight' => $request->weight,
            'dimensions' => $request->dimensions,
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        // Handle image upload
        if ($request->hasFile('images')) {
            $imagePaths = $product->images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
            }
            $product->update(['images' => $imagePaths]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Товар успешно обновлен');
    }

    /**
     * Remove the product.
     */
    public function destroy(Product $product)
    {
        // Delete associated images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Товар успешно удален');
    }

    /**
     * Toggle product status.
     */
    public function toggleStatus(Product $product)
    {
        $product->update([
            'is_active' => !$product->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $product->is_active,
            'message' => $product->is_active ? 'Товар активирован' : 'Товар деактивирован'
        ]);
    }

    /**
     * Delete product image.
     */
    public function deleteImage(Product $product, $imageIndex)
    {
        $images = $product->images;
        
        if (isset($images[$imageIndex])) {
            // Delete file from storage
            Storage::disk('public')->delete($images[$imageIndex]);
            
            // Remove from array
            unset($images[$imageIndex]);
            $images = array_values($images); // Reindex array
            
            $product->update(['images' => $images]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Изображение удалено'
        ]);
    }

    /**
     * Bulk actions for products.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id'
        ]);

        $action = $request->action;
        $ids = $request->ids;

        switch ($action) {
            case 'activate':
                Product::whereIn('id', $ids)->update(['is_active' => true]);
                $message = 'Товары активированы';
                break;
                
            case 'deactivate':
                Product::whereIn('id', $ids)->update(['is_active' => false]);
                $message = 'Товары деактивированы';
                break;
                
            case 'delete':
                $products = Product::whereIn('id', $ids)->get();
                foreach ($products as $product) {
                    // Delete images
                    if ($product->images) {
                        foreach ($product->images as $image) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                    $product->delete();
                }
                $message = 'Товары удалены';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
