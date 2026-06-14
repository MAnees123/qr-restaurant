<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces that the authenticated user's restaurant has a given feature enabled.
 * Usage in routes: ->middleware('feature:pos')
 * Super admins bypass this check entirely.
 */
class CheckFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = auth()->user();

        // Super admins bypass all feature checks
        if ($user && $user->is_super_admin) {
            return $next($request);
        }

        if (!$user) {
            return redirect()->route('login');
        }

        // Load the restaurant relationship fresh (avoids stale session cache)
        $restaurant = $user->restaurant()->first();

        if (!$restaurant) {
            abort(403, 'No restaurant context found for your account.');
        }

        // Hard block if tenant is suspended
        if ($restaurant->is_suspended) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been suspended. Please contact support.']);
        }

        // Check if subscription has expired
        if ($restaurant->subscription_ends_at
            && $restaurant->subscription_ends_at->isPast()
            && $restaurant->payment_status !== 'paid') {
            abort(402, 'Your subscription has expired. Please renew to access this feature.');
        }

        // Check granted features
        $granted = $restaurant->granted_features ?? [];

        if (!in_array($feature, $granted)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error'   => 'Feature not enabled on your plan.',
                    'feature' => $feature,
                ], 403);
            }

            // Friendly blade page instead of raw 403
            return response()->view('errors.feature-locked', [
                'feature' => $feature,
            ], 403);
        }

        return $next($request);
    }
}
