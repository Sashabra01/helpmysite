<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return redirect()->back()->with('error', 'Введите поисковый запрос');
        }

        $products = Product::where('name', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%")
                          ->orWhere('sku', 'like', "%{$query}%")
                          ->with('category')
                          ->paginate(12);

        return view('search.results', compact('products', 'query'));
    }
}