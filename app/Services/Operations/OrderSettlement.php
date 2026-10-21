<?php

namespace App\Services\Operations;

use App\Models\Order;
use App\Models\Payment;

class OrderSettlement
{
    /** Record a full cash payment (e.g. cash on delivery / paid at pickup) if the order is not paid yet. */
    public function markCashPaid(Order $order, string $note): void
    {
        if ($order->payment_status === 'paid') {
            return;
        }
        Payment::create([
            'restaurant_id' => $order->restaurant_id, 'order_id' => $order->id, 'amount' => $order->total_amount,
            'method' => 'cash', 'status' => 'paid', 'notes' => $note,
        ]);
        $order->update(['payment_status' => 'paid', 'payment_method' => 'cash']);
    }
}
