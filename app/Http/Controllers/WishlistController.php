<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display user's wishlist
     */
    public function index()
    {
        $wishlistItems = Auth::user()->wishlistProducts()->with(['category', 'images'])->get();
        
        return view('wishlist.index', compact('wishlistItems'));
    }

    /**
     * Add product to wishlist
     */
    public function add(Product $product)
    {
        if (Wishlist::isInWishlist(Auth::id(), $product->id)) {
            return redirect()->back()->with('info', 'Товар уже в избранном');
        }

        try {
            Wishlist::addToWishlist(Auth::id(), $product->id);
            
            return redirect()->back()->with('success', 'Товар добавлен в избранное');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка при добавлении в избранное');
        }
    }

    /**
     * Remove product from wishlist
     */
    public function remove(Product $product)
    {
        try {
            Wishlist::removeFromWishlist(Auth::id(), $product->id);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Товар удален из избранного',
                    'wishlist_count' => Auth::user()->wishlist_count
                ]);
            }
            
            return redirect()->back()->with('success', 'Товар удален из избранного');
            
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при удалении из избранного'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Ошибка при удалении из избранного');
        }
    }

    /**
     * Remove item from wishlist by wishlist ID
     */
    public function destroy(Wishlist $wishlist)
    {
        if ($wishlist->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $wishlist->delete();
            
            return redirect()->back()->with('success', 'Товар удален из избранного');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка при удалении из избранного');
        }
    }

    /**
     * Clear entire wishlist
     */
    public function clear()
    {
        try {
            Auth::user()->wishlists()->delete();
            
            return redirect()->back()->with('success', 'Избранное очищено');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка при очистке избранного');
        }
    }

    /**
     * Toggle product in wishlist (AJAX)
     */
    public function toggle(Product $product)
    {
        try {
            if (Wishlist::isInWishlist(Auth::id(), $product->id)) {
                Wishlist::removeFromWishlist(Auth::id(), $product->id);
                $added = false;
            } else {
                Wishlist::addToWishlist(Auth::id(), $product->id);
                $added = true;
            }

            return response()->json([
                'success' => true,
                'added' => $added,
                'wishlist_count' => Auth::user()->wishlist_count,
                'message' => $added ? 'Товар добавлен в избранное' : 'Товар удален из избранного'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении избранного'
            ], 500);
        }
    }

    /**
     * Get wishlist count (AJAX)
     */
    public function count()
    {
        return response()->json([
            'count' => Auth::user()->wishlist_count
        ]);
    }
}