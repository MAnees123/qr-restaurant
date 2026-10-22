<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Demo data for Operations modules: operations/online-order settings, delivery data on orders,
 * loyalty accounts + transactions. Re-runnable (keyed upserts only).
 */
class OperationsDemoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Restaurant::orderBy('id')->pluck('id') as $rid) {
            mt_srand($rid * 9103 + 5);
            $this->settings($rid);
            $orderPoints = $this->deliveryData($rid);
            $this->loyalty($rid, $orderPoints);
        }
    }

    private function up(string $table, array $keys, array $vals): int
    {
        $now = now();
        $row = DB::table($table)->where($keys)->first();
        if ($row) {
            DB::table($table)->where('id', $row->id)->update($vals + ['updated_at' => $now]);
            return $row->id;
        }
        return DB::table($table)->insertGetId(array_merge(['created_at' => $now, 'updated_at' => $now], $keys, $vals));
    }

    private function settings(int $rid): void
    {
        $this->up('operations_settings', ['restaurant_id' => $rid], [
            'online_ordering_open' => true,
            'delivery_fee' => 150 + ($rid - 1) * 50,           // Rs
            'delivery_min_order' => 800,                         // Rs
            'loyalty_enabled' => true,
            'loyalty_points_per_currency' => 0.1,                // 1 point per Rs 10
            'loyalty_point_value' => 1,                          // Rs 1 per point
            'loyalty_min_redeem' => 100,
        ]);
    }

    /**
     * Turns some existing orders into delivery / pickup orders and links them to customers.
     * Returns [customerIndex => [[order_id, order_number, points, created_at], ...]].
     */
    private function deliveryData(int $rid): array
    {
        $orders = DB::table('orders')->where('restaurant_id', $rid)->orderBy('id')->get();
        if ($orders->isEmpty()) {
            return [];
        }
        $pool = StaffDemoSeeder::customerPool($rid);
        $customerIds = DB::table('customers')->where('restaurant_id', $rid)->pluck('id', 'phone');
        $areas = ['House 14, Street 6, F-7/2, Islamabad', 'Flat 3B, Gulberg III, Lahore', 'Plot 22, Block 5, Clifton, Karachi',
            'House 88, Sector G-11/1, Islamabad', 'Apartment 9, DHA Phase 5, Lahore', 'House 5, Bahadurabad, Karachi'];
        $drivers = [['Rashid Mehmood', '03211234567'], ['Shahid Afridi', '03334455667'], ['Waqas Ahmed', '03459876543']];

        $finished = $orders->whereIn('status', ['served'])->values()->take(16);
        $live = $orders->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])->values()->take(3);
        $result = [];

        foreach ($finished->concat($live) as $n => $o) {
            $isLive = $n >= $finished->count();
            $ci = $n % count($pool);
            $c = $pool[$ci];
            $type = $isLive ? 'delivery' : ($n % 4 === 3 ? 'pickup' : 'delivery');
            $upd = [
                'order_type' => $type,
                'customer_id' => $customerIds[$c['phone']] ?? null,
                'customer_name' => $c['name'], 'customer_phone' => $c['phone'], 'customer_email' => $c['email'],
            ];
            if ($type === 'delivery') {
                $d = $drivers[$n % count($drivers)];
                $status = $isLive ? ['pending', 'assigned', 'out'][$n - $finished->count()] : 'delivered';
                $upd += [
                    'delivery_address' => $areas[$n % count($areas)], 'delivery_fee' => 150,
                    'delivery_status' => $status,
                    'driver_name' => $status === 'pending' ? null : $d[0],
                    'driver_phone' => $status === 'pending' ? null : $d[1],
                    'delivered_at' => $status === 'delivered' ? \Illuminate\Support\Carbon::parse($o->created_at)->addMinutes(rand(35, 60)) : null,
                ];
            }
            DB::table('orders')->where('id', $o->id)->update($upd);
            if (!$isLive && $o->payment_status === 'paid') {
                $result[$ci][] = [$o->id, $o->order_number, (int) floor($o->total_amount * 0.1), $o->created_at];
            }
        }
        return $result;
    }

    private function loyalty(int $rid, array $orderPoints): void
    {
        $pool = StaffDemoSeeder::customerPool($rid);
        foreach (array_slice($pool, 0, 16, true) as $i => $c) {
            $txns = [
                ['earn', mt_rand(60, 140), 'Welcome bonus', null, now()->subDays(58 - $i)],
                ['earn', mt_rand(20, 90), 'Dine-in visit', null, now()->subDays(40 - $i)],
            ];
            if ($i % 3 === 0) {
                $txns[] = ['adjust', 25, 'Birthday goodwill points', null, now()->subDays(22)];
            }
            foreach ($orderPoints[$i] ?? [] as [$oid, $onum, $pts, $at]) {
                $txns[] = ['earn', max($pts, 1), "Earned on order {$onum}", $oid, \Illuminate\Support\Carbon::parse($at)];
            }
            $earned = array_sum(array_map(fn ($t) => $t[1], $txns));
            if ($i % 2 === 0 && $earned > 120) {
                $txns[] = ['redeem', -100, 'Redeemed on dine-in bill (Rs 100 off)', null, now()->subDays(9)];
            }
            $balance = array_sum(array_map(fn ($t) => $t[1], $txns));
            $lifetime = array_sum(array_map(fn ($t) => $t[0] === 'earn' || $t[0] === 'adjust' ? $t[1] : 0, $txns));
            $accountId = $this->up('loyalty_accounts', ['restaurant_id' => $rid, 'phone' => $c['phone']], [
                'name' => $c['name'], 'balance' => $balance, 'lifetime_earned' => $lifetime,
                'created_at' => now()->subDays(58 - $i),
            ]);
            foreach ($txns as [$type, $pts, $note, $oid, $at]) {
                $this->up('loyalty_transactions', ['loyalty_account_id' => $accountId, 'type' => $type, 'note' => $note], [
                    'restaurant_id' => $rid, 'order_id' => $oid, 'points' => $pts, 'created_at' => $at,
                ]);
            }
        }
    }
}
