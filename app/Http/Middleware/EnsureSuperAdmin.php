<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user || !$user->is_super_admin) {
            abort(403, 'Super Admin access required');
        }
        return $next($request);
    }
}
?>
