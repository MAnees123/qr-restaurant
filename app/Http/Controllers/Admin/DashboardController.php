<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Table;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $user->restaurant_id;

        $stats = [
            'total_orders' => Order::where('restaurant_id', $restaurantId)->count(),
            'pending_orders' => Order::where('restaurant_id', $restaurantId)->where('status', 'pending')->count(),
            'total_revenue' => \App\Models\Payment::where('restaurant_id', $restaurantId)->where('status', 'paid')->sum('amount'),
            'pending_payments' => Order::where('restaurant_id', $restaurantId)->where('payment_status', 'pending')->sum('total_amount'),
            'active_tables' => Table::where('restaurant_id', $restaurantId)->where('status', 'occupied')->count(),
            'pending_reservations' => \App\Models\Reservation::where('restaurant_id', $restaurantId)->where('status', 'pending')->count(),
        ];
        // Payment breakdown (needed for stat cards)
        $stats['cash_payments'] = \App\Models\Payment::where('restaurant_id', $restaurantId)->where('status', 'paid')->where('method', 'cash')->sum('amount');
        $stats['online_payments'] = \App\Models\Payment::where('restaurant_id', $restaurantId)->where('status', 'paid')->where('method', '!=', 'cash')->sum('amount');
        // Top 10 products sold (for Pie Chart)
        $topProducts = \Illuminate\Support\Facades\DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->where('orders.restaurant_id', $restaurantId)
            ->whereNotIn('orders.status', ['cancelled'])
            ->whereNotNull('order_items.menu_item_id')
            ->select('menu_items.name', \Illuminate\Support\Facades\DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('menu_items.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();
        
        $topProductNames = $topProducts->pluck('name')->toArray();
        $topProductQtys = $topProducts->pluck('total_qty')->toArray();

        $recentOrders = Order::where('restaurant_id', $restaurantId)
            ->with('table')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Chart Data (Default 7 days)
        $range = $request->get('range', 'daily'); // hourly, daily, weekly, monthly, yearly
        $chartData = $this->getChartData($restaurantId, $range);

        $upcomingReservations = \App\Models\Reservation::where('restaurant_id', $restaurantId)
            ->where('status', 'confirmed')
            ->whereDate('reservation_date', '>=', now()->toDateString())
            ->orderBy('reservation_date', 'asc')
            ->orderBy('reservation_time', 'asc')
            ->take(5)
            ->get();

        $theme = auth()->user()->theme ?? 'default';

        if (view()->exists("themes.{$theme}.dashboard")) {
            return view("themes.{$theme}.dashboard", compact('stats', 'recentOrders', 'chartData', 'upcomingReservations', 'range', 'topProductNames', 'topProductQtys'));
        }

        return view('themes.default.dashboard', compact('stats', 'recentOrders', 'chartData', 'upcomingReservations', 'range', 'topProductNames', 'topProductQtys'));
    }

    private function getChartData($restaurantId, $range)
    {
        $labels = [];
        $data = [];

        switch ($range) {
            case 'hourly':
                for ($i = 23; $i >= 0; $i--) {
                    $time = now()->subHours($i);
                    $labels[] = $time->format('H:00');
                    $data[] = \App\Models\Payment::where('restaurant_id', $restaurantId)
                        ->where('status', 'paid')
                        ->whereBetween('created_at', [$time->startOfHour()->toDateTimeString(), $time->endOfHour()->toDateTimeString()])
                        ->sum('amount');
                }
                break;
            case 'monthly':
                for ($i = 11; $i >= 0; $i--) {
                    $month = now()->subMonths($i);
                    $labels[] = $month->format('M Y');
                    $data[] = \App\Models\Payment::where('restaurant_id', $restaurantId)
                        ->where('status', 'paid')
                        ->whereMonth('created_at', $month->month)
                        ->whereYear('created_at', $month->year)
                        ->sum('amount');
                }
                break;
            case 'yearly':
                for ($i = 4; $i >= 0; $i--) {
                    $year = now()->subYears($i);
                    $labels[] = $year->format('Y');
                    $data[] = \App\Models\Payment::where('restaurant_id', $restaurantId)
                        ->where('status', 'paid')
                        ->whereYear('created_at', $year->year)
                        ->sum('amount');
                }
                break;
            case 'weekly':
                for ($i = 7; $i >= 0; $i--) {
                    $week = now()->subWeeks($i);
                    $labels[] = 'Week ' . $week->weekOfYear;
                    $data[] = \App\Models\Payment::where('restaurant_id', $restaurantId)
                        ->where('status', 'paid')
                        ->whereBetween('created_at', [$week->startOfWeek()->toDateTimeString(), $week->endOfWeek()->toDateTimeString()])
                        ->sum('amount');
                }
                break;
            default: // daily
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $labels[] = $date->format('M d');
                    $data[] = \App\Models\Payment::where('restaurant_id', $restaurantId)
                        ->where('status', 'paid')
                        ->whereDate('created_at', $date->toDateString())
                        ->sum('amount');
                }
                break;
        }

        return ['labels' => $labels, 'data' => $data];
    }
    public function revenue()
    {
        $user = auth()->user();
        $restaurantId = $user->restaurant_id;

        // Fetch orders that contribute to revenue (usually 'served' or completed)
        $orders = Order::where('restaurant_id', $restaurantId)
            ->where('status', 'served')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.revenue.index', compact('orders'));
    }

    public function orderNotifications(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;
        $since = $request->get('since');

        $query = Order::where('restaurant_id', $restaurantId)
            ->with('table')
            ->orderBy('updated_at', 'desc');

        if ($since) {
            try {
                $query->where('updated_at', '>=', $since);
            } catch (\Exception $e) {
                // ignore invalid timestamp
            }
        } else {
            $query->where('updated_at', '>=', now()->subHours(24)->toDateTimeString());
        }

        $orders = $query->take(20)->get();

        return response()->json($orders);
    }
}
