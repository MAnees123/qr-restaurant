<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\Operations\LoyaltyService;

/** Awards loyalty points once an order is paid, and refunds redeemed points when it is cancelled. */
class OrderLoyaltyObserver
{
    public function saved(Order $order): void
    {
        $service = app(LoyaltyService::class);

        if ($order->payment_status === 'paid' && $order->customer_phone) {
            $service->earnForOrder($order);
        }

        if ($order->status === 'cancelled' && $order->payment_status !== 'paid' && $order->wasChanged('status')) {
            $service->refundForOrder($order);
        }
    }
}
