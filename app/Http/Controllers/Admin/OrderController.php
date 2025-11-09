<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items']);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20);

        $statuses = [
            'new' => 'Новый',
            'processing' => 'В обработке',
            'shipped' => 'Отправлен',
            'delivered' => 'Доставлен',
            'completed' => 'Завершен',
            'cancelled' => 'Отменен'
        ];

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    /**
     * Show the form for editing the order.
     */
    public function edit(Order $order)
    {
        $order->load(['user', 'items.product']);
        
        $statuses = [
            'new' => 'Новый',
            'processing' => 'В обработке',
            'shipped' => 'Отправлен',
            'delivered' => 'Доставлен',
            'completed' => 'Завершен',
            'cancelled' => 'Отменен'
        ];

        $products = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->get();

        return view('admin.orders.edit', compact('order', 'statuses', 'products'));
    }

    /**
     * Update the order.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:new,processing,shipped,delivered,completed,cancelled',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        DB::beginTransaction();

        try {
            // Если заказ отменяется, возвращаем товары на склад
            if ($oldStatus !== 'cancelled' && $newStatus === 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            }

            // Если заказ восстанавливается из отмены, снова уменьшаем склад
            if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->product && $item->product->stock >= $item->quantity) {
                        $item->product->decrement('stock', $item->quantity);
                    } else {
                        throw new \Exception("Недостаточно товара '{$item->product_name}' на складе");
                    }
                }
            }

            $order->update($request->only([
                'status', 'first_name', 'last_name', 'email', 'phone', 
                'address', 'city', 'postal_code', 'notes'
            ]));

            // Пересчитываем общую сумму
            $totalAmount = $order->items->sum(function($item) {
                return $item->quantity * $item->price;
            });
            
            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Заказ успешно обновлен');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Ошибка при обновлении заказа: ' . $e->getMessage());
        }
    }

    /**
     * Update order status via AJAX.
     */
    public function updateStatus(Order $order, Request $request)
    {
        $request->validate([
            'status' => 'required|in:new,processing,shipped,delivered,completed,cancelled'
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        DB::beginTransaction();

        try {
            // Если заказ отменяется, возвращаем товары на склад
            if ($oldStatus !== 'cancelled' && $newStatus === 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            }

            // Если заказ восстанавливается из отмены, снова уменьшаем склад
            if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->product && $item->product->stock >= $item->quantity) {
                        $item->product->decrement('stock', $item->quantity);
                    } else {
                        throw new \Exception("Недостаточно товара '{$item->product_name}' на складе");
                    }
                }
            }

            $order->update(['status' => $newStatus]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Статус заказа обновлен',
                'status' => $newStatus,
                'status_text' => $this->getStatusText($newStatus)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении статуса: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add item to order.
     */
    public function addItem(Order $order, Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Проверяем наличие на складе
        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => "Недостаточно товара на складе. В наличии: {$product->stock} шт."
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Проверяем, есть ли уже этот товар в заказе
            $existingItem = $order->items()->where('product_id', $product->id)->first();

            if ($existingItem) {
                // Обновляем количество
                $existingItem->increment('quantity', $request->quantity);
            } else {
                // Добавляем новый товар
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'price' => $product->price,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                ]);
            }

            // Уменьшаем количество на складе
            $product->decrement('stock', $request->quantity);

            // Пересчитываем общую сумму
            $totalAmount = $order->items->sum(function($item) {
                return $item->quantity * $item->price;
            });
            
            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Товар добавлен в заказ',
                'order_total' => $totalAmount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при добавлении товара: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from order.
     */
    public function removeItem(Order $order, OrderItem $item)
    {
        if ($item->order_id !== $order->id) {
            abort(403);
        }

        DB::beginTransaction();

        try {
            // Возвращаем товар на склад
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }

            // Удаляем товар из заказа
            $item->delete();

            // Пересчитываем общую сумму
            $totalAmount = $order->items->sum(function($item) {
                return $item->quantity * $item->price;
            });
            
            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Товар удален из заказа',
                'order_total' => $totalAmount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении товара: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status text for display.
     */
    private function getStatusText($status)
    {
        $statuses = [
            'new' => 'Новый',
            'processing' => 'В обработке',
            'shipped' => 'Отправлен',
            'delivered' => 'Доставлен',
            'completed' => 'Завершен',
            'cancelled' => 'Отменен'
        ];

        return $statuses[$status] ?? $status;
    }

    /**
     * Get order statistics.
     */
    public function statistics()
    {
        $today = now()->format('Y-m-d');
        $weekAgo = now()->subWeek()->format('Y-m-d');
        $monthAgo = now()->subMonth()->format('Y-m-d');

        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'today_orders' => Order::whereDate('created_at', $today)->count(),
            'week_orders' => Order::where('created_at', '>=', $weekAgo)->count(),
            'month_orders' => Order::where('created_at', '>=', $monthAgo)->count(),
            'pending_orders' => Order::whereIn('status', ['new', 'processing'])->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
        ];

        // Статистика по статусам
        $statusStats = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Статистика по дням (последние 7 дней)
        $dailyStats = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'stats' => $stats,
            'status_stats' => $statusStats,
            'daily_stats' => $dailyStats
        ]);
    }
}
