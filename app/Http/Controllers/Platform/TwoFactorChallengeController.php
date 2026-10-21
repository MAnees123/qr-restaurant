<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Platform\LoginSecurity;
use App\Services\Platform\Totp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorChallengeController extends Controller
{
    private function pending(Request $request): ?array
    {
        $p = $request->session()->get('platform.2fa');
        if (! $p || now()->timestamp - $p['at'] > 600) {
            $request->session()->forget('platform.2fa');
            return null;
        }
        return $p;
    }

    public function create(Request $request)
    {
        if (! $this->pending($request)) {
            return redirect()->route('login');
        }
        return view('platform.security.challenge');
    }

    public function store(Request $request, LoginSecurity $sec)
    {
        $p = $this->pending($request);
        if (! $p) {
            return redirect()->route('login')->withErrors(['email' => 'Your sign-in expired. Please log in again.']);
        }
        $request->validate(['code' => 'required|string|max:32']);

        $user = User::find($p['id']);
        $tf = $user ? $sec->twoFactorFor($user) : null;
        if (! $tf) {
            $request->session()->forget('platform.2fa');
            return redirect()->route('login');
        }

        $code = (string) $request->input('code');
        $ok = Totp::verify($tf->secret, $code) || $sec->useRecoveryCode($tf, $code);
        if (! $ok) {
            $p['tries']++;
            $sec->record($user, $user->email, 'two_factor_failed', $request);
            if ($p['tries'] >= 5) {
                $request->session()->forget('platform.2fa');
                return redirect()->route('login')->withErrors(['email' => 'Too many incorrect codes. Please log in again.']);
            }
            $request->session()->put('platform.2fa', $p);
            return back()->withErrors(['code' => 'The code is incorrect.']);
        }

        $request->session()->forget('platform.2fa');
        Auth::guard('web')->login($user, $p['remember']);
        $request->session()->regenerate();
        $sec->record($user, $user->email, 'success', $request);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
