<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Usage: CheckPermission::class.':manage_menu' (registered by class reference, no alias). */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$codes): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        foreach ($codes as $code) {
            if ($user->hasPermission($code)) {
                return $next($request);
            }
        }

        abort(403, 'You do not have permission to do this.');
    }
}
