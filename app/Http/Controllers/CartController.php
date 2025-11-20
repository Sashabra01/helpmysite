<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display shopping cart
     */
    public function index()
    {
        // ВРЕМЕННО: используем ID 1 если пользователь не авторизован
        $userId = Auth::id() ?? 1;
        
        $cartItems = Cart::where('user_id', $userId)->with('product')->get();
        $total = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->price;
        });
        
        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * Add product to cart
     */
    public function add(Product $product, Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock
        ]);

        // ВРЕМЕННО: используем ID 1 если пользователь не авторизован
        $userId = Auth::id() ?? 1;

        $cartItem = Cart::where('user_id', $userId)
                       ->where('product_id', $product->id)
                       ->first();

        if ($cartItem) {
            // Обновляем количество
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($newQuantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Недостаточно товара на складе'
                ]);
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Создаем новую запись
            Cart::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'quantity' => $request->quantity
            ]);
        }

        $cartCount = Cart::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'message' => 'Товар добавлен в корзину',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update(Cart $cart, Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $cart->product->stock
        ]);

        // ВРЕМЕННО: используем ID 1 если пользователь не авторизован
        $userId = Auth::id() ?? 1;

        // Проверяем что корзина принадлежит пользователю (или временному пользователю)
        if ($cart->user_id !== $userId) {
            abort(403);
        }

        $cart->update(['quantity' => $request->quantity]);

        $cartItems = Cart::where('user_id', $userId)->with('product')->get();
        $itemTotal = $cart->quantity * $cart->product->price;
        $total = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->price;
        });

        return response()->json([
            'success' => true,
            'item_total' => $itemTotal,
            'total' => $total,
            'cart_count' => $cartItems->count()
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove(Cart $cart)
    {
        // ВРЕМЕННО: используем ID 1 если пользователь не авторизован
        $userId = Auth::id() ?? 1;

        // Проверяем что корзина принадлежит пользователю (или временному пользователю)
        if ($cart->user_id !== $userId) {
            abort(403);
        }

        $cart->delete();

        $cartItems = Cart::where('user_id', $userId)->with('product')->get();
        $total = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->price;
        });

        return response()->json([
            'success' => true,
            'message' => 'Товар удален из корзины',
            'total' => $total,
            'cart_count' => $cartItems->count()
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        // ВРЕМЕННО: используем ID 1 если пользователь не авторизован
        $userId = Auth::id() ?? 1;

        Cart::where('user_id', $userId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Корзина очищена',
            'cart_count' => 0,
            'total' => 0
        ]);
    }

    /**
     * Get cart count (for header)
     */
    public function count()
    {
        // ВРЕМЕННО: используем ID 1 если пользователь не авторизован
        $userId = Auth::id() ?? 1;

        $count = Cart::where('user_id', $userId)->count();

        return response()->json([
            'count' => $count
        ]);
    }
}