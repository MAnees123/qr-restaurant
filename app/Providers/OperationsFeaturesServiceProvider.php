<?php

namespace App\Providers;

use App\Models\Order;
use App\Observers\OrderLoyaltyObserver;
use Illuminate\Support\ServiceProvider;

/** Boot hooks (model observers, listeners, view composers) for the Operations feature modules. */
class OperationsFeaturesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Order::observe(OrderLoyaltyObserver::class);
    }
}
