<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Banner::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $banners = [
            [
                'id'               => 1,
                'restaurant_id'    => 1,
                'title'            => 'Weekend Mega Feast Deal',
                'subtitle'         => 'Angus Steak Burger + Crispy Fries + Refresher Drink',
                'badge_text'       => '40% OFF FLASH DEAL',
                'original_price'   => 28.50,
                'discounted_price' => 17.00,
                'prep_time'        => 20,
                'image_path'       => 'banners/banner_weekend_feast.jpg',
                'redirect_url'     => null,
                'sort_order'       => 0,
                'is_active'        => true,
            ],
            [
                'id'               => 2,
                'restaurant_id'    => 1,
                'title'            => 'Royal Biryani Platter Special',
                'subtitle'         => 'Slow-cooked fragrant Basmati with Raita, Mint Chutney & Salad',
                'badge_text'       => "CHEF'S SPECIAL",
                'original_price'   => 16.50,
                'discounted_price' => 12.50,
                'prep_time'        => 15,
                'image_path'       => 'banners/banner_biryani_special.jpg',
                'redirect_url'     => null,
                'sort_order'       => 1,
                'is_active'        => true,
            ],
            [
                'id'               => 3,
                'restaurant_id'    => 1,
                'title'            => 'Wood-Fired Pizza Fiesta',
                'subtitle'         => 'Buy Any Large Gourmet Pizza & Get A Medium Pizza Free!',
                'badge_text'       => 'BUY 1 GET 1 FREE',
                'original_price'   => 35.00,
                'discounted_price' => 22.00,
                'prep_time'        => 20,
                'image_path'       => 'banners/banner_pizza_fiesta.jpg',
                'redirect_url'     => null,
                'sort_order'       => 2,
                'is_active'        => true,
            ],
            [
                'id'               => 4,
                'restaurant_id'    => 1,
                'title'            => 'Sizzling Ribeye Steak Night',
                'subtitle'         => 'Grilled prime cut with garlic herb butter, mashed potatoes & veggies',
                'badge_text'       => 'LIMITED EDITION',
                'original_price'   => 38.00,
                'discounted_price' => 29.50,
                'prep_time'        => 25,
                'image_path'       => 'banners/banner_ribeye_steak.jpg',
                'redirect_url'     => null,
                'sort_order'       => 3,
                'is_active'        => true,
            ],
            [
                'id'               => 5,
                'restaurant_id'    => 1,
                'title'            => 'Molten Lava & Espresso Combo',
                'subtitle'         => 'Warm Belgian chocolate cake with double roasted Italian espresso',
                'badge_text'       => 'SWEET DELIGHT',
                'original_price'   => 14.00,
                'discounted_price' => 9.99,
                'prep_time'        => 10,
                'image_path'       => 'banners/banner_lava_dessert.jpg',
                'redirect_url'     => null,
                'sort_order'       => 4,
                'is_active'        => true,
            ],
        ];

        foreach ($banners as $b) {
            Banner::create($b);
        }
    }
}
