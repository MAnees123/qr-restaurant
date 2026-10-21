<?php

namespace App\Observers\Comms;

use App\Models\Reservation;
use App\Services\Comms\Notifier;

class ReservationObserver
{
    public function created(Reservation $r): void
    {
        if ($r->status === 'confirmed') {
            $this->notify($r);
        }
    }

    public function updated(Reservation $r): void
    {
        if ($r->wasChanged('status') && $r->status === 'confirmed') {
            $this->notify($r);
        }
    }

    private function notify(Reservation $r): void
    {
        try {
            $restaurant = $r->restaurant;
            if (!$restaurant) {
                return;
            }
            app(Notifier::class)->dispatch($restaurant, 'reservation_confirmed', [
                'order_number'  => '',
                'status'        => 'Confirmed',
                'total'         => '',
                'restaurant'    => $restaurant->name,
                'table'         => $r->table?->table_number,
                'customer_name' => $r->customer_name,
                'date'          => (string) $r->reservation_date,
                'time'          => (string) $r->reservation_time,
            ], ['phone' => $r->customer_phone, 'email' => $r->customer_email ?? null], 'reservation#' . $r->id);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
