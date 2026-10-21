<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ActivityLog::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $admin = User::where('email', 'admin@restaurant.com')->first();
        $superadmin = User::where('email', 'maneesimee@gmail.com')->first();

        $logs = [
            [
                'restaurant_id' => 1,
                'user_id'       => $admin?->id ?? 1,
                'action'        => 'login',
                'description'   => 'Admin logged into dashboard',
                'ip_address'    => '127.0.0.1',
                'user_agent'    => 'Mozilla/5.0 (X11; Linux x86_64)',
                'created_at'    => now()->subMinutes(15),
                'updated_at'    => now()->subMinutes(15),
            ],
            [
                'restaurant_id' => 1,
                'user_id'       => $admin?->id ?? 1,
                'action'        => 'menu_update',
                'description'   => 'Updated prices and images for Chef Grills category',
                'ip_address'    => '127.0.0.1',
                'user_agent'    => 'Mozilla/5.0 (X11; Linux x86_64)',
                'created_at'    => now()->subHours(2),
                'updated_at'    => now()->subHours(2),
            ],
            [
                'restaurant_id' => 1,
                'user_id'       => $admin?->id ?? 1,
                'action'        => 'theme_changed',
                'description'   => 'Switched restaurant interface theme to dark mode',
                'ip_address'    => '127.0.0.1',
                'user_agent'    => 'Mozilla/5.0 (X11; Linux x86_64)',
                'created_at'    => now()->subHours(5),
                'updated_at'    => now()->subHours(5),
            ],
            [
                'restaurant_id' => null,
                'user_id'       => $superadmin?->id ?? 1,
                'action'        => 'plan_created',
                'description'   => 'SuperAdmin created enterprise subscription plan tier',
                'ip_address'    => '192.168.1.100',
                'user_agent'    => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at'    => now()->subDays(1),
                'updated_at'    => now()->subDays(1),
            ],
            [
                'restaurant_id' => 1,
                'user_id'       => $admin?->id ?? 1,
                'action'        => 'banner_published',
                'description'   => 'Published Weekend Mega Feast 40% OFF promotional banner',
                'ip_address'    => '127.0.0.1',
                'user_agent'    => 'Mozilla/5.0 (X11; Linux x86_64)',
                'created_at'    => now()->subDays(2),
                'updated_at'    => now()->subDays(2),
            ],
        ];

        foreach ($logs as $l) {
            ActivityLog::create($l);
        }
    }
}
