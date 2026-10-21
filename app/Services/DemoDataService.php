<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Copies the demo dataset of the template restaurant (the one the seeders fill)
 * into a single target restaurant, re-mapping every foreign key and regenerating
 * unique values so nothing is shared with, or duplicated across, other tenants.
 */
class DemoDataService
{
    /** Restaurant that holds the demo dataset created by DatabaseSeeder. */
    public const SOURCE_RESTAURANT_ID = 1;

    /** Tables whose rows would make a restaurant "already have data". */
    private const DATA_TABLES = ['tables', 'menu_categories', 'menu_items', 'orders'];

    public function hasData(Restaurant $restaurant): bool
    {
        foreach (self::DATA_TABLES as $table) {
            if (DB::table($table)->where('restaurant_id', $restaurant->id)->exists()) {
                return true;
            }
        }

        return false;
    }

    /** @return array<string,int> number of rows created per section */
    public function seed(Restaurant $restaurant): array
    {
        $src = self::SOURCE_RESTAURANT_ID;

        if ($restaurant->id === $src) {
            throw new RuntimeException('This is the demo template restaurant; it already holds the demo data.');
        }
        if ($this->hasData($restaurant)) {
            throw new RuntimeException('Demo data already exists for this restaurant (it already has tables, menu or orders).');
        }
        if (!DB::table('menu_items')->where('restaurant_id', $src)->exists()) {
            throw new RuntimeException('Template demo data not found. Run `php artisan db:seed` once to create it.');
        }

        return DB::transaction(function () use ($restaurant, $src) {
            $rid = $restaurant->id;
            $now = now();
            $count = [];

            // Copy rows of $table belonging to the template restaurant; $map receives
            // old id => new id, $override(row) adjusts each row before insert.
            $clone = function (string $table, callable $override, ?array &$map = null, ?string $name = null) use ($src, $rid, &$count) {
                $map = [];
                $rows = DB::table($table)->where('restaurant_id', $src)->orderBy('id')->get();
                foreach ($rows as $row) {
                    $new = (array) $row;
                    $oldId = $new['id'];
                    unset($new['id']);
                    $new['restaurant_id'] = $rid;
                    $new = $override($new);
                    $map[$oldId] = DB::table($table)->insertGetId($new);
                }
                $count[$name ?? $table] = count($map);
            };

            // Branches/portions may already exist (e.g. created by the plan or by hand): reuse by name.
            $branches = [];
            foreach (DB::table('branches')->where('restaurant_id', $src)->orderBy('id')->get() as $row) {
                $new = (array) $row;
                $oldId = $new['id'];
                unset($new['id']);
                $new['restaurant_id'] = $rid;
                $branches[$oldId] = DB::table('branches')->where('restaurant_id', $rid)->where('name', $new['name'])->value('id')
                    ?? DB::table('branches')->insertGetId($new);
            }
            $count['branches'] = count($branches);

            // Portions have no restaurant-wide uniqueness, but belong to the branches above.
            $portions = [];
            foreach (DB::table('portions')->where('restaurant_id', $src)->orderBy('id')->get() as $row) {
                $new = (array) $row;
                $oldId = $new['id'];
                unset($new['id']);
                $new['restaurant_id'] = $rid;
                $new['branch_id'] = $branches[$new['branch_id']] ?? null;
                $portions[$oldId] = DB::table('portions')->where('branch_id', $new['branch_id'])->where('name', $new['name'])->value('id')
                    ?? DB::table('portions')->insertGetId($new);
            }
            $count['portions'] = count($portions);

            $clone('tables', function ($r) use ($branches, $portions) {
                $r['branch_id'] = $branches[$r['branch_id']] ?? null;
                $r['portion_id'] = $portions[$r['portion_id']] ?? null;
                $r['secure_token'] = Str::random(32);
                $r['status'] = 'free';
                $r['auto_release_at'] = null;
                return $r;
            }, $tables);

            $clone('menu_categories', fn ($r) => $r, $categories);
            $clone('menu_items', function ($r) use ($categories) {
                $r['menu_category_id'] = $categories[$r['menu_category_id']] ?? null;
                return $r;
            }, $items);

            $clone('banners', fn ($r) => $r, $banners);
            $clone('discounts', function ($r) use ($rid) {
                $r['code'] = $r['code'] . '-R' . $rid; // codes are unique across the platform
                return $r;
            }, $discounts);

            $clone('orders', function ($r) use ($tables) {
                $r['table_id'] = $tables[$r['table_id']] ?? null;
                $r['order_number'] = 'ORD-' . strtoupper(Str::random(6));
                $r['guest_token'] = Str::random(32);
                return $r;
            }, $orders);

            $count['order_items'] = 0;
            foreach (DB::table('order_items')->whereIn('order_id', array_keys($orders))->orderBy('id')->get() as $row) {
                $new = (array) $row;
                unset($new['id']);
                $new['order_id'] = $orders[$new['order_id']];
                $new['menu_item_id'] = $items[$new['menu_item_id']] ?? null;
                if ($new['menu_item_id'] === null) {
                    continue;
                }
                DB::table('order_items')->insert($new);
                $count['order_items']++;
            }

            $clone('payments', function ($r) use ($orders) {
                $r['order_id'] = $orders[$r['order_id']] ?? null;
                $r['transaction_id'] = $r['transaction_id'] ? 'TXN-' . strtoupper(Str::random(10)) : null;
                return $r;
            });
            DB::table('payments')->where('restaurant_id', $rid)->whereNull('order_id')->delete();

            $clone('reservations', function ($r) use ($tables) {
                $r['table_id'] = $tables[$r['table_id']] ?? null;
                return $r;
            });
            $clone('table_calls', function ($r) use ($tables) {
                $r['table_id'] = $tables[$r['table_id']] ?? null;
                return $r;
            });
            $clone('product_analytics', function ($r) use ($items) {
                $r['menu_item_id'] = $items[$r['menu_item_id']] ?? null;
                return $r;
            });
            $clone('hot_item_caches', function ($r) use ($items) {
                $r['menu_item_id'] = $items[$r['menu_item_id']] ?? null;
                return $r;
            });
            $clone('activity_logs', function ($r) {
                $r['user_id'] = null;
                return $r;
            });

            // Kitchen staff so the live kitchen screen can be demonstrated.
            $kitchenEmail = "kitchen{$rid}@restaurant.com";
            $kitchenCreated = false;
            if (!DB::table('users')->where('email', $kitchenEmail)->exists()) {
                DB::table('users')->insert([
                    'restaurant_id'     => $rid,
                    'name'              => 'Demo Kitchen Chef',
                    'email'             => $kitchenEmail,
                    'email_verified_at' => $now,
                    'password'          => Hash::make('password'),
                    'role'              => 'kitchen',
                    'is_super_admin'    => 0,
                    'is_suspended'      => 0,
                    'theme'             => 'default',
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]);
                $kitchenCreated = true;
            }
            $count['kitchen_staff'] = $kitchenCreated ? 1 : 0;

            // Sample notifications for the restaurant's admin.
            $admin = DB::table('users')->where('restaurant_id', $rid)->where('role', 'admin')->first();
            $count['notifications'] = 0;
            if ($admin) {
                $templates = DB::table('notifications')
                    ->where('notifiable_type', 'App\\Models\\User')
                    ->whereIn('notifiable_id', DB::table('users')->where('restaurant_id', $src)->where('role', 'admin')->pluck('id'))
                    ->get();
                foreach ($templates as $n) {
                    $new = (array) $n;
                    $new['id'] = (string) Str::uuid();
                    $new['notifiable_id'] = $admin->id;
                    DB::table('notifications')->insert($new);
                    $count['notifications']++;
                }
            }

            return $count;
        });
    }
}
