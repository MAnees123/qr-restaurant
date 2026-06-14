<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            abort(403, 'Unauthorized action.');
        }

        // Check for tenant suspension
        if (auth()->user()->restaurant && auth()->user()->restaurant->is_suspended) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Your restaurant account has been suspended by the administrator.']);
        }

        return $next($request);
    }
}
