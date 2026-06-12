<?php

namespace App\Services;

use App\Models\ProductAnalytic;
use App\Models\HotItemCache;
use Carbon\Carbon;

class HotItemDetectionService
{
    public function calculateHotItems($restaurantId)
    {
        // Calculate for today
        $this->detectForPeriod($restaurantId, 'day', Carbon::today(), 'today');
        
        // Calculate for this week
        $this->detectForPeriod($restaurantId, 'week', Carbon::now()->startOfWeek(), 'this_week');
        
        // Calculate for this month
        $this->detectForPeriod($restaurantId, 'month', Carbon::now()->startOfMonth(), 'this_month');
    }

    private function detectForPeriod($restaurantId, $analyticType, Carbon $currentPeriodStart, $cachePeriodType)
    {
        $current = ProductAnalytic::where('restaurant_id', $restaurantId)
            ->where('period_type', $analyticType)
            ->where('period_date', $currentPeriodStart)
            ->get()->keyBy('menu_item_id');

        $previousPeriodStart = $currentPeriodStart->copy();
        if ($analyticType === 'day') $previousPeriodStart->subDay();
        elseif ($analyticType === 'week') $previousPeriodStart->subWeek();
        elseif ($analyticType === 'month') $previousPeriodStart->subMonth();

        $previous = ProductAnalytic::where('restaurant_id', $restaurantId)
            ->where('period_type', $analyticType)
            ->where('period_date', $previousPeriodStart)
            ->get()->keyBy('menu_item_id');

        if ($current->isEmpty()) {
            return;
        }

        // Clear old
        HotItemCache::where('restaurant_id', $restaurantId)
            ->where('period_type', $cachePeriodType)
            ->delete();

        $itemsToCache = [];
        $totalItemsSold = $current->sum('quantity_sold');

        foreach ($current as $menuItemId => $stat) {
            $prevStat = $previous->get($menuItemId);
            $prevQty = $prevStat ? $prevStat->quantity_sold : 0;
            $currQty = $stat->quantity_sold;

            $growth = 0;
            if ($prevQty > 0) {
                $growth = (($currQty - $prevQty) / $prevQty) * 100;
            } elseif ($currQty > 0 && $prevQty == 0) {
                $growth = 100; 
            }

            $badge = null;
            if ($totalItemsSold > 0 && ($currQty / $totalItemsSold) > 0.15) {
                $badge = '🏆 Best Seller';
            } elseif ($growth >= 50 && $currQty >= 5) {
                $badge = '📈 Trending';
            } elseif ($analyticType === 'day' && $currQty > 10) {
                $badge = '⚡ Fast Selling';
            } elseif ($currQty >= 3) {
                $badge = '🔥 Hot Item';
            }

            // Rank Score Formula
            $rankScore = ($currQty * 0.7) + (min($growth, 100) * 0.3);

            $itemsToCache[] = [
                'restaurant_id' => $restaurantId,
                'menu_item_id' => $menuItemId,
                'rank_score' => $rankScore,
                'badge_type' => $badge,
                'period_type' => $cachePeriodType,
                'quantity_sold' => $currQty,
                'total_revenue' => $stat->total_revenue,
                'growth_percentage' => $growth,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Sort descending
        usort($itemsToCache, function($a, $b) {
            return $b['rank_score'] <=> $a['rank_score'];
        });

        // Insert top 15
        $topItems = array_slice($itemsToCache, 0, 15);
        HotItemCache::insert($topItems);
    }
}
