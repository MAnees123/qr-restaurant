<?php

namespace App\Providers;

use App\Models\User;
use App\Services\Platform\LoginSecurity;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

/** Boot hooks (model observers, listeners, view composers) for the Platform feature modules. */
class PlatformFeaturesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // advanced_security: log failed password attempts for known users of restaurants with the feature.
        Event::listen(Failed::class, function (Failed $e) {
            if ($e->user instanceof User) {
                app(LoginSecurity::class)->record($e->user, $e->user->email, 'failed', request());
            }
        });
    }
}
