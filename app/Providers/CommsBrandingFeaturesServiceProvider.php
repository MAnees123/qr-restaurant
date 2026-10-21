<?php

namespace App\Providers;

use App\Http\Middleware\Comms\InjectFeatureHead;
use App\Models\Order;
use App\Models\Reservation;
use App\Observers\Comms\OrderObserver;
use App\Observers\Comms\ReservationObserver;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

/** Boot hooks (model observers, listeners, view composers) for the CommsBranding feature modules. */
class CommsBrandingFeaturesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(Kernel $kernel): void
    {
        // Each observer re-checks the granted feature per restaurant before doing anything.
        Order::observe(OrderObserver::class);
        Reservation::observe(ReservationObserver::class);

        // Branding / dark mode head snippet, injected without editing any theme layout.
        // (global middleware: the Kernel re-syncs the 'web' group after boot, which would drop a group push)
        $kernel->pushMiddleware(InjectFeatureHead::class);
    }
}
