<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Restricts a staff panel to users whose staff_role matches (waiter | cashier). Usage: OperationsStaffRole::class.':waiter' */
class OperationsStaffRole
{
    public function handle(Request $request, Closure $next, string $staffRole): Response
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'staff' && $user->staff_role === $staffRole, 403, 'This panel is not available for your staff role.');

        return $next($request);
    }
}
