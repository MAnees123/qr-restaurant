<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Portion;

class BranchAndPortionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Portion::truncate();
        Branch::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Branches for Restaurant 1
        $mainBranch = Branch::create([
            'id'            => 1,
            'restaurant_id' => 1,
            'name'          => 'Main Downtown Flagship',
            'is_active'     => true,
        ]);

        $expressBranch = Branch::create([
            'id'            => 2,
            'restaurant_id' => 1,
            'name'          => 'Uptown Rooftop & Terrace',
            'is_active'     => true,
        ]);

        // Branch for Restaurant 2
        Branch::create([
            'id'            => 3,
            'restaurant_id' => 2,
            'name'          => 'Bistro North Hub',
            'is_active'     => true,
        ]);

        // Portions / Dining Sections
        $portions = [
            ['id' => 1, 'restaurant_id' => 1, 'branch_id' => 1, 'name' => 'Main Dining Hall', 'is_active' => true],
            ['id' => 2, 'restaurant_id' => 1, 'branch_id' => 1, 'name' => 'Executive VIP Lounge', 'is_active' => true],
            ['id' => 3, 'restaurant_id' => 1, 'branch_id' => 1, 'name' => 'Open-Air Patio & Garden', 'is_active' => true],
            ['id' => 4, 'restaurant_id' => 1, 'branch_id' => 2, 'name' => 'Rooftop Deck', 'is_active' => true],
            ['id' => 5, 'restaurant_id' => 1, 'branch_id' => 2, 'name' => 'Sunset Terrace', 'is_active' => true],
        ];

        foreach ($portions as $p) {
            Portion::create($p);
        }
    }
}
