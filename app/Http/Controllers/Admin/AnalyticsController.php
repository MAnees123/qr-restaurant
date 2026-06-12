<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HotItemCache;
use App\Models\ProductAnalytic;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = auth()->user()->restaurant->id ?? null;
        if (!$restaurantId) {
            return redirect()->route('admin.dashboard')->with('error', 'Restaurant not found.');
        }

        $hotItems = HotItemCache::with('menuItem')
            ->where('restaurant_id', $restaurantId)
            ->where('period_type', 'this_month')
            ->orderByDesc('rank_score')
            ->take(8)
            ->get();
            
        $todayTop = HotItemCache::with('menuItem')
            ->where('restaurant_id', $restaurantId)
            ->where('period_type', 'today')
            ->orderByDesc('rank_score')
            ->take(5)
            ->get();
            
        $weekTop = HotItemCache::with('menuItem')
            ->where('restaurant_id', $restaurantId)
            ->where('period_type', 'this_week')
            ->orderByDesc('rank_score')
            ->take(5)
            ->get();

        $theme = auth()->user()->theme ?? 'default';

        return view('admin.analytics.index', compact('hotItems', 'todayTop', 'weekTop', 'theme'));
    }

    public function salesApi(Request $request)
    {
        $restaurantId = auth()->user()->restaurant->id ?? null;
        if (!$restaurantId) return response()->json(['labels' => [], 'qty' => [], 'revenue' => []]);

        $filter = $request->query('filter', '7days');
        
        $start = Carbon::now();
        if ($filter == 'today') {
            $start->startOfDay();
            $data = ProductAnalytic::where('restaurant_id', $restaurantId)
                ->where('period_type', 'hour')
                ->where('period_date', '>=', $start)
                ->select(DB::raw('DATE_FORMAT(period_date, "%H:00") as date'), DB::raw('SUM(quantity_sold) as total_qty'), DB::raw('SUM(total_revenue) as total_rev'))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } elseif ($filter == '30days') {
            $start->subDays(30);
            $data = ProductAnalytic::where('restaurant_id', $restaurantId)
                ->where('period_type', 'day')
                ->where('period_date', '>=', $start)
                ->select(DB::raw('DATE(period_date) as date'), DB::raw('SUM(quantity_sold) as total_qty'), DB::raw('SUM(total_revenue) as total_rev'))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } else {
            // 7 days
            $start->subDays(7);
            $data = ProductAnalytic::where('restaurant_id', $restaurantId)
                ->where('period_type', 'day')
                ->where('period_date', '>=', $start)
                ->select(DB::raw('DATE(period_date) as date'), DB::raw('SUM(quantity_sold) as total_qty'), DB::raw('SUM(total_revenue) as total_rev'))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }

        return response()->json([
            'labels' => $data->pluck('date'),
            'qty' => $data->pluck('total_qty'),
            'revenue' => $data->pluck('total_rev'),
        ]);
    }
}
