<?php

namespace App\Services\Operations;

use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;

class CashSummary
{
    public function forDay(int $restaurantId, ?string $date): array
    {
        $day = $date ? Carbon::parse($date) : now();
        $payments = Payment::where('restaurant_id', $restaurantId)->where('status', 'paid')
            ->whereBetween('created_at', [$day->copy()->startOfDay(), $day->copy()->endOfDay()])->get();

        return [
            'date' => $day->toDateString(),
            'total' => (float) $payments->sum('amount'),
            'count' => $payments->count(),
            'by_method' => $payments->groupBy('method')->map(fn ($g) => ['count' => $g->count(), 'amount' => (float) $g->sum('amount')])->sortKeys()->all(),
            'unpaid_count' => Order::where('restaurant_id', $restaurantId)->where('payment_status', '!=', 'paid')->where('status', '!=', 'cancelled')->count(),
            'unpaid_total' => (float) Order::where('restaurant_id', $restaurantId)->where('payment_status', '!=', 'paid')->where('status', '!=', 'cancelled')->sum('total_amount'),
        ];
    }
}
