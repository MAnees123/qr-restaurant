<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Support\ServiceProvider;

/** Boot hooks (model observers, listeners, view composers) for the Staff feature modules. */
class StaffFeaturesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // customer_mgmt: orders that carry a customer phone are auto-linked to a directory entry.
        Order::saving(function (Order $order) {
            $phone = trim((string) $order->getAttribute('customer_phone'));

            if ($phone === '' || $order->getAttribute('customer_id') || !$order->restaurant_id) {
                return;
            }

            if (!Restaurant::find($order->restaurant_id)?->hasFeature('customer_mgmt')) {
                return;
            }

            $customer = Customer::firstOrCreate(
                ['restaurant_id' => $order->restaurant_id, 'phone' => $phone],
                ['name' => trim((string) $order->getAttribute('customer_name')) ?: 'Guest ' . $phone]
            );

            $order->setAttribute('customer_id', $customer->id);
        });
    }
}
