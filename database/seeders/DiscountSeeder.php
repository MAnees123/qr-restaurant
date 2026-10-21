<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Discount;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Discount::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $discounts = [
            [
                'restaurant_id' => 1,
                'code'          => 'WELCOME10',
                'type'          => 'percentage',
                'value'         => 10.00,
                'is_active'     => true,
                'starts_at'     => now()->subDays(30),
                'expires_at'    => now()->addMonths(6),
                'usage_limit'   => 500,
                'used_count'    => 42,
            ],
            [
                'restaurant_id' => 1,
                'code'          => 'FLAT5',
                'type'          => 'fixed',
                'value'         => 5.00,
                'is_active'     => true,
                'starts_at'     => now()->subDays(15),
                'expires_at'    => now()->addMonths(3),
                'usage_limit'   => 200,
                'used_count'    => 18,
            ],
            [
                'restaurant_id' => 1,
                'code'          => 'WEEKEND20',
                'type'          => 'percentage',
                'value'         => 20.00,
                'is_active'     => true,
                'starts_at'     => now()->subDays(7),
                'expires_at'    => now()->addMonths(2),
                'usage_limit'   => 150,
                'used_count'    => 35,
            ],
            [
                'restaurant_id' => 1,
                'code'          => 'SUPERDEAL',
                'type'          => 'percentage',
                'value'         => 25.00,
                'is_active'     => true,
                'starts_at'     => now()->subDays(5),
                'expires_at'    => now()->addMonth(),
                'usage_limit'   => 100,
                'used_count'    => 12,
            ],
            [
                'restaurant_id' => 1,
                'code'          => 'VIPGUEST',
                'type'          => 'fixed',
                'value'         => 10.00,
                'is_active'     => true,
                'starts_at'     => now()->subDays(10),
                'expires_at'    => now()->addMonths(5),
                'usage_limit'   => 50,
                'used_count'    => 8,
            ],
        ];

        foreach ($discounts as $d) {
            Discount::create($d);
        }
    }
}
