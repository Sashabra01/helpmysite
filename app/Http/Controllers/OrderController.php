<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display checkout page
     */
    public function checkout()
    {
        $user = Auth::user();
        
        // Проверяем аутентификацию
        if (!$user) {
            return redirect()->route('login');
        }

        $cartItems = $user->cartItems()->with('product')->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Ваша корзина пуста');
        }

        // Проверяем наличие товаров
        foreach ($cartItems as $item) {
            if (!$item->product || $item->product->stock < $item->quantity) {
                return redirect()->route('cart.index')->with('error', 
                    "Товар '{$item->product->name}' недоступен в нужном количестве. В наличии: {$item->product->stock} шт.");
            }
        }

        $total = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->price;
        });

        return view('orders.checkout', compact('cartItems', 'total', 'user'));
    }

    /**
     * Create new order
     */
    public function store(Request $request)
    {
        // Проверяем аутентификацию
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Необходима авторизация'
            ], 401);
        }

        $user = Auth::user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Ваша корзина пуста'
            ], 400);
        }

        // Валидация данных
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'payment_method' => 'required|in:card,cash,online',
            'shipping_method' => 'required|in:pickup,delivery',
            'notes' => 'nullable|string|max:1000',
            'terms' => 'required|accepted'
        ]);

        // Проверяем наличие товаров
        foreach ($cartItems as $item) {
            if (!$item->product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Некоторые товары в корзине не найдены'
                ], 400);
            }

            if ($item->product->stock < $item->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Товар '{$item->product->name}' недоступен в нужном количестве. В наличии: {$item->product->stock} шт."
                ], 400);
            }
        }

        DB::beginTransaction();

        try {
            // Рассчитываем стоимость доставки
            $subtotal = $cartItems->sum(function($item) {
                return $item->quantity * $item->product->price;
            });
            
            $shippingCost = 0;
            if ($validated['shipping_method'] === 'delivery') {
                $shippingCost = 300;
                // Бесплатная доставка от 3000 руб
                if ($subtotal >= 3000) {
                    $shippingCost = 0;
                }
            }

            $totalAmount = $subtotal + $shippingCost;

            // Создаем заказ
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . date('YmdHis') . Str::random(4),
                'status' => 'new',
                'total_amount' => $totalAmount,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'payment_method' => $validated['payment_method'],
                'shipping_method' => $validated['shipping_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Создаем элементы заказа и обновляем склад
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku ?? 'N/A',
                ]);

                // Уменьшаем количество на складе
                $item->product->decrement('stock', $item->quantity);
            }

            // Очищаем корзину
            $user->cartItems()->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'redirect_url' => route('orders.confirmation', $order)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Order creation error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при создании заказа: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display order confirmation
     */
    public function confirmation(Order $order)
    {
        // Проверяем что заказ принадлежит пользователю
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items');

        return view('orders.confirmation', compact('order'));
    }

    /**
     * Display user's orders
     */
    public function index()
    {
        $orders = Auth::user()->orders()
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display order details
     */
    public function show(Order $order)
    {
        // Проверяем что заказ принадлежит пользователю
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items');

        return view('orders.show', compact('order'));
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order)
    {
        // Проверяем что заказ принадлежит пользователю
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Проверяем что заказ можно отменить
        if (!in_array($order->status, ['new', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Невозможно отменить заказ с текущим статусом'
            ]);
        }

        DB::beginTransaction();

        try {
            // Возвращаем товары на склад
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            // Обновляем статус заказа
            $order->update(['status' => 'cancelled']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Заказ успешно отменен'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отмене заказа: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate shipping cost
     */
    public function calculateShipping(Request $request)
    {
        $request->validate([
            'city' => 'required|string',
            'shipping_method' => 'required|in:pickup,delivery'
        ]);

        $cartItems = Auth::user()->cartItems()->with('product')->get();
        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->price;
        });

        $shippingCost = 0;

        if ($request->shipping_method === 'delivery') {
            // Базовая стоимость доставки
            $shippingCost = 300;
            
            // Бесплатная доставка от 3000 руб
            if ($subtotal >= 3000) {
                $shippingCost = 0;
            }
        }

        return response()->json([
            'shipping_cost' => $shippingCost,
            'total' => $subtotal + $shippingCost,
            'free_shipping_threshold' => 3000,
            'remaining_for_free_shipping' => max(0, 3000 - $subtotal)
        ]);
    }
}
