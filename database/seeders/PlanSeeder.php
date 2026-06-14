<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Services\FeatureRegistry;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Plan::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Plan::create([
            'name'          => 'Free',
            'price_monthly' => 0,
            'price_yearly'  => 0,
            'trial_days'    => 0,
            'max_branches'  => 1,
            'max_users'     => 3,
            'max_tables'    => 10,
            'features'      => FeatureRegistry::freeFeatures(),
            'is_active'     => true,
        ]);

        Plan::create([
            'name'          => 'Starter',
            'price_monthly' => 29,
            'price_yearly'  => 290,
            'trial_days'    => 14,
            'max_branches'  => 2,
            'max_users'     => 10,
            'max_tables'    => 50,
            'features'      => FeatureRegistry::starterFeatures(),
            'is_active'     => true,
        ]);

        Plan::create([
            'name'          => 'Pro',
            'price_monthly' => 79,
            'price_yearly'  => 790,
            'trial_days'    => 14,
            'max_branches'  => 10,
            'max_users'     => 50,
            'max_tables'    => 200,
            'features'      => FeatureRegistry::allCodes(),
            'is_active'     => true,
        ]);

        Plan::create([
            'name'          => 'Enterprise',
            'price_monthly' => 199,
            'price_yearly'  => 1990,
            'trial_days'    => 30,
            'max_branches'  => -1, // unlimited
            'max_users'     => -1,
            'max_tables'    => -1,
            'features'      => FeatureRegistry::allCodes(),
            'is_active'     => true,
        ]);
    }
}
