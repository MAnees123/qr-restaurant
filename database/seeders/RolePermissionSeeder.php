<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_permission')->truncate();
        DB::table('restaurant_role')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $permissions = [
            ['name' => 'manage_orders', 'description' => 'Can view, update and manage orders'],
            ['name' => 'manage_menu', 'description' => 'Can create and edit menu categories and items'],
            ['name' => 'manage_tables', 'description' => 'Can configure tables, portions, and QR codes'],
            ['name' => 'manage_kitchen', 'description' => 'Can access kitchen dashboard and update prep status'],
            ['name' => 'manage_reservations', 'description' => 'Can view and approve table reservations'],
            ['name' => 'manage_discounts', 'description' => 'Can create coupons and discount offers'],
            ['name' => 'manage_banners', 'description' => 'Can create and edit promotional banners'],
            ['name' => 'view_analytics', 'description' => 'Can view sales and product performance analytics'],
            ['name' => 'manage_settings', 'description' => 'Can configure restaurant settings and themes'],
            ['name' => 'manage_billing', 'description' => 'Can view subscription and billing history'],
        ];

        $permMap = [];
        foreach ($permissions as $p) {
            $id = DB::table('permissions')->insertGetId([
                'name'        => $p['name'],
                'description' => $p['description'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            $permMap[$p['name']] = $id;
        }

        $roles = [
            [
                'id'          => 1,
                'name'        => 'Super Admin',
                'description' => 'Platform super administrator with unrestricted access',
                'tenant_id'   => null,
                'permissions' => array_keys($permMap),
            ],
            [
                'id'          => 2,
                'name'        => 'Restaurant Admin',
                'description' => 'Full administrative access to the restaurant operations',
                'tenant_id'   => 1,
                'permissions' => array_keys($permMap),
            ],
            [
                'id'          => 3,
                'name'        => 'Kitchen Chef',
                'description' => 'Access to live kitchen display, tickets and prep status',
                'tenant_id'   => 1,
                'permissions' => ['manage_kitchen', 'manage_orders'],
            ],
            [
                'id'          => 4,
                'name'        => 'Waiter',
                'description' => 'Table service, order taking and table call responses',
                'tenant_id'   => 1,
                'permissions' => ['manage_orders', 'manage_tables', 'manage_reservations'],
            ],
            [
                'id'          => 5,
                'name'        => 'Cashier',
                'description' => 'Handles customer billing, payments and receipts',
                'tenant_id'   => 1,
                'permissions' => ['manage_orders', 'manage_discounts', 'view_analytics'],
            ],
        ];

        foreach ($roles as $r) {
            DB::table('roles')->insert([
                'id'          => $r['id'],
                'name'        => $r['name'],
                'description' => $r['description'],
                'tenant_id'   => $r['tenant_id'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            foreach ($r['permissions'] as $permName) {
                if (isset($permMap[$permName])) {
                    DB::table('role_permission')->insert([
                        'role_id'       => $r['id'],
                        'permission_id' => $permMap[$permName],
                    ]);
                }
            }
        }
    }
}
