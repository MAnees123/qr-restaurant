<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TableCall;
use App\Models\Table;

class TableCallSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TableCall::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $tables = Table::where('restaurant_id', 1)->get();
        if ($tables->isEmpty()) return;

        // Active waiter calls
        TableCall::create([
            'restaurant_id' => 1,
            'table_id'      => $tables->firstWhere('table_number', 'T-01')?->id ?? $tables->first()->id,
            'status'        => 'pending',
            'created_at'    => now()->subMinutes(3),
            'updated_at'    => now()->subMinutes(3),
        ]);

        TableCall::create([
            'restaurant_id' => 1,
            'table_id'      => $tables->firstWhere('table_number', 'VIP-1')?->id ?? $tables->first()->id,
            'status'        => 'accepted',
            'created_at'    => now()->subMinutes(7),
            'updated_at'    => now()->subMinutes(2),
        ]);

        TableCall::create([
            'restaurant_id' => 1,
            'table_id'      => $tables->firstWhere('table_number', 'Roof-01')?->id ?? $tables->first()->id,
            'status'        => 'completed',
            'created_at'    => now()->subMinutes(25),
            'updated_at'    => now()->subMinutes(15),
        ]);
    }
}
