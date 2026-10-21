<?php

namespace App\Observers\Comms;

use App\Models\Comms\CommsSetting;
use App\Models\Order;
use App\Services\Comms\EscPos;
use App\Services\Comms\Notifier;

class OrderObserver
{
    public function created(Order $order): void
    {
        $this->notify($order, 'order_placed');
        $this->autoPrint($order);
    }

    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            $this->notify($order, 'order_status_changed');
        }
    }

    private function notify(Order $order, string $event): void
    {
        try {
            $restaurant = $order->restaurant;
            if (!$restaurant) {
                return;
            }
            app(Notifier::class)->dispatch($restaurant, $event, [
                'order_number'  => $order->order_number,
                'status'        => ucfirst((string) $order->status),
                'total'         => number_format((float) $order->total_amount, 2),
                'restaurant'    => $restaurant->name,
                'table'         => $order->table?->table_number,
                'customer_name' => $order->customer_name ?: 'Customer',
                'date'          => now()->toDateString(),
                'time'          => now()->format('H:i'),
            ], ['phone' => $order->customer_phone ?? null, 'email' => $order->customer_email ?? null], $order->order_number);
        } catch (\Throwable $e) {
            report($e); // never break order flow
        }
    }

    /** Sends a kitchen ticket to a network ESC/POS printer if one is configured and auto print is on. */
    private function autoPrint(Order $order): void
    {
        try {
            $restaurant = $order->restaurant;
            if (!$restaurant || !$restaurant->hasFeature('printer')) {
                return;
            }
            $s = CommsSetting::read($restaurant->id, 'printer');
            if (!$s['auto_print'] || empty($s['printer_host'])) {
                return;
            }
            $order->load('orderItems.menuItem', 'table');
            $err = EscPos::send($s['printer_host'], (int) $s['printer_port'], EscPos::ticket($order, $s, 'kitchen'));
            app(Notifier::class)->log($restaurant, 'printer', 'order_placed', 'tcp', $s['printer_host'] . ':' . $s['printer_port'], null,
                'Kitchen ticket ' . $order->order_number, $err ? 'failed' : 'sent', $err, $order->order_number);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
