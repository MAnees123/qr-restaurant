<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Demo data for Staff modules: roles & permissions, attendance, salary profiles,
 * payroll runs + payslips and customers. Re-runnable (keyed upserts only).
 */
class StaffDemoSeeder extends Seeder
{
    /** Shared pool of demo customers (name, email slug). Phones are derived per restaurant. */
    public static function customerPool(int $rid): array
    {
        $names = [
            'Ahmed Raza', 'Fatima Noor', 'Bilal Hussain', 'Ayesha Siddiqui', 'Hamza Sheikh', 'Sana Malik',
            'Usman Ghani', 'Zainab Tariq', 'Imran Qureshi', 'Hira Anwar', 'Kamran Butt', 'Mariam Iqbal',
            'Faisal Mahmood', 'Nadia Pervaiz', 'Saad Chaudhry', 'Rabia Khan', 'Danish Ali', 'Amna Javed',
            'Talha Rehman', 'Komal Yasin',
        ];
        $tags = ['VIP', 'Regular', 'Family', 'Delivery', 'Birthday club', 'Corporate', ''];
        $pool = [];
        foreach ($names as $i => $name) {
            $pool[] = [
                'name'  => $name,
                'phone' => '0300' . sprintf('%07d', 1234500 + $i * 7919 + $rid * 111),
                'email' => strtolower(str_replace(' ', '.', $name)) . '@example.pk',
                'tags'  => $tags[$i % count($tags)],
            ];
        }
        return $pool;
    }

    public function run(): void
    {
        $password = Hash::make('password');

        foreach (Restaurant::orderBy('id')->pluck('id') as $rid) {
            mt_srand($rid * 7001 + 11);
            $this->roles($rid);
            $this->extraUsers($rid, $password);
            $this->attendance($rid);
            $this->payroll($rid);
            $this->customers($rid);
        }
    }

    private function up(string $table, array $keys, array $vals, bool $ts = true): int
    {
        $now = now();
        $row = DB::table($table)->where($keys)->first();
        if ($row) {
            DB::table($table)->where('id', $row->id)->update($vals + ($ts ? ['updated_at' => $now] : []));
            return $row->id;
        }
        return DB::table($table)->insertGetId(
            array_merge($ts ? ['created_at' => $now, 'updated_at' => $now] : [], $keys, $vals)
        );
    }

    private function roles(int $rid): void
    {
        $defs = [
            'Floor Manager' => ['Runs the floor, orders and tables', ['view_orders', 'manage_orders', 'manage_tables', 'view_menu', 'view_reports', 'manage_customers', 'manage_attendance', 'manage_discounts']],
            'Head Cashier'  => ['Handles billing and cash drawer', ['view_orders', 'manage_orders', 'view_menu', 'manage_customers', 'manage_discounts']],
            'Waiter'        => ['Takes orders and serves tables', ['view_orders', 'manage_orders', 'view_menu']],
            'Kitchen Lead'  => ['Controls menu availability and tickets', ['view_orders', 'view_menu', 'manage_menu']],
            'Accountant'    => ['Reports and payroll access', ['view_reports', 'manage_payroll', 'view_orders']],
            'HR Officer'    => ['Attendance and payroll administration', ['manage_attendance', 'manage_payroll']],
        ];
        foreach ($defs as $name => [$desc, $perms]) {
            $this->up('staff_roles', ['restaurant_id' => $rid, 'name' => $name], [
                'description' => $desc,
                'permissions' => json_encode($perms),
            ]);
        }
        $waiter = DB::table('staff_roles')->where(['restaurant_id' => $rid, 'name' => 'Waiter'])->value('id');
        $cashier = DB::table('staff_roles')->where(['restaurant_id' => $rid, 'name' => 'Head Cashier'])->value('id');
        $kitchen = DB::table('staff_roles')->where(['restaurant_id' => $rid, 'name' => 'Kitchen Lead'])->value('id');
        $map = [
            ['waiter@restaurant.com', 'waiter', $waiter], ['cashier@restaurant.com', 'cashier', $cashier],
            ['kitchen@restaurant.com', null, $kitchen],
        ];
        foreach ($map as [$email, $staffRole, $roleId]) {
            DB::table('users')->where('email', $email)->where('restaurant_id', $rid)
                ->update(['staff_role' => $staffRole, 'custom_role_id' => $roleId]);
        }
    }

