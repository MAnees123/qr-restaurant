<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Seeding SaaS Plans & Features...');
        $this->call(PlanSeeder::class);
        $this->call(FeatureSeeder::class);

        $this->command->info('Seeding Roles & Permissions...');
        $this->call(RolePermissionSeeder::class);

        $this->command->info('Seeding Restaurants & Tenants...');
        $this->call(RestaurantSeeder::class);

        $this->command->info('Seeding Users & Staff Accounts...');
        $this->call(UserSeeder::class);

        $this->command->info('Seeding Branches & Portions...');
        $this->call(BranchAndPortionSeeder::class);

        $this->command->info('Seeding Tables & QR Codes...');
        $this->call(TableSeeder::class);

        $this->command->info('Seeding Menu Categories & Items...');
        $this->call(MenuSeeder::class);

        $this->command->info('Seeding Hot Product Banners & Deals...');
        $this->call(BannerSeeder::class);

        $this->command->info('Seeding Coupons & Discounts...');
        $this->call(DiscountSeeder::class);

        $this->command->info('Seeding Orders, Order Items & Payments...');
        $this->call(OrderSeeder::class);

        $this->command->info('Seeding Table Reservations...');
        $this->call(ReservationSeeder::class);

        $this->command->info('Seeding Waiter Table Calls...');
        $this->call(TableCallSeeder::class);

        $this->command->info('Seeding System Notifications...');
        $this->call(NotificationSeeder::class);

        $this->command->info('Seeding Subscriptions & Invoices...');
        $this->call(SubscriptionBillingSeeder::class);

        $this->command->info('Seeding Product Analytics & Hot Item Caches...');
        $this->call(AnalyticsSeeder::class);

        $this->command->info('Seeding Activity Logs...');
        $this->call(ActivityLogSeeder::class);

        $this->command->info('✓ Database successfully populated with complete client demo data!');
    }
}
