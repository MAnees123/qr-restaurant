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

        // Super admins have unrestricted access
        if ($user && $user->is_super_admin) {
            return $next($request);
        }

        // Check restaurant has the feature granted
        if (!$user || !$user->restaurant) {
            abort(403, 'No restaurant context.');
        }

        $restaurant = $user->restaurant;

        // Hard block if tenant is suspended
        if ($restaurant->is_suspended) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been suspended. Please contact support.']);
        }

        // Check if subscription has expired
        if ($restaurant->subscription_ends_at && $restaurant->subscription_ends_at->isPast()
            && $restaurant->payment_status !== 'paid') {
            abort(402, 'Your subscription has expired. Please renew to access this feature.');
        }

        $granted = $restaurant->granted_features ?? [];
        if (!in_array($feature, $granted)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Feature not enabled on your plan.'], 403);
            }
            abort(403, "The '{$feature}' module is not enabled on your current plan. Please contact your administrator.");
        }

        return $next($request);
    }
}
