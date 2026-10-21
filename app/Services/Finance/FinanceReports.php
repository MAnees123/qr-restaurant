<?php

namespace App\Services\Finance;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/** Read-only aggregate queries over orders, items, expenses and purchases. Always scoped by restaurant. */
class FinanceReports
{
    public function __construct(public int $restaurantId, public string $from, public string $to) {}

    public static function range(?string $from, ?string $to): array
    {
        $to = $to ?: Carbon::today()->toDateString();
        $from = $from ?: Carbon::parse($to)->startOfMonth()->toDateString();
        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }
        return [$from, $to];
    }

    private function paidOrders()
    {
        return DB::table('orders')
            ->where('orders.restaurant_id', $this->restaurantId)
            ->where('orders.payment_status', 'paid')
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('orders.created_at', [$this->from . ' 00:00:00', $this->to . ' 23:59:59']);
    }

    public function revenue(): float
    {
        return round((float) $this->paidOrders()->sum('orders.total_amount'), 2);
    }

    public function orderCount(): int
    {
        return $this->paidOrders()->count();
    }

    public function expensesTotal(): float
    {
        return round((float) DB::table('expenses')->where('restaurant_id', $this->restaurantId)
            ->whereBetween('expense_date', [$this->from, $this->to])->sum('amount'), 2);
    }

    /** Purchases = received (not cancelled/only ordered) purchase orders. */
    public function purchasesTotal(): float
    {
        return round((float) DB::table('purchase_orders')->where('restaurant_id', $this->restaurantId)
            ->where('status', 'received')->whereBetween('order_date', [$this->from, $this->to])->sum('total_amount'), 2);
    }

    public function salesByDay()
    {
        return $this->paidOrders()->selectRaw('DATE(orders.created_at) as day, COUNT(*) as orders, SUM(orders.total_amount) as revenue')
            ->groupBy('day')->orderBy('day')->get();
    }

    public function salesByPaymentMethod()
    {
        return $this->paidOrders()->selectRaw("COALESCE(orders.payment_method, 'unknown') as method, COUNT(*) as orders, SUM(orders.total_amount) as revenue")
            ->groupBy('method')->orderByDesc('revenue')->get();
    }

    private function items()
    {
        return $this->paidOrders()->join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('menu_items', 'menu_items.id', '=', 'order_items.menu_item_id');
    }

    public function salesByItem(int $limit = 0)
    {
        $q = $this->items()->selectRaw("COALESCE(menu_items.name, 'Deleted item') as item, SUM(order_items.quantity) as qty, SUM(order_items.subtotal) as revenue")
            ->groupBy('order_items.menu_item_id', 'menu_items.name')->orderByDesc('qty');
        return $limit ? $q->limit($limit)->get() : $q->get();
    }

    public function salesByCategory()
    {
        return $this->items()->leftJoin('menu_categories', 'menu_categories.id', '=', 'menu_items.menu_category_id')
            ->selectRaw("COALESCE(menu_categories.name, 'Uncategorised') as category, SUM(order_items.quantity) as qty, SUM(order_items.subtotal) as revenue")
            ->groupBy('menu_categories.id', 'menu_categories.name')->orderByDesc('revenue')->get();
    }

    public function expenseVsRevenueByDay()
    {
        $rev = $this->salesByDay()->pluck('revenue', 'day');
        $exp = DB::table('expenses')->where('restaurant_id', $this->restaurantId)
            ->whereBetween('expense_date', [$this->from, $this->to])
            ->selectRaw('expense_date as day, SUM(amount) as total')->groupBy('day')->pluck('total', 'day');
        $days = $rev->keys()->merge($exp->keys())->map(fn ($d) => (string) $d)->unique()->sort()->values();
        return $days->map(fn ($d) => (object) [
            'day' => $d, 'revenue' => (float) ($rev[$d] ?? 0), 'expenses' => (float) ($exp[$d] ?? 0),
            'net' => (float) ($rev[$d] ?? 0) - (float) ($exp[$d] ?? 0),
        ]);
    }
}
