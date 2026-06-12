<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductAnalytic;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsAggregationService
{
    public function aggregateForPeriod($restaurantId, $periodType, Carbon $date)
    {
        $start = null;
        $end = null;
        
        switch ($periodType) {
            case 'hour':
                $start = $date->copy()->startOfHour();
                $end = $date->copy()->endOfHour();
                break;
            case 'day':
                $start = $date->copy()->startOfDay();
                $end = $date->copy()->endOfDay();
                break;
            case 'week':
                $start = $date->copy()->startOfWeek();
                $end = $date->copy()->endOfWeek();
                break;
            case 'month':
                $start = $date->copy()->startOfMonth();
                $end = $date->copy()->endOfMonth();
                break;
            case 'year':
                $start = $date->copy()->startOfYear();
                $end = $date->copy()->endOfYear();
                break;
        }

        $analytics = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.restaurant_id', $restaurantId)
            // exclude pending/cancelled
            ->whereNotIn('orders.status', ['pending', 'cancelled'])
            ->whereBetween('orders.created_at', [$start, $end])
            ->whereNotNull('order_items.menu_item_id')
            ->select(
                'order_items.menu_item_id',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('order_items.menu_item_id')
            ->get();

        foreach ($analytics as $item) {
            ProductAnalytic::updateOrCreate(
                [
                    'restaurant_id' => $restaurantId,
                    'menu_item_id' => $item->menu_item_id,
                    'period_type' => $periodType,
                    'period_date' => $start,
                ],
                [
                    'quantity_sold' => $item->total_quantity,
                    'total_revenue' => $item->total_revenue,
                ]
            );
        }
    }
}
