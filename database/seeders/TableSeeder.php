<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Table;
use App\Models\QrCode;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        QrCode::truncate();
        Table::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $tablesData = [
            // Branch 1 - Main Dining Hall
            ['table_number' => 'T-01', 'capacity' => 2, 'branch_id' => 1, 'portion_id' => 1, 'status' => 'occupied'],
            ['table_number' => 'T-02', 'capacity' => 4, 'branch_id' => 1, 'portion_id' => 1, 'status' => 'occupied'],
            ['table_number' => 'T-03', 'capacity' => 4, 'branch_id' => 1, 'portion_id' => 1, 'status' => 'free'],
            ['table_number' => 'T-04', 'capacity' => 6, 'branch_id' => 1, 'portion_id' => 1, 'status' => 'reserved'],

            // Branch 1 - VIP Lounge
            ['table_number' => 'VIP-1', 'capacity' => 8,  'branch_id' => 1, 'portion_id' => 2, 'status' => 'occupied'],
            ['table_number' => 'VIP-2', 'capacity' => 10, 'branch_id' => 1, 'portion_id' => 2, 'status' => 'free'],
            ['table_number' => 'VIP-3', 'capacity' => 6,  'branch_id' => 1, 'portion_id' => 2, 'status' => 'reserved'],

            // Branch 1 - Patio
            ['table_number' => 'Patio-01', 'capacity' => 4, 'branch_id' => 1, 'portion_id' => 3, 'status' => 'free'],
            ['table_number' => 'Patio-02', 'capacity' => 4, 'branch_id' => 1, 'portion_id' => 3, 'status' => 'occupied'],
            ['table_number' => 'Patio-03', 'capacity' => 2, 'branch_id' => 1, 'portion_id' => 3, 'status' => 'free'],

            // Branch 2 - Rooftop Deck
            ['table_number' => 'Roof-01', 'capacity' => 4, 'branch_id' => 2, 'portion_id' => 4, 'status' => 'occupied'],
            ['table_number' => 'Roof-02', 'capacity' => 6, 'branch_id' => 2, 'portion_id' => 4, 'status' => 'free'],
        ];

        foreach ($tablesData as $idx => $tData) {
            $uuid = (string) Str::uuid();
            $token = Str::random(32);

            // Allow first table to have a friendly demo alias code
            $table = Table::create([
                'id'              => $idx + 1,
                'restaurant_id'   => 1,
                'branch_id'       => $tData['branch_id'],
                'portion_id'      => $tData['portion_id'],
                'table_number'    => $tData['table_number'],
                'capacity'        => $tData['capacity'],
                'is_active'       => true,
                'status'          => $tData['status'],
                'secure_token'    => $token,
                'auto_release_at' => null,
            ]);

            QrCode::create([
                'table_id'      => $table->id,
                'code'          => $uuid,
                'qr_image_path' => null,
            ]);
        }
    }
}
