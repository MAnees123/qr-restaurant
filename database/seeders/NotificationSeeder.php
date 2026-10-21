<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('notifications')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $admin = User::where('email', 'admin@restaurant.com')->first();
        if (!$admin) return;

        $notifications = [
            [
                'id'              => (string) Str::uuid(),
                'type'            => 'App\Notifications\NewOrderNotification',
                'notifiable_type' => User::class,
                'notifiable_id'   => $admin->id,
                'data'            => json_encode([
                    'title'       => 'New Order Received',
                    'message'     => 'Table T-01 placed a new order for $36.00.',
                    'action_url'  => '/admin/orders',
                    'type'        => 'order',
                ]),
                'read_at'         => null,
                'created_at'      => now()->subMinutes(5),
                'updated_at'      => now()->subMinutes(5),
            ],
            [
                'id'              => (string) Str::uuid(),
                'type'            => 'App\Notifications\TableCallNotification',
                'notifiable_type' => User::class,
                'notifiable_id'   => $admin->id,
                'data'            => json_encode([
                    'title'       => 'Waiter Call Alert',
                    'message'     => 'Table VIP-1 requested waiter assistance.',
                    'action_url'  => '/admin/table-calls',
                    'type'        => 'table_call',
                ]),
                'read_at'         => null,
                'created_at'      => now()->subMinutes(12),
                'updated_at'      => now()->subMinutes(12),
            ],
            [
                'id'              => (string) Str::uuid(),
                'type'            => 'App\Notifications\PaymentNotification',
                'notifiable_type' => User::class,
                'notifiable_id'   => $admin->id,
                'data'            => json_encode([
                    'title'       => 'Payment Received',
                    'message'     => 'Payment of $84.00 received for Order #ORD-K981.',
                    'action_url'  => '/admin/orders',
                    'type'        => 'payment',
                ]),
                'read_at'         => now()->subMinutes(20),
                'created_at'      => now()->subMinutes(40),
                'updated_at'      => now()->subMinutes(20),
            ],
        ];

        foreach ($notifications as $n) {
            DB::table('notifications')->insert($n);
        }
    }
}
