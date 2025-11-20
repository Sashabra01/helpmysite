<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Display user's profile
     */
    public function index()
    {
        $user = Auth::user();
        $recentOrders = $user->orders()->with('items')->latest()->take(5)->get();
        $wishlistCount = $user->wishlist_count;
        
        // Статистика заказов
        $stats = [
            'total_orders' => $user->orders()->count(),
            'total_spent' => $user->orders()->where('status', 'completed')->sum('total_amount'),
            'pending_orders' => $user->orders()->whereIn('status', ['new', 'processing'])->count(),
            'completed_orders' => $user->orders()->where('status', 'completed')->count(),
            'cancelled_orders' => $user->orders()->where('status', 'cancelled')->count(),
        ];
        
        return view('profile.index', compact('user', 'stats', 'recentOrders', 'wishlistCount'));
    }

    /**
     * Display user's order history
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->orders()->with(['items' => function($query) {
            $query->with('product');
        }])->latest();

        // Фильтрация по статусу
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Поиск по номеру заказа
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('order_number', 'like', "%{$search}%");
        }

        // Фильтрация по дате
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(10);
        $statuses = Order::STATUSES;

        return view('profile.orders', compact('orders', 'user', 'statuses'));
    }

    /**
     * Display order details
     */
    public function orderDetails(Order $order)
    {
        $user = Auth::user();
        
        // Проверяем что заказ принадлежит пользователю
        if ($order->user_id !== $user->id) {
            abort(403, 'Доступ запрещен');
        }

        $order->load(['items' => function($query) {
            $query->with('product');
        }]);

        return view('profile.order-details', compact('order', 'user'));
    }

    /**
     * Cancel user's order
     */
    public function cancelOrder(Order $order)
    {
        $user = Auth::user();
        
        // Проверяем что заказ принадлежит пользователю
        if ($order->user_id !== $user->id) {
            abort(403, 'Доступ запрещен');
        }

        // Проверяем что заказ можно отменить
        if (!$order->canBeCancelled()) {
            return back()->with('error', 'Невозможно отменить заказ с текущим статусом');
        }

        try {
            // Возвращаем товары на склад
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            // Обновляем статус заказа
            $order->update(['status' => 'cancelled']);

            return redirect()->route('profile.orders')
                            ->with('success', 'Заказ #' . $order->order_number . ' успешно отменен');

        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при отмене заказа: ' . $e->getMessage());
        }
    }

    /**
     * Repeat order (create new order with same items)
     */
    public function repeatOrder(Order $order)
    {
        $user = Auth::user();
        
        // Проверяем что заказ принадлежит пользователю
        if ($order->user_id !== $user->id) {
            abort(403, 'Доступ запрещен');
        }

        try {
            // Создаем новый заказ
            $newOrder = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . date('YmdHis') . rand(100, 999),
                'status' => 'new',
                'total_amount' => $order->total_amount,
                'first_name' => $order->first_name,
                'last_name' => $order->last_name,
                'email' => $order->email,
                'phone' => $order->phone,
                'address' => $order->address,
                'city' => $order->city,
                'postal_code' => $order->postal_code,
                'payment_method' => $order->payment_method,
                'shipping_method' => $order->shipping_method,
            ]);

            // Копируем товары из оригинального заказа
            foreach ($order->items as $item) {
                $newOrder->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'product_name' => $item->product_name,
                    'product_sku' => $item->product_sku,
                ]);

                // Уменьшаем количество на складе
                if ($item->product) {
                    $item->product->decrement('stock', $item->quantity);
                }
            }

            return redirect()->route('orders.confirmation', $newOrder)
                            ->with('success', 'Заказ #' . $newOrder->order_number . ' создан на основе предыдущего заказа');

        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при создании заказа: ' . $e->getMessage());
        }
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $user->update($request->only([
                'name', 'email', 'phone', 'address', 'city', 'postal_code'
            ]));

            return redirect()->route('profile.index')
                            ->with('success', 'Профиль успешно обновлен');

        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при обновлении профиля: ' . $e->getMessage());
        }
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Проверяем текущий пароль
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Текущий пароль неверен');
        }

        try {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return redirect()->route('profile.index')
                            ->with('success', 'Пароль успешно изменен');

        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при изменении пароля: ' . $e->getMessage());
        }
    }

    /**
     * Show user's wishlist
     */
    public function wishlist()
    {
        $user = Auth::user();
        $wishlistItems = $user->wishlistProducts()
                             ->with('category')
                             ->paginate(12);

        return view('profile.wishlist', compact('user', 'wishlistItems'));
    }

    /**
     * Get user statistics (API endpoint)
     */
    public function statistics()
    {
        $user = Auth::user();

        $stats = [
            'total_orders' => $user->orders()->count(),
            'total_spent' => $user->orders()->where('status', 'completed')->sum('total_amount'),
            'pending_orders' => $user->orders()->whereIn('status', ['new', 'processing'])->count(),
            'completed_orders' => $user->orders()->where('status', 'completed')->count(),
            'cancelled_orders' => $user->orders()->where('status', 'cancelled')->count(),
            'wishlist_count' => $user->wishlist_count,
        ];

        // Статистика по месяцам
        $monthlyStats = $user->orders()
                            ->where('status', 'completed')
                            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count, SUM(total_amount) as total')
                            ->groupBy('year', 'month')
                            ->orderBy('year', 'desc')
                            ->orderBy('month', 'desc')
                            ->limit(12)
                            ->get();

        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'loyalty_level' => $this->getLoyaltyLevel($stats['total_spent']),
                'discount' => $this->getDiscount($stats['total_spent']),
                'is_regular' => $stats['total_orders'] >= 3,
                'is_active' => true,
            ],
            'stats' => $stats,
            'monthly_stats' => $monthlyStats,
        ]);
    }

    /**
     * Calculate loyalty level based on total spent
     */
    private function getLoyaltyLevel($totalSpent)
    {
        if ($totalSpent >= 50000) return 'Платиновый';
        if ($totalSpent >= 20000) return 'Золотой';
        if ($totalSpent >= 5000) return 'Серебряный';
        return 'Базовый';
    }

    /**
     * Calculate discount based on loyalty level
     */
    private function getDiscount($totalSpent)
    {
        if ($totalSpent >= 50000) return 15;
        if ($totalSpent >= 20000) return 10;
        if ($totalSpent >= 5000) return 5;
        return 0;
    }

    /**
     * Delete user account
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'password' => 'required|current_password',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Неверный пароль');
        }

        try {
            // Выходим из системы
            Auth::logout();

            // Удаляем пользователя
            $user->delete();

            // Очищаем сессию
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')
                    ->with('success', 'Ваш аккаунт успешно удален');

        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при удалении аккаунта: ' . $e->getMessage());
        }
    }
}
