<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Services\FeatureRegistry;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Plan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Plan::create([
            'id'            => 1,
            'name'          => 'Free',
            'price_monthly' => 0.00,
            'price_yearly'  => 0.00,
            'trial_days'    => 0,
            'max_branches'  => 1,
            'max_users'     => 3,
            'max_tables'    => 10,
            'max_storage_mb'=> 50,
            'features'      => FeatureRegistry::freeFeatures(),
            'is_active'     => true,
        ]);

        Plan::create([
            'id'            => 2,
            'name'          => 'Starter',
            'price_monthly' => 29.00,
            'price_yearly'  => 290.00,
            'trial_days'    => 14,
            'max_branches'  => 2,
            'max_users'     => 10,
            'max_tables'    => 50,
            'max_storage_mb'=> 100,
            'features'      => FeatureRegistry::starterFeatures(),
            'is_active'     => true,
        ]);

        Plan::create([
            'id'            => 3,
            'name'          => 'Pro',
            'price_monthly' => 79.00,
            'price_yearly'  => 790.00,
            'trial_days'    => 14,
            'max_branches'  => 10,
            'max_users'     => 50,
            'max_tables'    => 200,
            'max_storage_mb'=> 500,
            'features'      => FeatureRegistry::allCodes(),
            'is_active'     => true,
        ]);

        Plan::create([
            'id'            => 4,
            'name'          => 'Enterprise',
            'price_monthly' => 199.00,
            'price_yearly'  => 1990.00,
            'trial_days'    => 30,
            'max_branches'  => 999, // unlimited
            'max_users'     => 999,
            'max_tables'    => 999,
            'max_storage_mb'=> 2048,
            'features'      => FeatureRegistry::allCodes(),
            'is_active'     => true,
        ]);
    }
}
