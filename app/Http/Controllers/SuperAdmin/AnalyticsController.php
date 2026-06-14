<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Restaurant;

class AnalyticsController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // Revenue by month (last 6 months)
        $revenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $label = $month->format('M Y');
            $total = 0;
            if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'total_amount')) {
                $total = Order::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('total_amount');
            }
            $revenueChart[] = ['label' => $label, 'value' => round($total, 2)];
        }

        // Orders by status
        $ordersByStatus = [];
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'status')) {
            $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
        }

        // Restaurant growth (last 6 months)
        $growthChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $count = Restaurant::whereYear('created_at', '<=', $month->year)
                ->where(function ($q) use ($month) {
                    $q->whereYear('created_at', '<', $month->year)
                      ->orWhere(function ($q2) use ($month) {
                          $q2->whereYear('created_at', $month->year)
                             ->whereMonth('created_at', '<=', $month->month);
                      });
                })
                ->count();
            $growthChart[] = ['label' => $month->format('M Y'), 'value' => $count];
        }

        // Top 5 restaurants by order count
        $topRestaurants = [];
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'restaurant_id')) {
            $topRestaurants = Restaurant::withCount('orders')
                ->orderByDesc('orders_count')
                ->limit(5)
                ->get();
        }

        return view('superadmin.analytics.index', compact(
            'revenueChart', 'ordersByStatus', 'growthChart', 'topRestaurants'
        ));
    }
}
