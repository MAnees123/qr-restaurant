<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ProductAnalytic;
use App\Models\HotItemCache;
use App\Models\MenuItem;

class AnalyticsSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        HotItemCache::truncate();
        ProductAnalytic::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $menuItems = MenuItem::where('restaurant_id', 1)->get();
        if ($menuItems->isEmpty()) return;

        // Populate daily product analytics for the past 14 days
        for ($daysAgo = 13; $daysAgo >= 0; $daysAgo--) {
            $date = now()->subDays($daysAgo)->toDateString();

            foreach ($menuItems as $item) {
                $qty = rand(2, 18);
                $revenue = round($item->price * $qty, 2);
                $discount = round($revenue * 0.05, 2);
                $cost = round($item->price * 0.40 * $qty, 2);
                $profit = round($revenue - $cost - $discount, 2);

                ProductAnalytic::create([
                    'restaurant_id'  => 1,
                    'menu_item_id'   => $item->id,
                    'quantity_sold'  => $qty,
                    'total_revenue'  => $revenue,
                    'total_discount' => $discount,
                    'total_profit'   => $profit,
                    'period_type'    => 'day',
                    'period_date'    => $date . ' 00:00:00',
                ]);
            }
        }

        // Hot Item Caches (Top 6 Bestsellers & Trending)
        $topItems = [
            ['id' => 7,  'badge' => 'bestseller', 'score' => 98.5, 'qty' => 142, 'growth' => 24.5], // Angus Burger
            ['id' => 17, 'badge' => 'bestseller', 'score' => 96.0, 'qty' => 135, 'growth' => 18.2], // Biryani
            ['id' => 12, 'badge' => 'trending',   'score' => 92.4, 'qty' => 110, 'growth' => 31.0], // Pepperoni Pizza
            ['id' => 14, 'badge' => 'trending',   'score' => 89.1, 'qty' => 78,  'growth' => 45.6], // Ribeye Steak
            ['id' => 21, 'badge' => 'popular',    'score' => 85.3, 'qty' => 95,  'growth' => 12.8], // Lava Cake
            ['id' => 3,  'badge' => 'popular',    'score' => 81.7, 'qty' => 82,  'growth' => 15.4], // Dynamite Shrimps
        ];

        foreach ($topItems as $ti) {
            $item = $menuItems->firstWhere('id', $ti['id']);
            if (!$item) continue;

            $rev = round($item->price * $ti['qty'], 2);

            HotItemCache::create([
                'restaurant_id'     => 1,
                'menu_item_id'      => $item->id,
                'rank_score'        => $ti['score'],
                'badge_type'        => $ti['badge'],
                'period_type'       => 'month',
                'quantity_sold'     => $ti['qty'],
                'total_revenue'     => $rev,
                'growth_percentage' => $ti['growth'],
            ]);
        }
    }
}
