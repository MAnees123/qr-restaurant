<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        // Seed data for plans
        DB::table('plans')->updateOrInsert(
            ['id' => 2],
            [
  'id' => 2,
  'name' => 'Starter',
  'price_monthly' => '29.00',
  'price_yearly' => '290.00',
  'trial_days' => 14,
  'max_branches' => 2,
  'max_users' => 10,
  'max_tables' => 50,
  'max_storage_mb' => 100,
  'features' => '["pos", "table_ordering", "kitchen_screen", "waiter_panel", "cashier_module", "coupons", "analytics", "reports", "theme_custom", "dark_mode", "qr_logo", "email_notif", "export_import", "banners"]',
  'is_active' => 1,
  'created_at' => '2026-06-13 09:36:46',
  'updated_at' => '2026-06-13 09:36:46',
]
        );

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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

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

        // Remove data for plans
        DB::table('plans')->whereIn('id', [
  0 => 2,
])->delete();
    }
};
