<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = [
            // ─── Super Admins ──────────────────────────────────────────
            [
                'restaurant_id'     => null,
                'name'              => 'Super Administrator',
                'email'             => 'maneesimee@gmail.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('123456789'),
                'role'              => 'admin',
                'is_super_admin'    => true,
                'theme'             => 'dark',
                'avatar'            => null,
                'is_suspended'      => false,
            ],
            [
                'restaurant_id'     => null,
                'name'              => 'Global Super Admin',
                'email'             => 'superadmin@restaurant.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'is_super_admin'    => true,
                'theme'             => 'dark',
                'avatar'            => null,
                'is_suspended'      => false,
            ],

            // ─── Restaurant #1 Staff (Primary Demo Restaurant) ─────────
            [
                'restaurant_id'     => 1,
                'name'              => 'Adeel Munir (Manager)',
                'email'             => 'admin@restaurant.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'is_super_admin'    => false,
                'theme'             => 'dark',
                'avatar'            => null,
                'is_suspended'      => false,
            ],
            [
                'restaurant_id'     => 1,
                'name'              => 'Admin Demo',
                'email'             => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('1234'),
                'role'              => 'admin',
                'is_super_admin'    => false,
                'theme'             => 'dark',
                'avatar'            => null,
                'is_suspended'      => false,
            ],
            [
                'restaurant_id'     => 1,
                'name'              => 'Chef Marco Rossi',
                'email'             => 'kitchen@restaurant.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => 'kitchen',
                'is_super_admin'    => false,
                'theme'             => 'dark',
                'avatar'            => null,
                'is_suspended'      => false,
            ],
            [
                'restaurant_id'     => 1,
                'name'              => 'Sarah Jenkins',
                'email'             => 'waiter@restaurant.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => 'staff',
                'is_super_admin'    => false,
                'theme'             => 'default',
                'avatar'            => null,
                'is_suspended'      => false,
            ],
            [
                'restaurant_id'     => 1,
                'name'              => 'Michael Scott (Cashier)',
                'email'             => 'cashier@restaurant.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => 'staff',
                'is_super_admin'    => false,
                'theme'             => 'default',
                'avatar'            => null,
                'is_suspended'      => false,
            ],

            // ─── Restaurant #2 & #3 Admins ─────────────────────────────
            [
                'restaurant_id'     => 2,
                'name'              => 'Elena Vance',
                'email'             => 'bistro_admin@restaurant.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'is_super_admin'    => false,
                'theme'             => 'default',
                'avatar'            => null,
                'is_suspended'      => false,
            ],
            [
                'restaurant_id'     => 3,
                'name'              => 'Tariq Mehmood',
                'email'             => 'spice_admin@restaurant.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'is_super_admin'    => false,
                'theme'             => 'default',
                'avatar'            => null,
                'is_suspended'      => false,
            ],
        ];

        foreach ($users as $u) {
            User::create($u);
        }
    }
}
