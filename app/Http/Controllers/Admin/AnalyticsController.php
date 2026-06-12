<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id ?? null;
        if (!$restaurantId) {
            return redirect()->route('admin.dashboard')->with('error', 'Restaurant not found.');
        }

        $theme = auth()->user()->theme ?? 'default';

        // ── TODAY'S TOP ITEMS (live from order_items) ──
        $todayTop = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->where('orders.restaurant_id', $restaurantId)
            ->whereNotIn('orders.status', ['cancelled'])
            ->whereDate('orders.created_at', Carbon::today())
            ->whereNotNull('order_items.menu_item_id')
            ->select(
                'menu_items.id',
                'menu_items.name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // ── THIS WEEK'S TOP ITEMS ──
        $weekTop = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->where('orders.restaurant_id', $restaurantId)
            ->whereNotIn('orders.status', ['cancelled'])
            ->whereBetween('orders.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->whereNotNull('order_items.menu_item_id')
            ->select(
                'menu_items.id',
                'menu_items.name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // ── THIS MONTH'S TOP ITEMS with badge logic ──
        $monthStart = Carbon::now()->startOfMonth();
        $prevMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $prevMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $currentMonth = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->where('orders.restaurant_id', $restaurantId)
            ->whereNotIn('orders.status', ['cancelled'])
            ->where('orders.created_at', '>=', $monthStart)
            ->whereNotNull('order_items.menu_item_id')
            ->select(
                'menu_items.id',
                'menu_items.name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $prevMonth = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.restaurant_id', $restaurantId)
            ->whereNotIn('orders.status', ['cancelled'])
            ->whereBetween('orders.created_at', [$prevMonthStart, $prevMonthEnd])
            ->whereNotNull('order_items.menu_item_id')
            ->select('order_items.menu_item_id', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('order_items.menu_item_id')
            ->get()->keyBy('menu_item_id');

        $totalThisMonth = $currentMonth->sum('total_qty');

        $hotItems = $currentMonth->map(function ($item) use ($prevMonth, $totalThisMonth) {
            $prevQty = $prevMonth->get($item->id)?->total_qty ?? 0;
            $currQty = $item->total_qty;

            $growth = 0;
            if ($prevQty > 0) {
                $growth = (($currQty - $prevQty) / $prevQty) * 100;
            } elseif ($currQty > 0) {
                $growth = 100;
            }

            $badge = null;
            if ($totalThisMonth > 0 && ($currQty / $totalThisMonth) > 0.15) {
                $badge = '🏆 Best Seller';
            } elseif ($growth >= 50) {
                $badge = '📈 Trending';
            } elseif ($currQty >= 10) {
                $badge = '⚡ Fast Selling';
            } elseif ($currQty >= 3) {
                $badge = '🔥 Hot Item';
            }

            return (object)[
                'name' => $item->name,
                'quantity_sold' => $currQty,
                'total_revenue' => $item->total_revenue,
                'growth_percentage' => $growth,
                'badge_type' => $badge,
            ];
        });

        return view('admin.analytics.index', compact('hotItems', 'todayTop', 'weekTop', 'theme'));
    }

    public function salesApi(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id ?? null;
        if (!$restaurantId) return response()->json(['labels' => [], 'qty' => [], 'revenue' => []]);

        $filter = $request->query('filter', '7days');

        if ($filter === 'today') {
            $rows = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.restaurant_id', $restaurantId)
                ->whereNotIn('orders.status', ['cancelled'])
                ->whereDate('orders.created_at', Carbon::today())
                ->select(
                    DB::raw('DATE_FORMAT(orders.created_at, "%H:00") as label'),
                    DB::raw('SUM(order_items.quantity) as total_qty'),
                    DB::raw('SUM(order_items.subtotal) as total_rev')
                )
                ->groupBy('label')->orderBy('label')->get();
        } elseif ($filter === '30days') {
            $rows = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.restaurant_id', $restaurantId)
                ->whereNotIn('orders.status', ['cancelled'])
                ->where('orders.created_at', '>=', Carbon::now()->subDays(30))
                ->select(
                    DB::raw('DATE(orders.created_at) as label'),
                    DB::raw('SUM(order_items.quantity) as total_qty'),
                    DB::raw('SUM(order_items.subtotal) as total_rev')
                )
                ->groupBy('label')->orderBy('label')->get();
        } else {
            // 7 days default
            $rows = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.restaurant_id', $restaurantId)
                ->whereNotIn('orders.status', ['cancelled'])
                ->where('orders.created_at', '>=', Carbon::now()->subDays(7))
                ->select(
                    DB::raw('DATE(orders.created_at) as label'),
                    DB::raw('SUM(order_items.quantity) as total_qty'),
                    DB::raw('SUM(order_items.subtotal) as total_rev')
                )
                ->groupBy('label')->orderBy('label')->get();
        }

        return response()->json([
            'labels'  => $rows->pluck('label'),
            'qty'     => $rows->pluck('total_qty'),
            'revenue' => $rows->pluck('total_rev'),
        ]);
    }

    public function topItemsApi(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id ?? null;
        if (!$restaurantId) return response()->json(['names' => [], 'qty' => []]);

        $filter = $request->query('filter', '7days');

        if ($filter === 'today') {
            $start = Carbon::today();
            $dateCondition = ['orders.created_at', '>=', $start];
        } elseif ($filter === '30days') {
            $start = Carbon::now()->subDays(30);
            $dateCondition = ['orders.created_at', '>=', $start];
        } else {
            $start = Carbon::now()->subDays(7);
            $dateCondition = ['orders.created_at', '>=', $start];
        }

        $rows = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->where('orders.restaurant_id', $restaurantId)
            ->whereNotIn('orders.status', ['cancelled'])
            ->where(...$dateCondition)
            ->whereNotNull('order_items.menu_item_id')
            ->select(
                'menu_items.name',
                DB::raw('SUM(order_items.quantity) as total_qty')
            )
            ->groupBy('menu_items.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return response()->json([
            'names' => $rows->pluck('name'),
            'qty'   => $rows->pluck('total_qty'),
        ]);
    }
}
