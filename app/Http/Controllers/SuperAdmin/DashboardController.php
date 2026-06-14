<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the Super Admin dashboard with global statistics.
     * Uses safe helpers so pages don't crash if an optional table/column
     * hasn't been migrated yet.
     */
    public function index()
    {
        // Cache for 5 minutes so heavy queries don't hammer the DB on every page load
        $stats = cache()->remember('superadmin.stats', 300, function () {
            return [
                'total_restaurants'     => $this->safeCount('restaurants'),
                'active_restaurants'    => $this->safeCount('restaurants', ['is_active' => true]),
                'suspended_restaurants' => $this->safeCount('restaurants', ['is_suspended' => true]),
                'expired_subscriptions' => $this->safeCountTable('subscriptions', ['status' => 'expired']),
                'monthly_revenue'       => $this->safeRevenue(),
                'active_orders'         => $this->safeCountWhere('orders', 'status', '!=', 'completed'),
                'qr_orders'             => $this->safeCountTable('orders', ['payment_method' => 'qr']),
                'inventory_items'       => $this->safeCountTable('inventory_items'),
                'active_staff'          => $this->safeCountNotNull('users', 'restaurant_id'),
                'total_branches'        => $this->safeCountTable('branches'),
            ];
        });

        return view('superadmin.dashboard', compact('stats'));
    }

    /*──────── Safe DB helpers (never crash if table/column is absent) ────────*/

    private function safeCount(string $table, array $where = []): int
    {
        if (!Schema::hasTable($table)) return 0;
        $q = DB::table($table);
        foreach ($where as $col => $val) {
            if (Schema::hasColumn($table, $col)) {
                $q->where($col, $val);
            }
        }
        return (int) $q->count();
    }

    private function safeCountTable(string $table, array $where = []): int
    {
        if (!Schema::hasTable($table)) return 0;
        $q = DB::table($table);
        foreach ($where as $col => $val) {
            if (Schema::hasColumn($table, $col)) {
                $q->where($col, $val);
            }
        }
        return (int) $q->count();
    }

    private function safeCountWhere(string $table, string $col, string $op, $val): int
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $col)) return 0;
        return (int) DB::table($table)->where($col, $op, $val)->count();
    }

    private function safeCountNotNull(string $table, string $col): int
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $col)) return 0;
        return (int) DB::table($table)->whereNotNull($col)->count();
    }

    private function safeRevenue(): float
    {
        if (!Schema::hasTable('billing_invoices') || !Schema::hasColumn('billing_invoices', 'amount')) {
            return 0.0;
        }
        return (float) DB::table('billing_invoices')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
    }
}
