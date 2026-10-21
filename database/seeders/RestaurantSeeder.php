<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Restaurant;
use App\Services\FeatureRegistry;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('restaurant_role')->truncate();
        DB::table('restaurant_feature')->truncate();
        DB::table('restaurants')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $allFeatures = FeatureRegistry::allCodes();

        // 1. Primary Demo Restaurant
        $primary = Restaurant::create([
            'id'                   => 1,
            'name'                 => 'Cafe Munch Gourmet',
            'owner_name'           => 'Adeel Munir',
            'domain'               => 'cafemunch.demo',
            'cuisine_type'         => 'Continental & Fusion',
            'theme'                => 'dark',
            'logo'                 => 'restaurant_logos/logo.png',
            'address'              => '42 Boulevard Avenue, Culinary District',
            'country'              => 'United States',
            'city'                 => 'New York',
            'timezone'             => 'America/New_York',
            'currency'             => 'USD',
            'phone'                => '+1 (555) 382-9104',
            'is_active'            => true,
            'is_suspended'         => false,
            'subscription_plan'    => 'enterprise',
            'plan_id'              => 4,
            'granted_features'     => $allFeatures,
            'subscription_ends_at' => now()->addYear(),
            'billing_cycle'        => 'yearly',
            'payment_status'       => 'paid',
            'trial_ends_at'        => null,
            'max_branches'         => 10,
            'max_users'            => 50,
            'max_tables'           => 100,
            'max_storage_mb'       => 2048,
        ]);

        // 2. Second Restaurant for SuperAdmin multi-tenant demo
        $bistro = Restaurant::create([
            'id'                   => 2,
            'name'                 => 'The Artisan Bistro',
            'owner_name'           => 'Elena Vance',
            'domain'               => 'artisanbistro.demo',
            'cuisine_type'         => 'Italian & French',
            'theme'                => 'default',
            'logo'                 => 'restaurant_logos/logo.png',
            'address'              => '78 Pinecrest Way, Downtown',
            'country'              => 'United States',
            'city'                 => 'Chicago',
            'timezone'             => 'America/Chicago',
            'currency'             => 'USD',
            'phone'                => '+1 (555) 492-8812',
            'is_active'            => true,
            'is_suspended'         => false,
            'subscription_plan'    => 'pro',
            'plan_id'              => 3,
            'granted_features'     => $allFeatures,
            'subscription_ends_at' => now()->addMonths(6),
            'billing_cycle'        => 'monthly',
            'payment_status'       => 'paid',
            'trial_ends_at'        => null,
            'max_branches'         => 5,
            'max_users'            => 25,
            'max_tables'           => 60,
            'max_storage_mb'       => 500,
        ]);

        // 3. Third Restaurant
        $spice = Restaurant::create([
            'id'                   => 3,
            'name'                 => 'Urban Spice Grill',
            'owner_name'           => 'Tariq Mehmood',
            'domain'               => 'urbanspice.demo',
            'cuisine_type'         => 'Traditional BBQ',
            'theme'                => 'default',
            'logo'                 => 'restaurant_logos/logo.png',
            'address'              => '105 Market Square',
            'country'              => 'United States',
            'city'                 => 'Dallas',
            'timezone'             => 'America/Chicago',
            'currency'             => 'USD',
            'phone'                => '+1 (555) 714-3029',
            'is_active'            => true,
            'is_suspended'         => false,
            'subscription_plan'    => 'starter',
            'plan_id'              => 2,
            'granted_features'     => FeatureRegistry::starterFeatures(),
            'subscription_ends_at' => now()->addMonth(),
            'billing_cycle'        => 'monthly',
            'payment_status'       => 'paid',
            'trial_ends_at'        => null,
            'max_branches'         => 2,
            'max_users'            => 10,
            'max_tables'           => 30,
            'max_storage_mb'       => 100,
        ]);

        // Seed restaurant_feature pivot
        $featureRecords = DB::table('features')->get();
        foreach ([$primary, $bistro, $spice] as $rest) {
            foreach ($featureRecords as $f) {
                $enabled = in_array($f->code, $rest->granted_features ?? []);
                DB::table('restaurant_feature')->insert([
                    'restaurant_id' => $rest->id,
                    'feature_id'    => $f->id,
                    'enabled'       => $enabled,
                    'limit_value'   => null,
                ]);
            }

            // Assign roles
            foreach ([2, 3, 4, 5] as $roleId) {
                DB::table('restaurant_role')->insert([
                    'restaurant_id' => $rest->id,
                    'role_id'       => $roleId,
                ]);
            }
        }
    }
}
