<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /*
     * IMAGE MIGRATION STRATEGY:
     * Image paths (like logos, banners, menu item images) are stored as strings relative
     * to the storage/public disk. This migration preserves the exact string paths in the database.
     * To ensure images display correctly on a fresh installation, you must physically copy the
     * contents of the original `storage/app/public` directory to the new server and run
     * `php artisan storage:link`.
     */

    public function up(): void
    {

        // Seed data for restaurants
        DB::table('restaurants')->updateOrInsert(
            ['id' => 1],
            [
  'id' => 1,
  'name' => 'Cafe Munch',
  'owner_name' => NULL,
  'domain' => NULL,
  'cuisine_type' => 'Pakistani',
  'theme' => 'default',
  'logo' => 'restaurant_logos/IpD2RlJ2tcYUgzKQfyWKvZAGObBvPLhpiimT1mLd.png',
  'address' => 'Bypass Road, Amedpur East.',
  'country' => NULL,
  'city' => NULL,
  'timezone' => 'UTC',
  'currency' => 'USD',
  'phone' => '03087728724',
  'is_active' => 1,
  'is_suspended' => 0,
  'subscription_plan' => 'free',
  'granted_features' => '["pos", "table_ordering", "kitchen_screen", "online_orders", "table_reservation", "inventory", "multi_branch", "role_permission", "customer_mgmt", "payroll", "coupons", "banners", "billing", "theme_custom", "dark_mode", "qr_logo", "printer"]',
  'subscription_ends_at' => '2026-06-30 00:00:00',
  'billing_cycle' => 'monthly',
  'payment_status' => 'paid',
  'trial_ends_at' => NULL,
  'max_branches' => 1,
  'max_users' => 5,
  'max_tables' => 20,
  'max_storage_mb' => 100,
  'created_at' => '2026-04-23 12:10:44',
  'updated_at' => '2026-06-14 08:47:47',
  'plan_id' => 2,
]
        );

        // Seed data for users
        DB::table('users')->updateOrInsert(
            ['id' => 1],
            [
  'id' => 1,
  'restaurant_id' => 1,
  'name' => 'Admin User',
  'email' => 'admin@restaurant.com',
  'email_verified_at' => NULL,
  'password' => '$2y$12$nnHz6gKI2DQ/N3T/zuiIBu32EzXWW1DGgRPHPBYpgljTtGrdesSqW',
  'role' => 'admin',
  'is_super_admin' => 0,
  'avatar' => NULL,
  'is_suspended' => 0,
  'theme' => 'dark',
  'remember_token' => '48RuSYldFucQ7VJWrC6coL3tQvRW3Ze1ew7MntHOyLijHRPNOXuFRAcbPzi4',
  'created_at' => '2026-04-23 12:10:43',
  'updated_at' => '2026-06-18 12:13:14',
]
        );
        DB::table('users')->updateOrInsert(
            ['id' => 2],
            [
  'id' => 2,
  'restaurant_id' => 1,
  'name' => 'Kitchen Staff',
  'email' => 'kitchen@restaurant.com',
  'email_verified_at' => NULL,
  'password' => '$2y$12$5RGFTPc8KTVZU9/9DkWsye7L75RxBq/5j/dKfvcNBPoMWFhqoEjRS',
  'role' => 'kitchen',
  'is_super_admin' => 0,
  'avatar' => NULL,
  'is_suspended' => 0,
  'theme' => 'default',
  'remember_token' => NULL,
  'created_at' => '2026-04-23 12:10:44',
  'updated_at' => '2026-06-14 06:46:32',
]
        );
        DB::table('users')->updateOrInsert(
            ['id' => 3],
            [
  'id' => 3,
  'restaurant_id' => 1,
  'name' => 'Admin User',
  'email' => 'admin@gmail.com',
  'email_verified_at' => NULL,
  'password' => '1234',
  'role' => 'admin',
  'is_super_admin' => 0,
  'avatar' => NULL,
  'is_suspended' => 0,
  'theme' => 'default',
  'remember_token' => NULL,
  'created_at' => NULL,
  'updated_at' => NULL,
]
        );

        // Seed data for menu_categories
        DB::table('menu_categories')->updateOrInsert(
            ['id' => 1],
            [
  'id' => 1,
  'restaurant_id' => 1,
  'name' => 'Starters & Chaat',
  'description' => NULL,
  'sort_order' => 0,
  'is_active' => 1,
  'created_at' => '2026-04-23 12:10:44',
  'updated_at' => '2026-04-23 12:10:44',
]
        );
        DB::table('menu_categories')->updateOrInsert(
            ['id' => 2],
            [
  'id' => 2,
  'restaurant_id' => 1,
  'name' => 'Main Course',
  'description' => NULL,
  'sort_order' => 1,
  'is_active' => 1,
  'created_at' => '2026-04-23 12:10:44',
  'updated_at' => '2026-04-23 12:10:44',
]
        );
        DB::table('menu_categories')->updateOrInsert(
            ['id' => 3],
            [
  'id' => 3,
  'restaurant_id' => 1,
  'name' => 'Desserts',
  'description' => NULL,
  'sort_order' => 2,
  'is_active' => 1,
  'created_at' => '2026-04-23 12:10:44',
  'updated_at' => '2026-04-23 12:10:44',
]
        );
        DB::table('menu_categories')->updateOrInsert(
            ['id' => 4],
            [
  'id' => 4,
  'restaurant_id' => 1,
  'name' => 'Drinks',
  'description' => NULL,
  'sort_order' => 3,
  'is_active' => 1,
  'created_at' => '2026-04-23 12:10:45',
  'updated_at' => '2026-04-23 12:10:45',
]
        );

        // Seed data for menu_items
        DB::table('menu_items')->updateOrInsert(
            ['id' => 1],
            [
  'id' => 1,
  'menu_category_id' => 1,
  'restaurant_id' => 1,
  'name' => 'Dahi Bhallay',
  'description' => 'Soft lentil dumplings topped with creamy yogurt and tamarind chutney.',
  'price' => '150.00',
  'original_price' => '180.00',
  'preparation_time' => 15,
  'image' => 'menu_items/dahi_bhallay.png',
  'is_available' => 1,
  'sort_order' => 0,
  'created_at' => '2026-04-23 12:10:45',
  'updated_at' => '2026-06-07 17:30:08',
]
        );
        DB::table('menu_items')->updateOrInsert(
            ['id' => 2],
            [
  'id' => 2,
  'menu_category_id' => 1,
  'restaurant_id' => 1,
  'name' => 'Gol Gappay',
  'description' => 'Crispy hollow puris filled with chickpeas, served with spicy mint water.',
  'price' => '200.00',
  'original_price' => NULL,
  'preparation_time' => 15,
  'image' => 'menu_items/gol_gappay.png',
  'is_available' => 1,
  'sort_order' => 1,
  'created_at' => '2026-04-23 12:10:45',
  'updated_at' => '2026-04-24 11:13:12',
]
        );
        DB::table('menu_items')->updateOrInsert(
            ['id' => 3],
            [
  'id' => 3,
  'menu_category_id' => 2,
  'restaurant_id' => 1,
  'name' => 'Chicken Biryani',
  'description' => 'Classic spicy chicken biryani made with fragrant basmati rice.',
  'price' => '450.00',
  'original_price' => NULL,
  'preparation_time' => 25,
  'image' => 'menu_items/chicken_biryani.png',
  'is_available' => 1,
  'sort_order' => 2,
  'created_at' => '2026-04-23 12:10:45',
  'updated_at' => '2026-06-06 05:00:53',
]
        );
        DB::table('menu_items')->updateOrInsert(
            ['id' => 4],
            [
  'id' => 4,
  'menu_category_id' => 2,
  'restaurant_id' => 1,
  'name' => 'Beef Karahi',
  'description' => 'Traditional beef karahi cooked with tomatoes and green chilies.',
  'price' => '1200.00',
  'original_price' => NULL,
  'preparation_time' => 15,
  'image' => 'menu_items/ZFGSwgmTqHlPnAdKfIYpzd4QikQoVsR9pWzuNANE.png',
  'is_available' => 1,
  'sort_order' => 3,
  'created_at' => '2026-04-23 12:10:45',
  'updated_at' => '2026-04-23 14:09:27',
]
        );
        DB::table('menu_items')->updateOrInsert(
            ['id' => 5],
            [
  'id' => 5,
  'menu_category_id' => 3,
  'restaurant_id' => 1,
  'name' => 'Kheer',
  'description' => 'Rich and creamy traditional rice pudding topped with nuts.',
  'price' => '250.00',
  'original_price' => NULL,
  'preparation_time' => 15,
  'image' => 'menu_items/pAmMFndt5nSYlrgj3lcwUQpBXXf8BXOdWPwymmgH.png',
  'is_available' => 1,
  'sort_order' => 4,
  'created_at' => '2026-04-23 12:10:45',
  'updated_at' => '2026-04-23 14:09:46',
]
        );
        DB::table('menu_items')->updateOrInsert(
            ['id' => 6],
            [
  'id' => 6,
  'menu_category_id' => 3,
  'restaurant_id' => 1,
  'name' => 'Gulab Jamun',
  'description' => 'Soft and sweet deep-fried milk dough balls in sugar syrup.',
  'price' => '150.00',
  'original_price' => NULL,
  'preparation_time' => 15,
  'image' => 'menu_items/nPoc9w58IUfUvLm9hmorAxvx60mhAFPW7b4q5Q7W.png',
  'is_available' => 1,
  'sort_order' => 5,
  'created_at' => '2026-04-23 12:10:45',
  'updated_at' => '2026-04-23 14:10:03',
]
        );
        DB::table('menu_items')->updateOrInsert(
            ['id' => 7],
            [
  'id' => 7,
  'menu_category_id' => 4,
  'restaurant_id' => 1,
  'name' => 'Fresh Mango Juice',
  'description' => 'Refreshing seasonal mango juice.',
  'price' => '300.00',
  'original_price' => NULL,
  'preparation_time' => 15,
  'image' => 'menu_items/mango_juice.png',
  'is_available' => 1,
  'sort_order' => 6,
  'created_at' => '2026-04-23 12:10:45',
  'updated_at' => '2026-04-23 12:10:45',
]
        );
        DB::table('menu_items')->updateOrInsert(
            ['id' => 8],
            [
  'id' => 8,
  'menu_category_id' => 4,
  'restaurant_id' => 1,
  'name' => 'Lassi',
  'description' => 'Sweet or salty yogurt-based traditional drink.',
  'price' => '200.00',
  'original_price' => NULL,
  'preparation_time' => 15,
  'image' => 'menu_items/0dj5H3pEfCWJqmbKnU5oRnvA3YzMH88ZKVdDFCdk.png',
  'is_available' => 1,
  'sort_order' => 7,
  'created_at' => '2026-04-23 12:10:45',
  'updated_at' => '2026-04-23 14:12:30',
]
        );
        DB::table('menu_items')->updateOrInsert(
            ['id' => 9],
            [
  'id' => 9,
  'menu_category_id' => 4,
  'restaurant_id' => 1,
  'name' => 'Hot Deal',
  'description' => NULL,
  'price' => '200.00',
  'original_price' => NULL,
  'preparation_time' => 15,
  'image' => 'menu_items/zlhEkHzfbl4421L3DbT9Uo2c0TlnxTAu2PTnH6bf.png',
  'is_available' => 1,
  'sort_order' => 3,
  'created_at' => '2026-04-23 14:09:05',
  'updated_at' => '2026-04-23 14:09:05',
]
        );

        // Seed data for portions
        DB::table('portions')->updateOrInsert(
            ['id' => 1],
            [
  'id' => 1,
  'restaurant_id' => 1,
  'branch_id' => 1,
  'name' => 'Male Portion',
  'is_active' => 1,
  'created_at' => '2026-06-13 06:04:12',
  'updated_at' => '2026-06-13 06:04:12',
]
        );

        // Seed data for banners
        DB::table('banners')->updateOrInsert(
            ['id' => 1],
            [
  'id' => 1,
  'restaurant_id' => 1,
  'title' => 'Pizza Big Deal',
  'subtitle' => '1 with 1 Free',
  'image_path' => 'banners/r9xES3ZiCkefBVd1s12viIEjK20Nkcp06irfeID4.png',
  'redirect_url' => NULL,
  'is_active' => 1,
  'sort_order' => 0,
  'created_at' => '2026-06-07 12:09:13',
  'updated_at' => '2026-06-07 17:36:42',
  'badge_text' => 'FEATURED',
  'original_price' => '1599.00',
  'discounted_price' => '1399.00',
  'prep_time' => NULL,
]
        );
        DB::table('banners')->updateOrInsert(
            ['id' => 2],
            [
  'id' => 2,
  'restaurant_id' => 1,
  'title' => 'Burger Hot Deal',
  'subtitle' => 'Zinger Hot Burger',
  'image_path' => 'banners/dxUuxLHZ65tc2GYAWs19pimc23jLhO1crBBTsaS7.png',
  'redirect_url' => NULL,
  'is_active' => 1,
  'sort_order' => 0,
  'created_at' => '2026-06-07 12:09:53',
  'updated_at' => '2026-06-07 17:36:24',
  'badge_text' => 'FEATURED',
  'original_price' => '799.00',
  'discounted_price' => '599.00',
  'prep_time' => NULL,
]
        );
        DB::table('banners')->updateOrInsert(
            ['id' => 3],
            [
  'id' => 3,
  'restaurant_id' => 1,
  'title' => 'Special Pizza',
  'subtitle' => 'Small with Cold Drink',
  'image_path' => 'banners/XaGmWGhQJIgFtXIVpZmtzEbjxnp5CNyxcQ8mK4RR.png',
  'redirect_url' => NULL,
  'is_active' => 1,
  'sort_order' => 0,
  'created_at' => '2026-06-07 12:10:29',
  'updated_at' => '2026-06-07 12:29:02',
  'badge_text' => 'FEATURED',
  'original_price' => '4000.00',
  'discounted_price' => '3000.00',
  'prep_time' => NULL,
]
        );

        // Seed data for tables
        DB::table('tables')->updateOrInsert(
            ['id' => 1],
            [
  'id' => 1,
  'restaurant_id' => 1,
  'table_number' => 'T-1',
  'capacity' => NULL,
  'is_active' => 1,
  'status' => 'occupied',
  'secure_token' => NULL,
  'auto_release_at' => NULL,
  'created_at' => '2026-04-23 12:10:44',
  'updated_at' => '2026-06-18 12:02:53',
  'branch_id' => NULL,
  'portion_id' => NULL,
]
        );
        DB::table('tables')->updateOrInsert(
            ['id' => 2],
            [
  'id' => 2,
  'restaurant_id' => 1,
  'table_number' => 'T-23',
  'capacity' => NULL,
  'is_active' => 1,
  'status' => 'occupied',
  'secure_token' => NULL,
  'auto_release_at' => NULL,
  'created_at' => '2026-04-23 12:10:44',
  'updated_at' => '2026-06-18 11:20:58',
  'branch_id' => 1,
  'portion_id' => 1,
]
        );
        DB::table('tables')->updateOrInsert(
            ['id' => 3],
            [
  'id' => 3,
  'restaurant_id' => 1,
  'table_number' => 'T-3',
  'capacity' => NULL,
  'is_active' => 1,
  'status' => 'free',
  'secure_token' => NULL,
  'auto_release_at' => '2026-06-14 07:29:12',
  'created_at' => '2026-04-23 12:10:44',
  'updated_at' => '2026-06-14 06:59:12',
  'branch_id' => NULL,
  'portion_id' => NULL,
]
        );
        DB::table('tables')->updateOrInsert(
            ['id' => 4],
            [
  'id' => 4,
  'restaurant_id' => 1,
  'table_number' => 'T-4',
  'capacity' => NULL,
  'is_active' => 1,
  'status' => 'free',
  'secure_token' => NULL,
  'auto_release_at' => NULL,
  'created_at' => '2026-04-23 12:10:44',
  'updated_at' => '2026-06-12 07:26:57',
  'branch_id' => NULL,
  'portion_id' => NULL,
]
        );
        DB::table('tables')->updateOrInsert(
            ['id' => 5],
            [
  'id' => 5,
  'restaurant_id' => 1,
  'table_number' => 'T-2',
  'capacity' => NULL,
  'is_active' => 1,
  'status' => 'free',
  'secure_token' => NULL,
  'auto_release_at' => NULL,
  'created_at' => '2026-06-13 04:55:30',
  'updated_at' => '2026-06-13 04:55:30',
  'branch_id' => NULL,
  'portion_id' => NULL,
]
        );
        DB::table('tables')->updateOrInsert(
            ['id' => 6],
            [
  'id' => 6,
  'restaurant_id' => 1,
  'table_number' => 'T-2',
  'capacity' => NULL,
  'is_active' => 1,
  'status' => 'occupied',
  'secure_token' => '4d91f94306a80e9513855ad51266021d',
  'auto_release_at' => NULL,
  'created_at' => '2026-06-13 06:04:31',
  'updated_at' => '2026-06-13 06:04:42',
  'branch_id' => 1,
  'portion_id' => 1,
]
        );

        // Seed data for qr_codes
        DB::table('qr_codes')->updateOrInsert(
            ['id' => 1],
            [
  'id' => 1,
  'table_id' => 1,
  'code' => '0e2dffd6-d351-41d7-b363-16d298be41ea',
  'qr_image_path' => NULL,
  'created_at' => '2026-04-23 12:10:44',
  'updated_at' => '2026-04-23 12:10:44',
]
        );
        DB::table('qr_codes')->updateOrInsert(
            ['id' => 2],
            [
  'id' => 2,
  'table_id' => 2,
  'code' => 'd969707c-ec67-483a-bb03-1b6a3a5645b8',
  'qr_image_path' => NULL,
  'created_at' => '2026-04-23 12:10:44',
  'updated_at' => '2026-04-23 12:10:44',
]
        );
        DB::table('qr_codes')->updateOrInsert(
            ['id' => 3],
            [
  'id' => 3,
  'table_id' => 3,
  'code' => '4c18981f-14b8-4c09-9e25-e73c5c7b3d91',
  'qr_image_path' => NULL,
  'created_at' => '2026-04-23 12:10:44',
  'updated_at' => '2026-04-23 12:10:44',
]
        );
        DB::table('qr_codes')->updateOrInsert(
            ['id' => 4],
            [
  'id' => 4,
  'table_id' => 4,
  'code' => '0abd9523-2300-4d19-8fe2-b0b73b8abe39',
  'qr_image_path' => NULL,
  'created_at' => '2026-04-23 12:10:44',
  'updated_at' => '2026-04-23 12:10:44',
]
        );
        DB::table('qr_codes')->updateOrInsert(
            ['id' => 5],
            [
  'id' => 5,
  'table_id' => 5,
  'code' => '93b67495-94d3-4513-8279-52b4e88fef17',
  'qr_image_path' => NULL,
  'created_at' => '2026-06-13 04:55:30',
  'updated_at' => '2026-06-13 04:55:30',
]
        );
        DB::table('qr_codes')->updateOrInsert(
            ['id' => 6],
            [
  'id' => 6,
  'table_id' => 6,
  'code' => 'ab56ba97-79ea-4fa5-ae74-88f4377bd29a',
  'qr_image_path' => NULL,
  'created_at' => '2026-06-13 06:04:31',
  'updated_at' => '2026-06-13 06:04:31',
]
        );

        // Seed data for reservations
        DB::table('reservations')->updateOrInsert(
            ['id' => 1],
            [
  'id' => 1,
  'restaurant_id' => 1,
  'table_id' => 2,
  'customer_name' => 'Ali',
  'customer_phone' => '03087728724',
  'reservation_date' => '2026-06-10',
  'reservation_time' => '20:20:00',
  'guests' => 6,
  'status' => 'confirmed',
  'event_type' => 'Event',
  'notes' => 'Family Event',
  'created_at' => '2026-06-07 17:18:59',
  'updated_at' => '2026-06-07 17:18:59',
]
        );

        // Seed data for orders
        DB::table('orders')->updateOrInsert(
            ['id' => 1],
            [
  'id' => 1,
  'restaurant_id' => 1,
  'table_id' => 3,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-5KKGGT',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '250.00',
  'notes' => 'Make it extra spicy!',
  'created_at' => '2026-04-21 04:10:45',
  'updated_at' => '2026-04-23 12:10:45',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 2],
            [
  'id' => 2,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-E5HWQ5',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'paid',
  'payment_method' => NULL,
  'total_amount' => '1050.00',
  'notes' => NULL,
  'created_at' => '2026-04-22 06:10:45',
  'updated_at' => '2026-04-24 10:41:25',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 3],
            [
  'id' => 3,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-WCXVSY',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '5100.00',
  'notes' => 'Make it extra spicy!',
  'created_at' => '2026-04-20 02:10:46',
  'updated_at' => '2026-04-23 12:10:46',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 4],
            [
  'id' => 4,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-RDYLX9',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => NULL,
  'payment_status' => 'paid',
  'payment_method' => NULL,
  'total_amount' => '2100.00',
  'notes' => 'Make it extra spicy!',
  'created_at' => '2026-04-23 07:10:46',
  'updated_at' => '2026-06-04 09:46:38',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 5],
            [
  'id' => 5,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-DZYTUO',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '850.00',
  'notes' => NULL,
  'created_at' => '2026-04-18 06:10:46',
  'updated_at' => '2026-04-23 12:10:46',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 6],
            [
  'id' => 6,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-BXVMNW',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '1350.00',
  'notes' => NULL,
  'created_at' => '2026-04-21 02:10:46',
  'updated_at' => '2026-04-23 14:15:14',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 7],
            [
  'id' => 7,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-PVZ7CF',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '1550.00',
  'notes' => NULL,
  'created_at' => '2026-04-20 10:10:47',
  'updated_at' => '2026-04-23 12:10:47',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 8],
            [
  'id' => 8,
  'restaurant_id' => 1,
  'table_id' => 4,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-UIGMWP',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '300.00',
  'notes' => 'Make it extra spicy!',
  'created_at' => '2026-04-20 10:10:47',
  'updated_at' => '2026-06-04 11:04:46',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 9],
            [
  'id' => 9,
  'restaurant_id' => 1,
  'table_id' => 3,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-DSCWIW',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '600.00',
  'notes' => NULL,
  'created_at' => '2026-04-21 11:10:47',
  'updated_at' => '2026-06-04 10:47:40',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 10],
            [
  'id' => 10,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-IV3JKP',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '2100.00',
  'notes' => NULL,
  'created_at' => '2026-04-23 06:10:47',
  'updated_at' => '2026-04-23 12:10:47',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 11],
            [
  'id' => 11,
  'restaurant_id' => 1,
  'table_id' => 3,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-UKWTER',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'paid',
  'payment_method' => NULL,
  'total_amount' => '3900.00',
  'notes' => NULL,
  'created_at' => '2026-04-22 02:10:47',
  'updated_at' => '2026-06-07 19:07:29',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 12],
            [
  'id' => 12,
  'restaurant_id' => 1,
  'table_id' => 4,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-JZYBJP',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '300.00',
  'notes' => 'Make it extra spicy!',
  'created_at' => '2026-04-20 02:10:47',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 13],
            [
  'id' => 13,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-XLSW8C',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => NULL,
  'payment_status' => 'paid',
  'payment_method' => NULL,
  'total_amount' => '1900.00',
  'notes' => NULL,
  'created_at' => '2026-04-22 10:10:48',
  'updated_at' => '2026-06-04 09:12:54',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 14],
            [
  'id' => 14,
  'restaurant_id' => 1,
  'table_id' => 3,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-XFHL7S',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '2700.00',
  'notes' => 'Make it extra spicy!',
  'created_at' => '2026-04-21 06:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 15],
            [
  'id' => 15,
  'restaurant_id' => 1,
  'table_id' => 3,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-YEBRVG',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '200.00',
  'notes' => 'Make it extra spicy!',
  'created_at' => '2026-04-20 09:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 16],
            [
  'id' => 16,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-RCSZ2I',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '1450.00',
  'notes' => 'Make it extra spicy!',
  'created_at' => '2026-04-23 07:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 17],
            [
  'id' => 17,
  'restaurant_id' => 1,
  'table_id' => 4,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-ON2YJN',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => NULL,
  'payment_status' => 'paid',
  'payment_method' => NULL,
  'total_amount' => '700.00',
  'notes' => 'Make it extra spicy!',
  'created_at' => '2026-04-22 04:10:48',
  'updated_at' => '2026-04-24 10:33:58',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 18],
            [
  'id' => 18,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-ZFGM9M',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '600.00',
  'notes' => NULL,
  'created_at' => '2026-04-19 10:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 19],
            [
  'id' => 19,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-GB8YLU',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '3950.00',
  'notes' => 'Make it extra spicy!',
  'created_at' => '2026-04-22 03:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 20],
            [
  'id' => 20,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-WTEAJL',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'paid',
  'payment_method' => NULL,
  'total_amount' => '1350.00',
  'notes' => 'Make it extra spicy!',
  'created_at' => '2026-04-22 04:10:48',
  'updated_at' => '2026-04-24 07:50:49',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 21],
            [
  'id' => 21,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-QYDJQO',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '300.00',
  'notes' => NULL,
  'created_at' => '2026-04-23 02:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 22],
            [
  'id' => 22,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-S2IM6J',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => NULL,
  'payment_status' => 'paid',
  'payment_method' => NULL,
  'total_amount' => '300.00',
  'notes' => NULL,
  'created_at' => '2026-04-23 09:10:48',
  'updated_at' => '2026-04-24 07:50:16',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 23],
            [
  'id' => 23,
  'restaurant_id' => 1,
  'table_id' => 3,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-GLXQ6O',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '5150.00',
  'notes' => 'Make it extra spicy!',
  'created_at' => '2026-04-17 11:10:48',
  'updated_at' => '2026-06-04 09:13:06',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 24],
            [
  'id' => 24,
  'restaurant_id' => 1,
  'table_id' => 4,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-WSXGOL',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '1400.00',
  'notes' => NULL,
  'created_at' => '2026-04-20 07:10:48',
  'updated_at' => '2026-04-23 13:30:46',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 25],
            [
  'id' => 25,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-KZJF6L',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '2250.00',
  'notes' => 'Make it extra spicy!',
  'created_at' => '2026-04-18 07:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 26],
            [
  'id' => 26,
  'restaurant_id' => 1,
  'table_id' => 3,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-50BVCC',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'paid',
  'payment_method' => NULL,
  'total_amount' => '1350.00',
  'notes' => NULL,
  'created_at' => '2026-04-19 10:10:48',
  'updated_at' => '2026-04-24 10:20:13',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 27],
            [
  'id' => 27,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-BO7W0H',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => NULL,
  'payment_status' => 'paid',
  'payment_method' => NULL,
  'total_amount' => '1700.00',
  'notes' => NULL,
  'created_at' => '2026-04-23 08:10:48',
  'updated_at' => '2026-04-24 08:00:25',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 28],
            [
  'id' => 28,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-GAVBDK',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '950.00',
  'notes' => NULL,
  'created_at' => '2026-04-20 02:10:48',
  'updated_at' => '2026-06-04 09:13:08',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 29],
            [
  'id' => 29,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-USMHKU',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '700.00',
  'notes' => 'Make it extra spicy!',
  'created_at' => '2026-04-19 07:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 30],
            [
  'id' => 30,
  'restaurant_id' => 1,
  'table_id' => 4,
  'subtotal' => '0.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ORD-JJB1VU',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => NULL,
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '2350.00',
  'notes' => NULL,
  'created_at' => '2026-04-22 04:10:48',
  'updated_at' => '2026-04-24 10:45:18',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 31],
            [
  'id' => 31,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '1050.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'HZPJGXXL',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => '2026-04-24 11:17:38',
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '1050.00',
  'notes' => 'With out misala',
  'created_at' => '2026-04-24 11:02:38',
  'updated_at' => '2026-04-24 11:03:40',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 32],
            [
  'id' => 32,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '800.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => '0URRETK5',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => '2026-04-24 11:55:02',
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '800.00',
  'notes' => NULL,
  'created_at' => '2026-04-24 11:40:02',
  'updated_at' => '2026-06-04 10:33:35',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 33],
            [
  'id' => 33,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '900.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'XT6GPTEA',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => '2026-06-04 05:47:32',
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '900.00',
  'notes' => NULL,
  'created_at' => '2026-06-04 05:32:32',
  'updated_at' => '2026-06-04 11:04:21',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 34],
            [
  'id' => 34,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '300.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'AHBABG08',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => '2026-06-04 05:50:39',
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '300.00',
  'notes' => NULL,
  'created_at' => '2026-06-04 05:35:39',
  'updated_at' => '2026-06-04 11:04:28',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 35],
            [
  'id' => 35,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '1400.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'NVEXYB5R',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => '2026-06-04 09:24:23',
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '1400.00',
  'notes' => NULL,
  'created_at' => '2026-06-04 09:09:23',
  'updated_at' => '2026-06-04 11:15:14',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 36],
            [
  'id' => 36,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '1650.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'RJTKXJN8',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => '2026-06-04 09:25:14',
  'payment_status' => 'paid',
  'payment_method' => NULL,
  'total_amount' => '1650.00',
  'notes' => NULL,
  'created_at' => '2026-06-04 09:10:14',
  'updated_at' => '2026-06-04 10:47:17',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 37],
            [
  'id' => 37,
  'restaurant_id' => 1,
  'table_id' => 3,
  'subtotal' => '150.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'SLPSTJPQ',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => '2026-06-04 10:16:18',
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '150.00',
  'notes' => NULL,
  'created_at' => '2026-06-04 10:01:18',
  'updated_at' => '2026-06-14 06:59:12',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 38],
            [
  'id' => 38,
  'restaurant_id' => 1,
  'table_id' => 4,
  'subtotal' => '200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'YDXW5SN7',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => '2026-06-04 11:00:38',
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '200.00',
  'notes' => NULL,
  'created_at' => '2026-06-04 10:45:38',
  'updated_at' => '2026-06-04 11:11:48',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 39],
            [
  'id' => 39,
  'restaurant_id' => 1,
  'table_id' => 4,
  'subtotal' => '200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'YBJQO7LZ',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => '2026-06-04 11:03:33',
  'payment_status' => 'paid',
  'payment_method' => NULL,
  'total_amount' => '200.00',
  'notes' => 'tasty',
  'created_at' => '2026-06-04 10:48:33',
  'updated_at' => '2026-06-04 11:11:37',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 40],
            [
  'id' => 40,
  'restaurant_id' => 1,
  'table_id' => 4,
  'subtotal' => '1200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'MQNSITDF',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => '2026-06-04 11:10:26',
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '1200.00',
  'notes' => NULL,
  'created_at' => '2026-06-04 10:55:26',
  'updated_at' => '2026-06-04 10:55:54',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 41],
            [
  'id' => 41,
  'restaurant_id' => 1,
  'table_id' => 4,
  'subtotal' => '200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => '8HGLCCBL',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => '2026-06-04 11:11:58',
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '200.00',
  'notes' => NULL,
  'created_at' => '2026-06-04 10:56:58',
  'updated_at' => '2026-06-07 19:07:25',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 42],
            [
  'id' => 42,
  'restaurant_id' => 1,
  'table_id' => 4,
  'subtotal' => '200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ADNVLQBD',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => '2026-06-04 11:18:54',
  'payment_status' => 'paid',
  'payment_method' => NULL,
  'total_amount' => '200.00',
  'notes' => NULL,
  'created_at' => '2026-06-04 11:03:54',
  'updated_at' => '2026-06-04 11:16:11',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 43],
            [
  'id' => 43,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '1750.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'MHF39SEF',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => '2026-06-05 20:06:35',
  'payment_status' => 'pending',
  'payment_method' => NULL,
  'total_amount' => '1750.00',
  'notes' => 'Extra gareebi
Kheer Malai
Cold juice',
  'created_at' => '2026-06-05 19:51:35',
  'updated_at' => '2026-06-05 19:53:23',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 44],
            [
  'id' => 44,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'UVVE8P6Z',
  'guest_token' => NULL,
  'status' => 'preparing',
  'estimated_completion_time' => '2026-06-05 22:39:32',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '200.00',
  'notes' => 'Address: Ahmed pur east, 808 street, 63351',
  'created_at' => '2026-06-05 22:24:32',
  'updated_at' => '2026-06-07 20:59:20',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 45],
            [
  'id' => 45,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '300.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'LIARM2KQ',
  'guest_token' => NULL,
  'status' => 'preparing',
  'estimated_completion_time' => '2026-06-05 22:47:49',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '300.00',
  'notes' => 'Address: Chiaki Sato, 123 Anywhere St., Any City, ST 12345',
  'created_at' => '2026-06-05 22:32:49',
  'updated_at' => '2026-06-07 19:07:19',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 46],
            [
  'id' => 46,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '650.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'OWQOBOPU',
  'guest_token' => NULL,
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-05 23:13:21',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '650.00',
  'notes' => 'Address: Chiaki Sato, 123 Anywhere St., Any City, ST 12345',
  'created_at' => '2026-06-05 22:58:21',
  'updated_at' => '2026-06-05 22:58:21',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 47],
            [
  'id' => 47,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'RXTWLFTP',
  'guest_token' => NULL,
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-05 23:18:07',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '200.00',
  'notes' => 'Address: Chiaki Sato, 123 Anywhere St., Any City, ST 12345',
  'created_at' => '2026-06-05 23:03:07',
  'updated_at' => '2026-06-05 23:03:07',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 48],
            [
  'id' => 48,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '450.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'SQTFH41Z',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => '2026-06-05 23:22:44',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '450.00',
  'notes' => 'Address: Chiaki Sato, 123 Anywhere St., Any City, ST 12345',
  'created_at' => '2026-06-05 23:07:44',
  'updated_at' => '2026-06-06 06:28:46',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 49],
            [
  'id' => 49,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '150.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'LUO9LXME',
  'guest_token' => NULL,
  'status' => 'served',
  'estimated_completion_time' => '2026-06-06 06:40:07',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '150.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-06 06:25:07',
  'updated_at' => '2026-06-06 06:26:48',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 50],
            [
  'id' => 50,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '1400.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'LDHFLJYU',
  'guest_token' => NULL,
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-06 06:44:22',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '1400.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-06 06:29:22',
  'updated_at' => '2026-06-06 06:29:22',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 51],
            [
  'id' => 51,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '1400.00',
  'coupon_code' => 'TEST10',
  'discount_amount' => '150.00',
  'order_number' => 'AVCITHOC',
  'guest_token' => NULL,
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-06 06:54:20',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '1250.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-06 06:39:20',
  'updated_at' => '2026-06-06 06:39:20',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 52],
            [
  'id' => 52,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '1900.00',
  'coupon_code' => '12345',
  'discount_amount' => '170.00',
  'order_number' => 'OM9ZLFOV',
  'guest_token' => NULL,
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-07 03:20:25',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '1730.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-07 03:05:25',
  'updated_at' => '2026-06-07 03:05:25',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 53],
            [
  'id' => 53,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '650.00',
  'coupon_code' => '12345',
  'discount_amount' => '170.00',
  'order_number' => 'QWY74ZPR',
  'guest_token' => NULL,
  'status' => 'preparing',
  'estimated_completion_time' => '2026-06-07 09:21:50',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '480.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-07 08:56:50',
  'updated_at' => '2026-06-07 09:04:59',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 54],
            [
  'id' => 54,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '250.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'NYGSRV9I',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => '2026-06-07 09:12:24',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '250.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-07 08:57:24',
  'updated_at' => '2026-06-07 08:58:10',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 55],
            [
  'id' => 55,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '1200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'TJ70DNKC',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => '2026-06-07 09:20:57',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '1200.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-07 09:05:57',
  'updated_at' => '2026-06-18 12:01:57',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 56],
            [
  'id' => 56,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => '1EURFPGR',
  'guest_token' => NULL,
  'status' => 'preparing',
  'estimated_completion_time' => '2026-06-07 09:28:52',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '200.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-07 09:13:52',
  'updated_at' => '2026-06-07 09:15:03',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 57],
            [
  'id' => 57,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '150.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'QXO9ZRIQ',
  'guest_token' => NULL,
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-07 09:30:51',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '150.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-07 09:15:51',
  'updated_at' => '2026-06-07 09:15:51',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 58],
            [
  'id' => 58,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '450.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'V4W2TRDU',
  'guest_token' => NULL,
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-07 09:41:35',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '450.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-07 09:16:35',
  'updated_at' => '2026-06-07 09:16:35',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 59],
            [
  'id' => 59,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '450.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'KGLBFN83',
  'guest_token' => NULL,
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-07 09:53:24',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '450.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-07 09:28:24',
  'updated_at' => '2026-06-07 09:28:24',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 60],
            [
  'id' => 60,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '150.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'D669UUTO',
  'guest_token' => NULL,
  'status' => 'preparing',
  'estimated_completion_time' => '2026-06-07 09:51:10',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '150.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-07 09:36:10',
  'updated_at' => '2026-06-07 09:36:45',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 61],
            [
  'id' => 61,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'GIQRM93G',
  'guest_token' => NULL,
  'status' => 'ready',
  'estimated_completion_time' => '2026-06-07 10:05:57',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '200.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-07 09:50:57',
  'updated_at' => '2026-06-07 09:51:33',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 62],
            [
  'id' => 62,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => '3FKXBCQO',
  'guest_token' => NULL,
  'status' => 'preparing',
  'estimated_completion_time' => '2026-06-07 10:06:52',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '200.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-07 09:51:52',
  'updated_at' => '2026-06-07 09:52:18',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 63],
            [
  'id' => 63,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => '0KSJHUKD',
  'guest_token' => NULL,
  'status' => 'preparing',
  'estimated_completion_time' => '2026-06-07 10:26:20',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '200.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-07 10:11:20',
  'updated_at' => '2026-06-07 10:11:43',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 64],
            [
  'id' => 64,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '650.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => '5QACMOUJ',
  'guest_token' => NULL,
  'status' => 'preparing',
  'estimated_completion_time' => '2026-06-07 10:37:50',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '650.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-07 10:12:50',
  'updated_at' => '2026-06-07 10:13:33',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 65],
            [
  'id' => 65,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'X1TE6DWB',
  'guest_token' => '3b597180-57e6-411b-87c4-b02b4c07a4c2',
  'status' => 'preparing',
  'estimated_completion_time' => '2026-06-07 10:32:08',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '200.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-07 10:17:08',
  'updated_at' => '2026-06-07 10:18:10',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 66],
            [
  'id' => 66,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '450.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'TTIJY6YV',
  'guest_token' => '3b597180-57e6-411b-87c4-b02b4c07a4c2',
  'status' => 'preparing',
  'estimated_completion_time' => '2026-06-07 10:42:38',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '450.00',
  'notes' => 'Address: Muhammad Anees , Ahmed Pur East, Any City, ST 12345',
  'created_at' => '2026-06-07 10:17:38',
  'updated_at' => '2026-06-07 10:18:00',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 67],
            [
  'id' => 67,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'MMVMX8X5',
  'guest_token' => '3b597180-57e6-411b-87c4-b02b4c07a4c2',
  'status' => 'preparing',
  'estimated_completion_time' => '2026-06-07 10:49:06',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '200.00',
  'notes' => 'Address: , ,',
  'created_at' => '2026-06-07 10:34:06',
  'updated_at' => '2026-06-07 10:39:00',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 68],
            [
  'id' => 68,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '150.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'CBHVZHNF',
  'guest_token' => '3b597180-57e6-411b-87c4-b02b4c07a4c2',
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-07 10:52:08',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '150.00',
  'notes' => '',
  'created_at' => '2026-06-07 10:37:08',
  'updated_at' => '2026-06-07 10:37:08',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 69],
            [
  'id' => 69,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '150.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'PQJ1W6JM',
  'guest_token' => '3b597180-57e6-411b-87c4-b02b4c07a4c2',
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-07 10:53:43',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '150.00',
  'notes' => '',
  'created_at' => '2026-06-07 10:38:43',
  'updated_at' => '2026-06-07 10:38:43',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 70],
            [
  'id' => 70,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '450.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'QBN9VXJI',
  'guest_token' => '3b597180-57e6-411b-87c4-b02b4c07a4c2',
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-07 11:10:49',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '450.00',
  'notes' => '',
  'created_at' => '2026-06-07 10:45:49',
  'updated_at' => '2026-06-07 10:45:49',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 71],
            [
  'id' => 71,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '250.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'O5HHNAQT',
  'guest_token' => '3b597180-57e6-411b-87c4-b02b4c07a4c2',
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-07 11:19:09',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '250.00',
  'notes' => '',
  'created_at' => '2026-06-07 11:04:09',
  'updated_at' => '2026-06-07 11:04:09',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 72],
            [
  'id' => 72,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '150.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'Y1ET8ASQ',
  'guest_token' => '3b597180-57e6-411b-87c4-b02b4c07a4c2',
  'status' => 'ready',
  'estimated_completion_time' => '2026-06-07 11:46:09',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '150.00',
  'notes' => 'spicy',
  'created_at' => '2026-06-07 11:31:09',
  'updated_at' => '2026-06-07 11:31:48',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 73],
            [
  'id' => 73,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '1200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'LIRDZ9Q9',
  'guest_token' => '895ee6db-e3a9-4c4a-89fa-4283811382e9',
  'status' => 'preparing',
  'estimated_completion_time' => '2026-06-07 18:12:30',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '1200.00',
  'notes' => '',
  'created_at' => '2026-06-07 17:57:30',
  'updated_at' => '2026-06-07 17:58:02',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 74],
            [
  'id' => 74,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '150.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'LUPQ2FVV',
  'guest_token' => '91f87f95-2408-46d9-a7e5-ca74c2451fd7',
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-07 19:06:11',
  'payment_status' => 'pending',
  'payment_method' => 'cash',
  'total_amount' => '150.00',
  'notes' => '',
  'created_at' => '2026-06-07 18:51:11',
  'updated_at' => '2026-06-07 18:51:11',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 75],
            [
  'id' => 75,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '450.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'SJ2523YA',
  'guest_token' => '91f87f95-2408-46d9-a7e5-ca74c2451fd7',
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-07 19:16:58',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '450.00',
  'notes' => '',
  'created_at' => '2026-06-07 18:51:58',
  'updated_at' => '2026-06-07 18:51:58',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 76],
            [
  'id' => 76,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '1650.00',
  'coupon_code' => '12345',
  'discount_amount' => '170.00',
  'order_number' => 'OKWYZWGI',
  'guest_token' => '91f87f95-2408-46d9-a7e5-ca74c2451fd7',
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-07 19:16:47',
  'payment_status' => 'pending',
  'payment_method' => 'cash',
  'total_amount' => '1480.00',
  'notes' => '',
  'created_at' => '2026-06-07 19:01:47',
  'updated_at' => '2026-06-07 19:01:47',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 77],
            [
  'id' => 77,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '150.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ENAOKYSG',
  'guest_token' => '839bf42b-4d42-4e74-8e61-9fafb7529bb5',
  'status' => 'served',
  'estimated_completion_time' => '2026-06-07 19:18:30',
  'payment_status' => 'pending',
  'payment_method' => 'cash',
  'total_amount' => '150.00',
  'notes' => '',
  'created_at' => '2026-06-07 19:03:31',
  'updated_at' => '2026-06-12 05:43:33',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 78],
            [
  'id' => 78,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '250.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'NMVM0DF0',
  'guest_token' => '839bf42b-4d42-4e74-8e61-9fafb7529bb5',
  'status' => 'served',
  'estimated_completion_time' => '2026-06-07 19:19:06',
  'payment_status' => 'pending',
  'payment_method' => 'cash',
  'total_amount' => '250.00',
  'notes' => '',
  'created_at' => '2026-06-07 19:04:06',
  'updated_at' => '2026-06-07 19:07:06',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 79],
            [
  'id' => 79,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '2400.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'ZRIHLMUC',
  'guest_token' => '839bf42b-4d42-4e74-8e61-9fafb7529bb5',
  'status' => 'served',
  'estimated_completion_time' => '2026-06-07 19:28:03',
  'payment_status' => 'paid',
  'payment_method' => 'safepay',
  'total_amount' => '2400.00',
  'notes' => '',
  'created_at' => '2026-06-07 19:13:03',
  'updated_at' => '2026-06-12 16:47:34',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 80],
            [
  'id' => 80,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'R6VPMFYH',
  'guest_token' => '839bf42b-4d42-4e74-8e61-9fafb7529bb5',
  'status' => 'served',
  'estimated_completion_time' => '2026-06-07 20:00:32',
  'payment_status' => 'pending',
  'payment_method' => 'cash',
  'total_amount' => '200.00',
  'notes' => '',
  'created_at' => '2026-06-07 19:45:32',
  'updated_at' => '2026-06-12 05:43:20',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 81],
            [
  'id' => 81,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '1200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'YFNUATJ5',
  'guest_token' => '4c57b969-f375-4894-8b8e-2a21ca7a780d',
  'status' => 'served',
  'estimated_completion_time' => '2026-06-07 21:13:28',
  'payment_status' => 'pending',
  'payment_method' => 'cash',
  'total_amount' => '1200.00',
  'notes' => '',
  'created_at' => '2026-06-07 20:58:28',
  'updated_at' => '2026-06-12 05:39:02',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 82],
            [
  'id' => 82,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '1350.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'LUFZBGTO',
  'guest_token' => '5f88099f-7a88-4114-8b72-e7149de3e9b8',
  'status' => 'served',
  'estimated_completion_time' => '2026-06-12 16:52:47',
  'payment_status' => 'pending',
  'payment_method' => 'cash',
  'total_amount' => '1350.00',
  'notes' => '',
  'created_at' => '2026-06-12 16:37:47',
  'updated_at' => '2026-06-12 16:38:41',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 83],
            [
  'id' => 83,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '950.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'LEN1WYXO',
  'guest_token' => '5f88099f-7a88-4114-8b72-e7149de3e9b8',
  'status' => 'served',
  'estimated_completion_time' => '2026-06-12 17:15:25',
  'payment_status' => 'pending',
  'payment_method' => 'cash',
  'total_amount' => '950.00',
  'notes' => '',
  'created_at' => '2026-06-12 16:50:25',
  'updated_at' => '2026-06-12 16:50:47',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 84],
            [
  'id' => 84,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'QEB8ZEPV',
  'guest_token' => '8cde7d66-93f1-4f61-89fa-4394abe1e699',
  'status' => 'served',
  'estimated_completion_time' => '2026-06-14 09:31:36',
  'payment_status' => 'pending',
  'payment_method' => 'cash',
  'total_amount' => '200.00',
  'notes' => '',
  'created_at' => '2026-06-14 09:16:36',
  'updated_at' => '2026-06-14 09:17:40',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 85],
            [
  'id' => 85,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'MSH5IZXC',
  'guest_token' => '448cef7c-f926-4144-b442-b65e65c1cf61',
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-16 05:23:18',
  'payment_status' => 'pending',
  'payment_method' => 'cash',
  'total_amount' => '200.00',
  'notes' => 'extra spicy',
  'created_at' => '2026-06-16 05:08:18',
  'updated_at' => '2026-06-16 05:08:18',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 86],
            [
  'id' => 86,
  'restaurant_id' => 1,
  'table_id' => 2,
  'subtotal' => '1200.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'NOUNCKBV',
  'guest_token' => '021a5e2a-ed01-4eb7-95ed-1856485563fc',
  'status' => 'pending',
  'estimated_completion_time' => '2026-06-18 11:35:58',
  'payment_status' => 'pending',
  'payment_method' => 'cash',
  'total_amount' => '1200.00',
  'notes' => '',
  'created_at' => '2026-06-18 11:20:58',
  'updated_at' => '2026-06-18 11:20:58',
]
        );
        DB::table('orders')->updateOrInsert(
            ['id' => 87],
            [
  'id' => 87,
  'restaurant_id' => 1,
  'table_id' => 1,
  'subtotal' => '450.00',
  'coupon_code' => NULL,
  'discount_amount' => '0.00',
  'order_number' => 'MQVBD5WD',
  'guest_token' => '021a5e2a-ed01-4eb7-95ed-1856485563fc',
  'status' => 'served',
  'estimated_completion_time' => '2026-06-18 12:27:53',
  'payment_status' => 'pending',
  'payment_method' => 'cash',
  'total_amount' => '450.00',
  'notes' => 'Extra spicy',
  'created_at' => '2026-06-18 12:02:53',
  'updated_at' => '2026-06-18 12:12:40',
]
        );

        // Seed data for order_items
        DB::table('order_items')->updateOrInsert(
            ['id' => 1],
            [
  'id' => 1,
  'order_id' => 1,
  'menu_item_id' => 5,
  'quantity' => 1,
  'unit_price' => '250.00',
  'subtotal' => '250.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:45',
  'updated_at' => '2026-04-23 12:10:45',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 2],
            [
  'id' => 2,
  'order_id' => 2,
  'menu_item_id' => 1,
  'quantity' => 2,
  'unit_price' => '150.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:45',
  'updated_at' => '2026-04-23 12:10:45',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 3],
            [
  'id' => 3,
  'order_id' => 2,
  'menu_item_id' => 5,
  'quantity' => 3,
  'unit_price' => '250.00',
  'subtotal' => '750.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:45',
  'updated_at' => '2026-04-23 12:10:45',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 4],
            [
  'id' => 4,
  'order_id' => 3,
  'menu_item_id' => 4,
  'quantity' => 3,
  'unit_price' => '1200.00',
  'subtotal' => '3600.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:46',
  'updated_at' => '2026-04-23 12:10:46',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 5],
            [
  'id' => 5,
  'order_id' => 3,
  'menu_item_id' => 7,
  'quantity' => 1,
  'unit_price' => '300.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:46',
  'updated_at' => '2026-04-23 12:10:46',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 6],
            [
  'id' => 6,
  'order_id' => 3,
  'menu_item_id' => 4,
  'quantity' => 1,
  'unit_price' => '1200.00',
  'subtotal' => '1200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:46',
  'updated_at' => '2026-04-23 12:10:46',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 7],
            [
  'id' => 7,
  'order_id' => 4,
  'menu_item_id' => 3,
  'quantity' => 2,
  'unit_price' => '450.00',
  'subtotal' => '900.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:46',
  'updated_at' => '2026-04-23 12:10:46',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 8],
            [
  'id' => 8,
  'order_id' => 4,
  'menu_item_id' => 2,
  'quantity' => 2,
  'unit_price' => '200.00',
  'subtotal' => '400.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:46',
  'updated_at' => '2026-04-23 12:10:46',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 9],
            [
  'id' => 9,
  'order_id' => 4,
  'menu_item_id' => 2,
  'quantity' => 3,
  'unit_price' => '200.00',
  'subtotal' => '600.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:46',
  'updated_at' => '2026-04-23 12:10:46',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 10],
            [
  'id' => 10,
  'order_id' => 4,
  'menu_item_id' => 8,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:46',
  'updated_at' => '2026-04-23 12:10:46',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 11],
            [
  'id' => 11,
  'order_id' => 5,
  'menu_item_id' => 6,
  'quantity' => 3,
  'unit_price' => '150.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:46',
  'updated_at' => '2026-04-23 12:10:46',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 12],
            [
  'id' => 12,
  'order_id' => 5,
  'menu_item_id' => 2,
  'quantity' => 2,
  'unit_price' => '200.00',
  'subtotal' => '400.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:46',
  'updated_at' => '2026-04-23 12:10:46',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 13],
            [
  'id' => 13,
  'order_id' => 6,
  'menu_item_id' => 3,
  'quantity' => 3,
  'unit_price' => '450.00',
  'subtotal' => '1350.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:46',
  'updated_at' => '2026-04-23 12:10:46',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 14],
            [
  'id' => 14,
  'order_id' => 7,
  'menu_item_id' => 2,
  'quantity' => 2,
  'unit_price' => '200.00',
  'subtotal' => '400.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:47',
  'updated_at' => '2026-04-23 12:10:47',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 15],
            [
  'id' => 15,
  'order_id' => 7,
  'menu_item_id' => 6,
  'quantity' => 3,
  'unit_price' => '150.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:47',
  'updated_at' => '2026-04-23 12:10:47',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 16],
            [
  'id' => 16,
  'order_id' => 7,
  'menu_item_id' => 6,
  'quantity' => 2,
  'unit_price' => '150.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:47',
  'updated_at' => '2026-04-23 12:10:47',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 17],
            [
  'id' => 17,
  'order_id' => 7,
  'menu_item_id' => 8,
  'quantity' => 2,
  'unit_price' => '200.00',
  'subtotal' => '400.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:47',
  'updated_at' => '2026-04-23 12:10:47',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 18],
            [
  'id' => 18,
  'order_id' => 8,
  'menu_item_id' => 1,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:47',
  'updated_at' => '2026-04-23 12:10:47',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 19],
            [
  'id' => 19,
  'order_id' => 8,
  'menu_item_id' => 1,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:47',
  'updated_at' => '2026-04-23 12:10:47',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 20],
            [
  'id' => 20,
  'order_id' => 9,
  'menu_item_id' => 7,
  'quantity' => 2,
  'unit_price' => '300.00',
  'subtotal' => '600.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:47',
  'updated_at' => '2026-04-23 12:10:47',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 21],
            [
  'id' => 21,
  'order_id' => 10,
  'menu_item_id' => 8,
  'quantity' => 3,
  'unit_price' => '200.00',
  'subtotal' => '600.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:47',
  'updated_at' => '2026-04-23 12:10:47',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 22],
            [
  'id' => 22,
  'order_id' => 10,
  'menu_item_id' => 1,
  'quantity' => 2,
  'unit_price' => '150.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:47',
  'updated_at' => '2026-04-23 12:10:47',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 23],
            [
  'id' => 23,
  'order_id' => 10,
  'menu_item_id' => 4,
  'quantity' => 1,
  'unit_price' => '1200.00',
  'subtotal' => '1200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:47',
  'updated_at' => '2026-04-23 12:10:47',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 24],
            [
  'id' => 24,
  'order_id' => 11,
  'menu_item_id' => 8,
  'quantity' => 3,
  'unit_price' => '200.00',
  'subtotal' => '600.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:47',
  'updated_at' => '2026-04-23 12:10:47',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 25],
            [
  'id' => 25,
  'order_id' => 11,
  'menu_item_id' => 4,
  'quantity' => 2,
  'unit_price' => '1200.00',
  'subtotal' => '2400.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:47',
  'updated_at' => '2026-04-23 12:10:47',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 26],
            [
  'id' => 26,
  'order_id' => 11,
  'menu_item_id' => 3,
  'quantity' => 2,
  'unit_price' => '450.00',
  'subtotal' => '900.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:47',
  'updated_at' => '2026-04-23 12:10:47',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 27],
            [
  'id' => 27,
  'order_id' => 12,
  'menu_item_id' => 1,
  'quantity' => 2,
  'unit_price' => '150.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:47',
  'updated_at' => '2026-04-23 12:10:47',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 28],
            [
  'id' => 28,
  'order_id' => 13,
  'menu_item_id' => 8,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 29],
            [
  'id' => 29,
  'order_id' => 13,
  'menu_item_id' => 4,
  'quantity' => 1,
  'unit_price' => '1200.00',
  'subtotal' => '1200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 30],
            [
  'id' => 30,
  'order_id' => 13,
  'menu_item_id' => 5,
  'quantity' => 2,
  'unit_price' => '250.00',
  'subtotal' => '500.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 31],
            [
  'id' => 31,
  'order_id' => 14,
  'menu_item_id' => 3,
  'quantity' => 3,
  'unit_price' => '450.00',
  'subtotal' => '1350.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 32],
            [
  'id' => 32,
  'order_id' => 14,
  'menu_item_id' => 6,
  'quantity' => 3,
  'unit_price' => '150.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 33],
            [
  'id' => 33,
  'order_id' => 14,
  'menu_item_id' => 3,
  'quantity' => 1,
  'unit_price' => '450.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 34],
            [
  'id' => 34,
  'order_id' => 14,
  'menu_item_id' => 1,
  'quantity' => 3,
  'unit_price' => '150.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 35],
            [
  'id' => 35,
  'order_id' => 15,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 36],
            [
  'id' => 36,
  'order_id' => 16,
  'menu_item_id' => 2,
  'quantity' => 2,
  'unit_price' => '200.00',
  'subtotal' => '400.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 37],
            [
  'id' => 37,
  'order_id' => 16,
  'menu_item_id' => 2,
  'quantity' => 3,
  'unit_price' => '200.00',
  'subtotal' => '600.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 38],
            [
  'id' => 38,
  'order_id' => 16,
  'menu_item_id' => 3,
  'quantity' => 1,
  'unit_price' => '450.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 39],
            [
  'id' => 39,
  'order_id' => 17,
  'menu_item_id' => 2,
  'quantity' => 2,
  'unit_price' => '200.00',
  'subtotal' => '400.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 40],
            [
  'id' => 40,
  'order_id' => 17,
  'menu_item_id' => 7,
  'quantity' => 1,
  'unit_price' => '300.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 41],
            [
  'id' => 41,
  'order_id' => 18,
  'menu_item_id' => 7,
  'quantity' => 2,
  'unit_price' => '300.00',
  'subtotal' => '600.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 42],
            [
  'id' => 42,
  'order_id' => 19,
  'menu_item_id' => 8,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 43],
            [
  'id' => 43,
  'order_id' => 19,
  'menu_item_id' => 3,
  'quantity' => 3,
  'unit_price' => '450.00',
  'subtotal' => '1350.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 44],
            [
  'id' => 44,
  'order_id' => 19,
  'menu_item_id' => 4,
  'quantity' => 2,
  'unit_price' => '1200.00',
  'subtotal' => '2400.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 45],
            [
  'id' => 45,
  'order_id' => 20,
  'menu_item_id' => 3,
  'quantity' => 2,
  'unit_price' => '450.00',
  'subtotal' => '900.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 46],
            [
  'id' => 46,
  'order_id' => 20,
  'menu_item_id' => 1,
  'quantity' => 3,
  'unit_price' => '150.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 47],
            [
  'id' => 47,
  'order_id' => 21,
  'menu_item_id' => 1,
  'quantity' => 2,
  'unit_price' => '150.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 48],
            [
  'id' => 48,
  'order_id' => 22,
  'menu_item_id' => 6,
  'quantity' => 2,
  'unit_price' => '150.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 49],
            [
  'id' => 49,
  'order_id' => 23,
  'menu_item_id' => 7,
  'quantity' => 3,
  'unit_price' => '300.00',
  'subtotal' => '900.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 50],
            [
  'id' => 50,
  'order_id' => 23,
  'menu_item_id' => 8,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 51],
            [
  'id' => 51,
  'order_id' => 23,
  'menu_item_id' => 4,
  'quantity' => 3,
  'unit_price' => '1200.00',
  'subtotal' => '3600.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 52],
            [
  'id' => 52,
  'order_id' => 23,
  'menu_item_id' => 6,
  'quantity' => 3,
  'unit_price' => '150.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 53],
            [
  'id' => 53,
  'order_id' => 24,
  'menu_item_id' => 7,
  'quantity' => 2,
  'unit_price' => '300.00',
  'subtotal' => '600.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 54],
            [
  'id' => 54,
  'order_id' => 24,
  'menu_item_id' => 8,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 55],
            [
  'id' => 55,
  'order_id' => 24,
  'menu_item_id' => 1,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 56],
            [
  'id' => 56,
  'order_id' => 24,
  'menu_item_id' => 6,
  'quantity' => 3,
  'unit_price' => '150.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 57],
            [
  'id' => 57,
  'order_id' => 25,
  'menu_item_id' => 7,
  'quantity' => 3,
  'unit_price' => '300.00',
  'subtotal' => '900.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 58],
            [
  'id' => 58,
  'order_id' => 25,
  'menu_item_id' => 7,
  'quantity' => 3,
  'unit_price' => '300.00',
  'subtotal' => '900.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 59],
            [
  'id' => 59,
  'order_id' => 25,
  'menu_item_id' => 6,
  'quantity' => 3,
  'unit_price' => '150.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 60],
            [
  'id' => 60,
  'order_id' => 26,
  'menu_item_id' => 7,
  'quantity' => 2,
  'unit_price' => '300.00',
  'subtotal' => '600.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 61],
            [
  'id' => 61,
  'order_id' => 26,
  'menu_item_id' => 8,
  'quantity' => 2,
  'unit_price' => '200.00',
  'subtotal' => '400.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 62],
            [
  'id' => 62,
  'order_id' => 26,
  'menu_item_id' => 6,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 63],
            [
  'id' => 63,
  'order_id' => 26,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 64],
            [
  'id' => 64,
  'order_id' => 27,
  'menu_item_id' => 7,
  'quantity' => 2,
  'unit_price' => '300.00',
  'subtotal' => '600.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 65],
            [
  'id' => 65,
  'order_id' => 27,
  'menu_item_id' => 8,
  'quantity' => 3,
  'unit_price' => '200.00',
  'subtotal' => '600.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 66],
            [
  'id' => 66,
  'order_id' => 27,
  'menu_item_id' => 5,
  'quantity' => 2,
  'unit_price' => '250.00',
  'subtotal' => '500.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 67],
            [
  'id' => 67,
  'order_id' => 28,
  'menu_item_id' => 3,
  'quantity' => 1,
  'unit_price' => '450.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 68],
            [
  'id' => 68,
  'order_id' => 28,
  'menu_item_id' => 5,
  'quantity' => 2,
  'unit_price' => '250.00',
  'subtotal' => '500.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 69],
            [
  'id' => 69,
  'order_id' => 29,
  'menu_item_id' => 6,
  'quantity' => 2,
  'unit_price' => '150.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 70],
            [
  'id' => 70,
  'order_id' => 29,
  'menu_item_id' => 8,
  'quantity' => 2,
  'unit_price' => '200.00',
  'subtotal' => '400.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 71],
            [
  'id' => 71,
  'order_id' => 30,
  'menu_item_id' => 4,
  'quantity' => 1,
  'unit_price' => '1200.00',
  'subtotal' => '1200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 72],
            [
  'id' => 72,
  'order_id' => 30,
  'menu_item_id' => 5,
  'quantity' => 3,
  'unit_price' => '250.00',
  'subtotal' => '750.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 73],
            [
  'id' => 73,
  'order_id' => 30,
  'menu_item_id' => 8,
  'quantity' => 2,
  'unit_price' => '200.00',
  'subtotal' => '400.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-23 12:10:48',
  'updated_at' => '2026-04-23 12:10:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 74],
            [
  'id' => 74,
  'order_id' => 31,
  'menu_item_id' => 1,
  'quantity' => 3,
  'unit_price' => '150.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-24 11:02:38',
  'updated_at' => '2026-04-24 11:02:38',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 75],
            [
  'id' => 75,
  'order_id' => 31,
  'menu_item_id' => 2,
  'quantity' => 3,
  'unit_price' => '200.00',
  'subtotal' => '600.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-24 11:02:38',
  'updated_at' => '2026-04-24 11:02:38',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 76],
            [
  'id' => 76,
  'order_id' => 32,
  'menu_item_id' => 1,
  'quantity' => 2,
  'unit_price' => '150.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-24 11:40:02',
  'updated_at' => '2026-04-24 11:40:02',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 77],
            [
  'id' => 77,
  'order_id' => 32,
  'menu_item_id' => 9,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-24 11:40:02',
  'updated_at' => '2026-04-24 11:40:02',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 78],
            [
  'id' => 78,
  'order_id' => 32,
  'menu_item_id' => 7,
  'quantity' => 1,
  'unit_price' => '300.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-04-24 11:40:02',
  'updated_at' => '2026-04-24 11:40:02',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 79],
            [
  'id' => 79,
  'order_id' => 33,
  'menu_item_id' => 1,
  'quantity' => 3,
  'unit_price' => '150.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-04 05:32:32',
  'updated_at' => '2026-06-04 05:32:32',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 80],
            [
  'id' => 80,
  'order_id' => 33,
  'menu_item_id' => 3,
  'quantity' => 1,
  'unit_price' => '450.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-04 05:32:32',
  'updated_at' => '2026-06-04 05:32:32',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 81],
            [
  'id' => 81,
  'order_id' => 34,
  'menu_item_id' => 1,
  'quantity' => 2,
  'unit_price' => '150.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-04 05:35:39',
  'updated_at' => '2026-06-04 05:35:39',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 82],
            [
  'id' => 82,
  'order_id' => 35,
  'menu_item_id' => 7,
  'quantity' => 2,
  'unit_price' => '300.00',
  'subtotal' => '600.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-04 09:09:23',
  'updated_at' => '2026-06-04 09:09:23',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 83],
            [
  'id' => 83,
  'order_id' => 35,
  'menu_item_id' => 3,
  'quantity' => 1,
  'unit_price' => '450.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-04 09:09:23',
  'updated_at' => '2026-06-04 09:09:23',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 84],
            [
  'id' => 84,
  'order_id' => 35,
  'menu_item_id' => 1,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-04 09:09:23',
  'updated_at' => '2026-06-04 09:09:23',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 85],
            [
  'id' => 85,
  'order_id' => 35,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-04 09:09:23',
  'updated_at' => '2026-06-04 09:09:23',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 86],
            [
  'id' => 86,
  'order_id' => 36,
  'menu_item_id' => 4,
  'quantity' => 1,
  'unit_price' => '1200.00',
  'subtotal' => '1200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-04 09:10:14',
  'updated_at' => '2026-06-04 09:10:14',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 87],
            [
  'id' => 87,
  'order_id' => 36,
  'menu_item_id' => 3,
  'quantity' => 1,
  'unit_price' => '450.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-04 09:10:14',
  'updated_at' => '2026-06-04 09:10:14',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 88],
            [
  'id' => 88,
  'order_id' => 37,
  'menu_item_id' => 6,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-04 10:01:18',
  'updated_at' => '2026-06-04 10:01:18',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 89],
            [
  'id' => 89,
  'order_id' => 38,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-04 10:45:38',
  'updated_at' => '2026-06-04 10:45:38',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 90],
            [
  'id' => 90,
  'order_id' => 39,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-04 10:48:33',
  'updated_at' => '2026-06-04 10:48:33',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 91],
            [
  'id' => 91,
  'order_id' => 40,
  'menu_item_id' => 4,
  'quantity' => 1,
  'unit_price' => '1200.00',
  'subtotal' => '1200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-04 10:55:26',
  'updated_at' => '2026-06-04 10:55:26',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 92],
            [
  'id' => 92,
  'order_id' => 41,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-04 10:56:58',
  'updated_at' => '2026-06-04 10:56:58',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 93],
            [
  'id' => 93,
  'order_id' => 42,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-04 11:03:54',
  'updated_at' => '2026-06-04 11:03:54',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 94],
            [
  'id' => 94,
  'order_id' => 43,
  'menu_item_id' => 4,
  'quantity' => 1,
  'unit_price' => '1200.00',
  'subtotal' => '1200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-05 19:51:35',
  'updated_at' => '2026-06-05 19:51:35',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 95],
            [
  'id' => 95,
  'order_id' => 43,
  'menu_item_id' => 5,
  'quantity' => 1,
  'unit_price' => '250.00',
  'subtotal' => '250.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-05 19:51:35',
  'updated_at' => '2026-06-05 19:51:35',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 96],
            [
  'id' => 96,
  'order_id' => 43,
  'menu_item_id' => 7,
  'quantity' => 1,
  'unit_price' => '300.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-05 19:51:35',
  'updated_at' => '2026-06-05 19:51:35',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 97],
            [
  'id' => 97,
  'order_id' => 44,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-05 22:24:32',
  'updated_at' => '2026-06-05 22:24:32',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 98],
            [
  'id' => 98,
  'order_id' => 45,
  'menu_item_id' => 1,
  'quantity' => 2,
  'unit_price' => '150.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-05 22:32:49',
  'updated_at' => '2026-06-05 22:32:49',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 99],
            [
  'id' => 99,
  'order_id' => 46,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-05 22:58:21',
  'updated_at' => '2026-06-05 22:58:21',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 100],
            [
  'id' => 100,
  'order_id' => 46,
  'menu_item_id' => 3,
  'quantity' => 1,
  'unit_price' => '450.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-05 22:58:21',
  'updated_at' => '2026-06-05 22:58:21',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 101],
            [
  'id' => 101,
  'order_id' => 47,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-05 23:03:07',
  'updated_at' => '2026-06-05 23:03:07',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 102],
            [
  'id' => 102,
  'order_id' => 48,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-05 23:07:44',
  'updated_at' => '2026-06-05 23:07:44',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 103],
            [
  'id' => 103,
  'order_id' => 48,
  'menu_item_id' => 5,
  'quantity' => 1,
  'unit_price' => '250.00',
  'subtotal' => '250.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-05 23:07:44',
  'updated_at' => '2026-06-05 23:07:44',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 104],
            [
  'id' => 104,
  'order_id' => 49,
  'menu_item_id' => 1,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-06 06:25:07',
  'updated_at' => '2026-06-06 06:25:07',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 105],
            [
  'id' => 105,
  'order_id' => 50,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-06 06:29:22',
  'updated_at' => '2026-06-06 06:29:22',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 106],
            [
  'id' => 106,
  'order_id' => 50,
  'menu_item_id' => 4,
  'quantity' => 1,
  'unit_price' => '1200.00',
  'subtotal' => '1200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-06 06:29:22',
  'updated_at' => '2026-06-06 06:29:22',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 107],
            [
  'id' => 107,
  'order_id' => 51,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-06 06:39:20',
  'updated_at' => '2026-06-06 06:39:20',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 108],
            [
  'id' => 108,
  'order_id' => 51,
  'menu_item_id' => 4,
  'quantity' => 1,
  'unit_price' => '1200.00',
  'subtotal' => '1200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-06 06:39:20',
  'updated_at' => '2026-06-06 06:39:20',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 109],
            [
  'id' => 109,
  'order_id' => 52,
  'menu_item_id' => 4,
  'quantity' => 1,
  'unit_price' => '1200.00',
  'subtotal' => '1200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 03:05:25',
  'updated_at' => '2026-06-07 03:05:25',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 110],
            [
  'id' => 110,
  'order_id' => 52,
  'menu_item_id' => 1,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 03:05:25',
  'updated_at' => '2026-06-07 03:05:25',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 111],
            [
  'id' => 111,
  'order_id' => 52,
  'menu_item_id' => 5,
  'quantity' => 1,
  'unit_price' => '250.00',
  'subtotal' => '250.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 03:05:25',
  'updated_at' => '2026-06-07 03:05:25',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 112],
            [
  'id' => 112,
  'order_id' => 52,
  'menu_item_id' => 7,
  'quantity' => 1,
  'unit_price' => '300.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 03:05:25',
  'updated_at' => '2026-06-07 03:05:25',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 113],
            [
  'id' => 113,
  'order_id' => 53,
  'menu_item_id' => 3,
  'quantity' => 1,
  'unit_price' => '450.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 08:56:50',
  'updated_at' => '2026-06-07 08:56:50',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 114],
            [
  'id' => 114,
  'order_id' => 53,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 08:56:50',
  'updated_at' => '2026-06-07 08:56:50',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 115],
            [
  'id' => 115,
  'order_id' => 54,
  'menu_item_id' => 5,
  'quantity' => 1,
  'unit_price' => '250.00',
  'subtotal' => '250.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 08:57:24',
  'updated_at' => '2026-06-07 08:57:24',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 116],
            [
  'id' => 116,
  'order_id' => 55,
  'menu_item_id' => 4,
  'quantity' => 1,
  'unit_price' => '1200.00',
  'subtotal' => '1200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 09:05:57',
  'updated_at' => '2026-06-07 09:05:57',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 117],
            [
  'id' => 117,
  'order_id' => 56,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 09:13:52',
  'updated_at' => '2026-06-07 09:13:52',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 118],
            [
  'id' => 118,
  'order_id' => 57,
  'menu_item_id' => 1,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 09:15:51',
  'updated_at' => '2026-06-07 09:15:51',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 119],
            [
  'id' => 119,
  'order_id' => 58,
  'menu_item_id' => 3,
  'quantity' => 1,
  'unit_price' => '450.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 09:16:35',
  'updated_at' => '2026-06-07 09:16:35',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 120],
            [
  'id' => 120,
  'order_id' => 59,
  'menu_item_id' => 3,
  'quantity' => 1,
  'unit_price' => '450.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 09:28:24',
  'updated_at' => '2026-06-07 09:28:24',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 121],
            [
  'id' => 121,
  'order_id' => 60,
  'menu_item_id' => 1,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 09:36:10',
  'updated_at' => '2026-06-07 09:36:10',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 122],
            [
  'id' => 122,
  'order_id' => 61,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 09:50:57',
  'updated_at' => '2026-06-07 09:50:57',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 123],
            [
  'id' => 123,
  'order_id' => 62,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 09:51:52',
  'updated_at' => '2026-06-07 09:51:52',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 124],
            [
  'id' => 124,
  'order_id' => 63,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 10:11:20',
  'updated_at' => '2026-06-07 10:11:20',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 125],
            [
  'id' => 125,
  'order_id' => 64,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 10:12:50',
  'updated_at' => '2026-06-07 10:12:50',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 126],
            [
  'id' => 126,
  'order_id' => 64,
  'menu_item_id' => 3,
  'quantity' => 1,
  'unit_price' => '450.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 10:12:50',
  'updated_at' => '2026-06-07 10:12:50',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 127],
            [
  'id' => 127,
  'order_id' => 65,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 10:17:08',
  'updated_at' => '2026-06-07 10:17:08',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 128],
            [
  'id' => 128,
  'order_id' => 66,
  'menu_item_id' => 3,
  'quantity' => 1,
  'unit_price' => '450.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 10:17:38',
  'updated_at' => '2026-06-07 10:17:38',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 129],
            [
  'id' => 129,
  'order_id' => 67,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 10:34:06',
  'updated_at' => '2026-06-07 10:34:06',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 130],
            [
  'id' => 130,
  'order_id' => 68,
  'menu_item_id' => 6,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 10:37:08',
  'updated_at' => '2026-06-07 10:37:08',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 131],
            [
  'id' => 131,
  'order_id' => 69,
  'menu_item_id' => 1,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 10:38:43',
  'updated_at' => '2026-06-07 10:38:43',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 132],
            [
  'id' => 132,
  'order_id' => 70,
  'menu_item_id' => 3,
  'quantity' => 1,
  'unit_price' => '450.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 10:45:49',
  'updated_at' => '2026-06-07 10:45:49',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 133],
            [
  'id' => 133,
  'order_id' => 71,
  'menu_item_id' => 5,
  'quantity' => 1,
  'unit_price' => '250.00',
  'subtotal' => '250.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 11:04:09',
  'updated_at' => '2026-06-07 11:04:09',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 134],
            [
  'id' => 134,
  'order_id' => 72,
  'menu_item_id' => 1,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 11:31:09',
  'updated_at' => '2026-06-07 11:31:09',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 135],
            [
  'id' => 135,
  'order_id' => 73,
  'menu_item_id' => 4,
  'quantity' => 1,
  'unit_price' => '1200.00',
  'subtotal' => '1200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 17:57:30',
  'updated_at' => '2026-06-07 17:57:30',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 136],
            [
  'id' => 136,
  'order_id' => 74,
  'menu_item_id' => 1,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 18:51:11',
  'updated_at' => '2026-06-07 18:51:11',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 137],
            [
  'id' => 137,
  'order_id' => 75,
  'menu_item_id' => 3,
  'quantity' => 1,
  'unit_price' => '450.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 18:51:58',
  'updated_at' => '2026-06-07 18:51:58',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 138],
            [
  'id' => 138,
  'order_id' => 76,
  'menu_item_id' => 1,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 19:01:48',
  'updated_at' => '2026-06-07 19:01:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 139],
            [
  'id' => 139,
  'order_id' => 76,
  'menu_item_id' => 4,
  'quantity' => 1,
  'unit_price' => '1200.00',
  'subtotal' => '1200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 19:01:48',
  'updated_at' => '2026-06-07 19:01:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 140],
            [
  'id' => 140,
  'order_id' => 76,
  'menu_item_id' => 7,
  'quantity' => 1,
  'unit_price' => '300.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 19:01:48',
  'updated_at' => '2026-06-07 19:01:48',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 141],
            [
  'id' => 141,
  'order_id' => 77,
  'menu_item_id' => 6,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 19:03:31',
  'updated_at' => '2026-06-07 19:03:31',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 142],
            [
  'id' => 142,
  'order_id' => 78,
  'menu_item_id' => 5,
  'quantity' => 1,
  'unit_price' => '250.00',
  'subtotal' => '250.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 19:04:06',
  'updated_at' => '2026-06-07 19:04:06',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 143],
            [
  'id' => 143,
  'order_id' => 79,
  'menu_item_id' => 4,
  'quantity' => 2,
  'unit_price' => '1200.00',
  'subtotal' => '2400.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 19:13:03',
  'updated_at' => '2026-06-07 19:13:03',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 144],
            [
  'id' => 144,
  'order_id' => 80,
  'menu_item_id' => 9,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 19:45:32',
  'updated_at' => '2026-06-07 19:45:32',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 145],
            [
  'id' => 145,
  'order_id' => 81,
  'menu_item_id' => 4,
  'quantity' => 1,
  'unit_price' => '1200.00',
  'subtotal' => '1200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-07 20:58:28',
  'updated_at' => '2026-06-07 20:58:28',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 146],
            [
  'id' => 146,
  'order_id' => 82,
  'menu_item_id' => 1,
  'quantity' => 1,
  'unit_price' => '150.00',
  'subtotal' => '150.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-12 16:37:47',
  'updated_at' => '2026-06-12 16:37:47',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 147],
            [
  'id' => 147,
  'order_id' => 82,
  'menu_item_id' => 4,
  'quantity' => 1,
  'unit_price' => '1200.00',
  'subtotal' => '1200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-12 16:37:47',
  'updated_at' => '2026-06-12 16:37:47',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 148],
            [
  'id' => 148,
  'order_id' => 83,
  'menu_item_id' => 3,
  'quantity' => 1,
  'unit_price' => '450.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-12 16:50:25',
  'updated_at' => '2026-06-12 16:50:25',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 149],
            [
  'id' => 149,
  'order_id' => 83,
  'menu_item_id' => 9,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-12 16:50:25',
  'updated_at' => '2026-06-12 16:50:25',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 150],
            [
  'id' => 150,
  'order_id' => 83,
  'menu_item_id' => 7,
  'quantity' => 1,
  'unit_price' => '300.00',
  'subtotal' => '300.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-12 16:50:25',
  'updated_at' => '2026-06-12 16:50:25',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 151],
            [
  'id' => 151,
  'order_id' => 84,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-14 09:16:36',
  'updated_at' => '2026-06-14 09:16:36',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 152],
            [
  'id' => 152,
  'order_id' => 85,
  'menu_item_id' => 2,
  'quantity' => 1,
  'unit_price' => '200.00',
  'subtotal' => '200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-16 05:08:18',
  'updated_at' => '2026-06-16 05:08:18',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 153],
            [
  'id' => 153,
  'order_id' => 86,
  'menu_item_id' => 4,
  'quantity' => 1,
  'unit_price' => '1200.00',
  'subtotal' => '1200.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-18 11:20:58',
  'updated_at' => '2026-06-18 11:20:58',
]
        );
        DB::table('order_items')->updateOrInsert(
            ['id' => 154],
            [
  'id' => 154,
  'order_id' => 87,
  'menu_item_id' => 3,
  'quantity' => 1,
  'unit_price' => '450.00',
  'subtotal' => '450.00',
  'special_instructions' => NULL,
  'created_at' => '2026-06-18 12:02:53',
  'updated_at' => '2026-06-18 12:02:53',
]
        );

        // Seed data for payments
        DB::table('payments')->updateOrInsert(
            ['id' => 1],
            [
  'id' => 1,
  'restaurant_id' => 1,
  'order_id' => 22,
  'amount' => '300.00',
  'method' => 'cash',
  'transaction_id' => NULL,
  'status' => 'paid',
  'notes' => NULL,
  'created_at' => '2026-04-24 07:50:16',
  'updated_at' => '2026-04-24 07:50:16',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 2],
            [
  'id' => 2,
  'restaurant_id' => 1,
  'order_id' => 4,
  'amount' => '2100.00',
  'method' => 'cash',
  'transaction_id' => NULL,
  'status' => 'paid',
  'notes' => NULL,
  'created_at' => '2026-04-24 07:50:35',
  'updated_at' => '2026-04-24 07:50:35',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 3],
            [
  'id' => 3,
  'restaurant_id' => 1,
  'order_id' => 20,
  'amount' => '1350.00',
  'method' => 'cash',
  'transaction_id' => NULL,
  'status' => 'paid',
  'notes' => NULL,
  'created_at' => '2026-04-24 07:50:49',
  'updated_at' => '2026-04-24 07:50:49',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 4],
            [
  'id' => 4,
  'restaurant_id' => 1,
  'order_id' => 27,
  'amount' => '1700.00',
  'method' => 'cash',
  'transaction_id' => NULL,
  'status' => 'paid',
  'notes' => NULL,
  'created_at' => '2026-04-24 08:00:25',
  'updated_at' => '2026-04-24 08:00:25',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 5],
            [
  'id' => 5,
  'restaurant_id' => 1,
  'order_id' => 13,
  'amount' => '1900.00',
  'method' => 'cash',
  'transaction_id' => NULL,
  'status' => 'paid',
  'notes' => NULL,
  'created_at' => '2026-04-24 09:37:40',
  'updated_at' => '2026-04-24 09:37:40',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 6],
            [
  'id' => 6,
  'restaurant_id' => 1,
  'order_id' => 11,
  'amount' => '3900.00',
  'method' => 'cash',
  'transaction_id' => NULL,
  'status' => 'paid',
  'notes' => NULL,
  'created_at' => '2026-04-24 10:19:26',
  'updated_at' => '2026-04-24 10:19:26',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 7],
            [
  'id' => 7,
  'restaurant_id' => 1,
  'order_id' => 26,
  'amount' => '1350.00',
  'method' => 'cash',
  'transaction_id' => NULL,
  'status' => 'paid',
  'notes' => NULL,
  'created_at' => '2026-04-24 10:20:13',
  'updated_at' => '2026-04-24 10:20:13',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 8],
            [
  'id' => 8,
  'restaurant_id' => 1,
  'order_id' => 17,
  'amount' => '700.00',
  'method' => 'cash',
  'transaction_id' => NULL,
  'status' => 'paid',
  'notes' => NULL,
  'created_at' => '2026-04-24 10:33:34',
  'updated_at' => '2026-04-24 10:33:34',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 9],
            [
  'id' => 9,
  'restaurant_id' => 1,
  'order_id' => 2,
  'amount' => '1050.00',
  'method' => 'cash',
  'transaction_id' => NULL,
  'status' => 'paid',
  'notes' => NULL,
  'created_at' => '2026-04-24 10:41:25',
  'updated_at' => '2026-04-24 10:41:25',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 10],
            [
  'id' => 10,
  'restaurant_id' => 1,
  'order_id' => 36,
  'amount' => '1650.00',
  'method' => 'cash',
  'transaction_id' => NULL,
  'status' => 'paid',
  'notes' => NULL,
  'created_at' => '2026-06-04 09:11:00',
  'updated_at' => '2026-06-04 09:11:00',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 11],
            [
  'id' => 11,
  'restaurant_id' => 1,
  'order_id' => 39,
  'amount' => '200.00',
  'method' => 'cash',
  'transaction_id' => NULL,
  'status' => 'paid',
  'notes' => NULL,
  'created_at' => '2026-06-04 10:53:40',
  'updated_at' => '2026-06-04 10:53:40',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 12],
            [
  'id' => 12,
  'restaurant_id' => 1,
  'order_id' => 42,
  'amount' => '200.00',
  'method' => 'cash',
  'transaction_id' => NULL,
  'status' => 'paid',
  'notes' => NULL,
  'created_at' => '2026-06-04 11:16:11',
  'updated_at' => '2026-06-04 11:16:11',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 13],
            [
  'id' => 13,
  'restaurant_id' => 1,
  'order_id' => 44,
  'amount' => '200.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-71333824',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-05 22:24:32',
  'updated_at' => '2026-06-05 22:24:32',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 14],
            [
  'id' => 14,
  'restaurant_id' => 1,
  'order_id' => 45,
  'amount' => '300.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-89548697',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-05 22:32:49',
  'updated_at' => '2026-06-05 22:32:49',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 15],
            [
  'id' => 15,
  'restaurant_id' => 1,
  'order_id' => 46,
  'amount' => '650.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-59714739',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-05 22:58:22',
  'updated_at' => '2026-06-05 22:58:22',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 16],
            [
  'id' => 16,
  'restaurant_id' => 1,
  'order_id' => 47,
  'amount' => '200.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-59147144',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-05 23:03:07',
  'updated_at' => '2026-06-05 23:03:07',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 17],
            [
  'id' => 17,
  'restaurant_id' => 1,
  'order_id' => 48,
  'amount' => '450.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-51632963',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-05 23:07:44',
  'updated_at' => '2026-06-05 23:07:44',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 18],
            [
  'id' => 18,
  'restaurant_id' => 1,
  'order_id' => 49,
  'amount' => '150.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-65327293',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-06 06:25:07',
  'updated_at' => '2026-06-06 06:25:07',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 19],
            [
  'id' => 19,
  'restaurant_id' => 1,
  'order_id' => 50,
  'amount' => '1400.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-45459141',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-06 06:29:22',
  'updated_at' => '2026-06-06 06:29:22',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 20],
            [
  'id' => 20,
  'restaurant_id' => 1,
  'order_id' => 51,
  'amount' => '1250.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-16011110',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-06 06:39:20',
  'updated_at' => '2026-06-06 06:39:20',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 21],
            [
  'id' => 21,
  'restaurant_id' => 1,
  'order_id' => 52,
  'amount' => '1730.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-71962045',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 03:05:25',
  'updated_at' => '2026-06-07 03:05:25',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 22],
            [
  'id' => 22,
  'restaurant_id' => 1,
  'order_id' => 53,
  'amount' => '480.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-92745766',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 08:56:50',
  'updated_at' => '2026-06-07 08:56:50',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 23],
            [
  'id' => 23,
  'restaurant_id' => 1,
  'order_id' => 54,
  'amount' => '250.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-46286462',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 08:57:24',
  'updated_at' => '2026-06-07 08:57:24',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 24],
            [
  'id' => 24,
  'restaurant_id' => 1,
  'order_id' => 55,
  'amount' => '1200.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-12901759',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 09:05:57',
  'updated_at' => '2026-06-07 09:05:57',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 25],
            [
  'id' => 25,
  'restaurant_id' => 1,
  'order_id' => 56,
  'amount' => '200.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-10558002',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 09:13:52',
  'updated_at' => '2026-06-07 09:13:52',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 26],
            [
  'id' => 26,
  'restaurant_id' => 1,
  'order_id' => 57,
  'amount' => '150.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-97723851',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 09:15:51',
  'updated_at' => '2026-06-07 09:15:51',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 27],
            [
  'id' => 27,
  'restaurant_id' => 1,
  'order_id' => 58,
  'amount' => '450.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-65255576',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 09:16:35',
  'updated_at' => '2026-06-07 09:16:35',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 28],
            [
  'id' => 28,
  'restaurant_id' => 1,
  'order_id' => 59,
  'amount' => '450.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-41539732',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 09:28:24',
  'updated_at' => '2026-06-07 09:28:24',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 29],
            [
  'id' => 29,
  'restaurant_id' => 1,
  'order_id' => 60,
  'amount' => '150.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-27931453',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 09:36:10',
  'updated_at' => '2026-06-07 09:36:10',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 30],
            [
  'id' => 30,
  'restaurant_id' => 1,
  'order_id' => 61,
  'amount' => '200.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-23529862',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 09:50:57',
  'updated_at' => '2026-06-07 09:50:57',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 31],
            [
  'id' => 31,
  'restaurant_id' => 1,
  'order_id' => 62,
  'amount' => '200.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-77280958',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 09:51:52',
  'updated_at' => '2026-06-07 09:51:52',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 32],
            [
  'id' => 32,
  'restaurant_id' => 1,
  'order_id' => 63,
  'amount' => '200.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-83390163',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 10:11:20',
  'updated_at' => '2026-06-07 10:11:20',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 33],
            [
  'id' => 33,
  'restaurant_id' => 1,
  'order_id' => 64,
  'amount' => '650.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-44956170',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 10:12:50',
  'updated_at' => '2026-06-07 10:12:50',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 34],
            [
  'id' => 34,
  'restaurant_id' => 1,
  'order_id' => 65,
  'amount' => '200.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-20244899',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 10:17:08',
  'updated_at' => '2026-06-07 10:17:08',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 35],
            [
  'id' => 35,
  'restaurant_id' => 1,
  'order_id' => 66,
  'amount' => '450.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-32304692',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 10:17:38',
  'updated_at' => '2026-06-07 10:17:38',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 36],
            [
  'id' => 36,
  'restaurant_id' => 1,
  'order_id' => 67,
  'amount' => '200.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-77669731',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 10:34:06',
  'updated_at' => '2026-06-07 10:34:06',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 37],
            [
  'id' => 37,
  'restaurant_id' => 1,
  'order_id' => 68,
  'amount' => '150.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-60120236',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 10:37:08',
  'updated_at' => '2026-06-07 10:37:08',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 38],
            [
  'id' => 38,
  'restaurant_id' => 1,
  'order_id' => 69,
  'amount' => '150.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-46720868',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 10:38:43',
  'updated_at' => '2026-06-07 10:38:43',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 39],
            [
  'id' => 39,
  'restaurant_id' => 1,
  'order_id' => 70,
  'amount' => '450.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-38198754',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 10:45:49',
  'updated_at' => '2026-06-07 10:45:49',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 40],
            [
  'id' => 40,
  'restaurant_id' => 1,
  'order_id' => 71,
  'amount' => '250.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-48716610',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 11:04:09',
  'updated_at' => '2026-06-07 11:04:09',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 41],
            [
  'id' => 41,
  'restaurant_id' => 1,
  'order_id' => 72,
  'amount' => '150.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-25268932',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 11:31:09',
  'updated_at' => '2026-06-07 11:31:09',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 42],
            [
  'id' => 42,
  'restaurant_id' => 1,
  'order_id' => 73,
  'amount' => '1200.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-20432937',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 17:57:30',
  'updated_at' => '2026-06-07 17:57:30',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 43],
            [
  'id' => 43,
  'restaurant_id' => 1,
  'order_id' => 75,
  'amount' => '450.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-89679615',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 18:51:58',
  'updated_at' => '2026-06-07 18:51:58',
]
        );
        DB::table('payments')->updateOrInsert(
            ['id' => 44],
            [
  'id' => 44,
  'restaurant_id' => 1,
  'order_id' => 79,
  'amount' => '2400.00',
  'method' => 'safepay',
  'transaction_id' => 'SF-69531986',
  'status' => 'paid',
  'notes' => 'Online payment completed successfully via guest checkout.',
  'created_at' => '2026-06-07 19:13:03',
  'updated_at' => '2026-06-07 19:13:03',
]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        // Remove data for payments
        DB::table('payments')->whereIn('id', [
  0 => 1,
  1 => 2,
  2 => 3,
  3 => 4,
  4 => 5,
  5 => 6,
  6 => 7,
  7 => 8,
  8 => 9,
  9 => 10,
  10 => 11,
  11 => 12,
  12 => 13,
  13 => 14,
  14 => 15,
  15 => 16,
  16 => 17,
  17 => 18,
  18 => 19,
  19 => 20,
  20 => 21,
  21 => 22,
  22 => 23,
  23 => 24,
  24 => 25,
  25 => 26,
  26 => 27,
  27 => 28,
  28 => 29,
  29 => 30,
  30 => 31,
  31 => 32,
  32 => 33,
  33 => 34,
  34 => 35,
  35 => 36,
  36 => 37,
  37 => 38,
  38 => 39,
  39 => 40,
  40 => 41,
  41 => 42,
  42 => 43,
  43 => 44,
])->delete();

        // Remove data for order_items
        DB::table('order_items')->whereIn('id', [
  0 => 1,
  1 => 2,
  2 => 3,
  3 => 4,
  4 => 5,
  5 => 6,
  6 => 7,
  7 => 8,
  8 => 9,
  9 => 10,
  10 => 11,
  11 => 12,
  12 => 13,
  13 => 14,
  14 => 15,
  15 => 16,
  16 => 17,
  17 => 18,
  18 => 19,
  19 => 20,
  20 => 21,
  21 => 22,
  22 => 23,
  23 => 24,
  24 => 25,
  25 => 26,
  26 => 27,
  27 => 28,
  28 => 29,
  29 => 30,
  30 => 31,
  31 => 32,
  32 => 33,
  33 => 34,
  34 => 35,
  35 => 36,
  36 => 37,
  37 => 38,
  38 => 39,
  39 => 40,
  40 => 41,
  41 => 42,
  42 => 43,
  43 => 44,
  44 => 45,
  45 => 46,
  46 => 47,
  47 => 48,
  48 => 49,
  49 => 50,
  50 => 51,
  51 => 52,
  52 => 53,
  53 => 54,
  54 => 55,
  55 => 56,
  56 => 57,
  57 => 58,
  58 => 59,
  59 => 60,
  60 => 61,
  61 => 62,
  62 => 63,
  63 => 64,
  64 => 65,
  65 => 66,
  66 => 67,
  67 => 68,
  68 => 69,
  69 => 70,
  70 => 71,
  71 => 72,
  72 => 73,
  73 => 74,
  74 => 75,
  75 => 76,
  76 => 77,
  77 => 78,
  78 => 79,
  79 => 80,
  80 => 81,
  81 => 82,
  82 => 83,
  83 => 84,
  84 => 85,
  85 => 86,
  86 => 87,
  87 => 88,
  88 => 89,
  89 => 90,
  90 => 91,
  91 => 92,
  92 => 93,
  93 => 94,
  94 => 95,
  95 => 96,
  96 => 97,
  97 => 98,
  98 => 99,
  99 => 100,
  100 => 101,
  101 => 102,
  102 => 103,
  103 => 104,
  104 => 105,
  105 => 106,
  106 => 107,
  107 => 108,
  108 => 109,
  109 => 110,
  110 => 111,
  111 => 112,
  112 => 113,
  113 => 114,
  114 => 115,
  115 => 116,
  116 => 117,
  117 => 118,
  118 => 119,
  119 => 120,
  120 => 121,
  121 => 122,
  122 => 123,
  123 => 124,
  124 => 125,
  125 => 126,
  126 => 127,
  127 => 128,
  128 => 129,
  129 => 130,
  130 => 131,
  131 => 132,
  132 => 133,
  133 => 134,
  134 => 135,
  135 => 136,
  136 => 137,
  137 => 138,
  138 => 139,
  139 => 140,
  140 => 141,
  141 => 142,
  142 => 143,
  143 => 144,
  144 => 145,
  145 => 146,
  146 => 147,
  147 => 148,
  148 => 149,
  149 => 150,
  150 => 151,
  151 => 152,
  152 => 153,
  153 => 154,
])->delete();

        // Remove data for orders
        DB::table('orders')->whereIn('id', [
  0 => 1,
  1 => 2,
  2 => 3,
  3 => 4,
  4 => 5,
  5 => 6,
  6 => 7,
  7 => 8,
  8 => 9,
  9 => 10,
  10 => 11,
  11 => 12,
  12 => 13,
  13 => 14,
  14 => 15,
  15 => 16,
  16 => 17,
  17 => 18,
  18 => 19,
  19 => 20,
  20 => 21,
  21 => 22,
  22 => 23,
  23 => 24,
  24 => 25,
  25 => 26,
  26 => 27,
  27 => 28,
  28 => 29,
  29 => 30,
  30 => 31,
  31 => 32,
  32 => 33,
  33 => 34,
  34 => 35,
  35 => 36,
  36 => 37,
  37 => 38,
  38 => 39,
  39 => 40,
  40 => 41,
  41 => 42,
  42 => 43,
  43 => 44,
  44 => 45,
  45 => 46,
  46 => 47,
  47 => 48,
  48 => 49,
  49 => 50,
  50 => 51,
  51 => 52,
  52 => 53,
  53 => 54,
  54 => 55,
  55 => 56,
  56 => 57,
  57 => 58,
  58 => 59,
  59 => 60,
  60 => 61,
  61 => 62,
  62 => 63,
  63 => 64,
  64 => 65,
  65 => 66,
  66 => 67,
  67 => 68,
  68 => 69,
  69 => 70,
  70 => 71,
  71 => 72,
  72 => 73,
  73 => 74,
  74 => 75,
  75 => 76,
  76 => 77,
  77 => 78,
  78 => 79,
  79 => 80,
  80 => 81,
  81 => 82,
  82 => 83,
  83 => 84,
  84 => 85,
  85 => 86,
  86 => 87,
])->delete();

        // Remove data for reservations
        DB::table('reservations')->whereIn('id', [
  0 => 1,
])->delete();

        // Remove data for qr_codes
        DB::table('qr_codes')->whereIn('id', [
  0 => 1,
  1 => 2,
  2 => 3,
  3 => 4,
  4 => 5,
  5 => 6,
])->delete();

        // Remove data for tables
        DB::table('tables')->whereIn('id', [
  0 => 1,
  1 => 2,
  2 => 3,
  3 => 4,
  4 => 5,
  5 => 6,
])->delete();

        // Remove data for banners
        DB::table('banners')->whereIn('id', [
  0 => 1,
  1 => 2,
  2 => 3,
])->delete();

        // Remove data for portions
        DB::table('portions')->whereIn('id', [
  0 => 1,
])->delete();

        // Remove data for menu_items
        DB::table('menu_items')->whereIn('id', [
  0 => 1,
  1 => 2,
  2 => 3,
  3 => 4,
  4 => 5,
  5 => 6,
  6 => 7,
  7 => 8,
  8 => 9,
])->delete();

        // Remove data for menu_categories
        DB::table('menu_categories')->whereIn('id', [
  0 => 1,
  1 => 2,
  2 => 3,
  3 => 4,
])->delete();

        // Remove data for users
        DB::table('users')->whereIn('id', [
  0 => 1,
  1 => 2,
  2 => 3,
])->delete();

        // Remove data for restaurants
        DB::table('restaurants')->whereIn('id', [
  0 => 1,
])->delete();
    }
};
