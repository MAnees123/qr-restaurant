<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // advanced_security hook: IP allowlist + 2FA challenge (no-op unless the feature is granted)
        if ($blocked = app(\App\Services\Platform\LoginSecurity::class)->afterPasswordCheck($request)) {
            return $blocked;
        }

        // Super Admin goes directly to the Super Admin dashboard
        if (auth()->user()->is_super_admin) {
            return redirect()->route('superadmin.dashboard');
        }

        // Operations module: waiter / cashier staff land on their own panel
        $user = auth()->user();
        if ($user->role === 'staff' && $user->staff_role === 'waiter' && $user->restaurant?->hasFeature('waiter_panel')) {
            return redirect()->route('staff.waiter.index');
        }
        if ($user->role === 'staff' && $user->staff_role === 'cashier' && $user->restaurant?->hasFeature('cashier_module')) {
            return redirect()->route('staff.cashier.index');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
