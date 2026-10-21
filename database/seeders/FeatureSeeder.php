<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Services\FeatureRegistry;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('restaurant_feature')->truncate();
        DB::table('features')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $featuresByCategory = FeatureRegistry::all();

        foreach ($featuresByCategory as $category => $items) {
            foreach ($items as $item) {
                DB::table('features')->updateOrInsert(
                    ['code' => $item['code']],
                    [
                        'label'         => $item['label'],
                        'category'      => $category,
                        'default_limit' => null,
                        'meta'          => json_encode(['description' => $item['label'] . ' functionality']),
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]
                );
            }
        }
    }
}