    private function extraUsers(int $rid, string $password): void
    {
        $waiter = DB::table('staff_roles')->where(['restaurant_id' => $rid, 'name' => 'Waiter'])->value('id');
        $cashier = DB::table('staff_roles')->where(['restaurant_id' => $rid, 'name' => 'Head Cashier'])->value('id');
        $kitchen = DB::table('staff_roles')->where(['restaurant_id' => $rid, 'name' => 'Kitchen Lead'])->value('id');
        $staff = [
            ["Hassan Ali",    "hassan.r{$rid}@restaurant.com",  'staff',   'waiter',  $waiter],
            ["Noman Ashraf",  "noman.r{$rid}@restaurant.com",   'staff',   'waiter',  $waiter],
            ["Saima Bukhari", "saima.r{$rid}@restaurant.com",   'staff',   'cashier', $cashier],
            ["Chef Rizwan",   "rizwan.r{$rid}@restaurant.com",  'kitchen', null,      $kitchen],
        ];
        foreach ($staff as [$name, $email, $role, $staffRole, $roleId]) {
            $exists = DB::table('users')->where('email', $email)->exists();
            if (!$exists) {
                DB::table('users')->insert([
                    'restaurant_id' => $rid, 'name' => $name, 'email' => $email, 'email_verified_at' => now(),
                    'password' => $password, 'role' => $role, 'is_super_admin' => false, 'theme' => 'default',
                    'is_suspended' => false, 'staff_role' => $staffRole, 'custom_role_id' => $roleId,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }

    private function attendance(int $rid): void
    {
        $this->up('attendance_settings', ['restaurant_id' => $rid], [
            'shift_start' => '10:00', 'late_grace_minutes' => 15, 'standard_hours' => 9,
        ]);
        $users = DB::table('users')->where('restaurant_id', $rid)->where('is_super_admin', false)->orderBy('id')->pluck('id');
        $notes = ['Traffic delay', 'Family emergency', 'Sick leave', 'Covered double shift', null, null, null];
        foreach ($users as $uid) {
            for ($d = 59; $d >= 0; $d--) {
                $date = now()->subDays($d)->startOfDay();
                if ($date->isSunday() && ($uid % 2 === 0 || $date->day % 2 === 0)) {
                    continue; // weekly off, rotated
                }
                $roll = mt_rand(1, 100);
                $status = $roll <= 76 ? 'present' : ($roll <= 88 ? 'late' : ($roll <= 93 ? 'half_day' : ($roll <= 97 ? 'leave' : 'absent')));
                $in = $out = null;
                if (in_array($status, ['present', 'late', 'half_day'], true)) {
                    $in = (clone $date)->setTime(10, 0)->addMinutes($status === 'late' ? mt_rand(20, 70) : mt_rand(-10, 10));
                    $out = (clone $in)->addMinutes($status === 'half_day' ? mt_rand(240, 280) : mt_rand(520, 600));
                    if ($date->isToday() && $out->isFuture()) {
                        $out = null;
                    }
                }
                $this->up('attendance_records', ['user_id' => $uid, 'work_date' => $date->toDateString()], [
                    'restaurant_id' => $rid,
                    'clock_in' => $in, 'clock_out' => $out, 'status' => $status,
                    'note' => in_array($status, ['late', 'leave', 'absent'], true) ? $notes[mt_rand(0, count($notes) - 1)] : null,
                    'created_at' => $date->copy()->setTime(10, 5),
                ]);
            }
        }
    }

    private function payroll(int $rid): void
    {
        $users = DB::table('users')->where('restaurant_id', $rid)->where('is_super_admin', false)->orderBy('id')->get();
        $generator = $users->firstWhere('role', 'admin')->id ?? $users->first()->id;

        $profiles = [];
        foreach ($users as $u) {
            $base = match ($u->role) {
                'admin'   => 150000,
                'kitchen' => 85000 + ($u->id % 3) * 5000,
                default   => 45000 + ($u->id % 4) * 5000,
            };
            $row = [
                'restaurant_id' => $rid,
                'base_salary' => $base, 'allowances' => round($base * 0.1, 2),
                'deductions' => round($base * 0.02, 2), 'overtime_rate' => round($base / 26 / 8 * 1.5, 2),
                'working_days' => 26,
            ];
            $this->up('salary_profiles', ['user_id' => $u->id], $row);
            $profiles[$u->id] = $row;
        }

        foreach ([2, 1, 0] as $back) { // two paid months and the current month pending
            $month = now()->subMonths($back)->format('Y-m');
            $runId = $this->up('payroll_runs', ['restaurant_id' => $rid, 'month' => $month], [
                'generated_by' => $generator,
                'created_at' => now()->subMonths($back)->endOfMonth()->min(now())->subHours(2),
            ]);
            foreach ($profiles as $uid => $p) {
                $absent = mt_rand(0, 3);
                $present = $p['working_days'] - $absent;
                $otHours = mt_rand(0, 14);
                $otPay = round($otHours * $p['overtime_rate'], 2);
                $absDed = round($p['base_salary'] / $p['working_days'] * $absent, 2);
                $net = round($p['base_salary'] + $p['allowances'] - $p['deductions'] + $otPay - $absDed, 2);
                $paid = $back > 0;
                $this->up('payslips', ['payroll_run_id' => $runId, 'user_id' => $uid], [
                    'restaurant_id' => $rid, 'days_present' => $present, 'days_absent' => $absent,
                    'overtime_hours' => $otHours, 'base_salary' => $p['base_salary'], 'allowances' => $p['allowances'],
                    'deductions' => $p['deductions'], 'overtime_pay' => $otPay, 'absence_deduction' => $absDed,
                    'net_pay' => $net, 'status' => $paid ? 'paid' : 'pending',
                    'paid_at' => $paid ? now()->subMonths($back)->endOfMonth()->addDays(2) : null,
                ]);
            }
        }
    }

    private function customers(int $rid): void
    {
        $notes = ['Prefers corner table', 'Allergic to nuts', 'Likes extra spicy', 'Usually orders for family of five', null, null];
        foreach (self::customerPool($rid) as $i => $c) {
            $this->up('customers', ['restaurant_id' => $rid, 'phone' => $c['phone']], [
                'name' => $c['name'], 'email' => $c['email'], 'tags' => $c['tags'] ?: null,
                'notes' => $notes[$i % count($notes)],
                'created_at' => now()->subDays(58 - $i * 2),
            ]);
        }
    }
}
