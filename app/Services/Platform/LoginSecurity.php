<?php

namespace App\Services\Platform;

use App\Models\Platform\LoginAttempt;
use App\Models\Platform\SecuritySetting;
use App\Models\Platform\UserTwoFactor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** Everything advanced_security adds to the login flow. Active only when the restaurant was granted the feature. */
class LoginSecurity
{
    public function active(?User $user): bool
    {
        return $user && ! $user->is_super_admin && $user->restaurant_id && $user->restaurant?->hasFeature('advanced_security');
    }

    public function record(?User $user, string $email, string $result, Request $request): void
    {
        $rid = $user?->restaurant_id;
        if (! $rid || ! $user->restaurant?->hasFeature('advanced_security')) return;
        LoginAttempt::create([
            'restaurant_id' => $rid, 'user_id' => $user->id, 'email' => $email,
            'ip_address' => $request->ip(), 'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
            'result' => $result, 'created_at' => now(),
        ]);
    }

    public function ipAllowed(User $user, ?string $ip): bool
    {
        $list = SecuritySetting::forRestaurant($user->restaurant_id)->allowedIps();
        return ! $list || in_array($ip, $list, true);
    }

    public function twoFactorFor(User $user): ?UserTwoFactor
    {
        $tf = UserTwoFactor::where('user_id', $user->id)->first();
        return $tf && $tf->isEnabled() ? $tf : null;
    }

    /**
     * Hook called right after the password was verified. Returns a redirect response when the login must not
     * complete (IP blocked, or 2FA challenge required); null to continue the normal flow.
     */
    public function afterPasswordCheck(Request $request)
    {
        $user = Auth::guard('web')->user();
        if (! $this->active($user)) return null;

        if (! $this->ipAllowed($user, $request->ip())) {
            $this->record($user, $user->email, 'blocked_ip', $request);
            Auth::guard('web')->logout();
            return redirect()->route('login')->withErrors(['email' => 'Sign-in from your network address is not allowed for this restaurant.']);
        }

        if ($this->twoFactorFor($user)) {
            $id = $user->id;
            $this->record($user, $user->email, 'password_ok_2fa_pending', $request);
            Auth::guard('web')->logout();
            $request->session()->put('platform.2fa', ['id' => $id, 'remember' => $request->boolean('remember'), 'at' => now()->timestamp, 'tries' => 0]);
            return redirect()->route('two-factor.challenge');
        }

        $this->record($user, $user->email, 'success', $request);
        return null;
    }

    /** @return string[] plain recovery codes */
    public function newRecoveryCodes(UserTwoFactor $tf): array
    {
        $plain = [];
        for ($i = 0; $i < 8; $i++) {
            $plain[] = strtolower(Str::random(5) . '-' . Str::random(5));
        }
        $tf->update(['recovery_codes' => array_map(fn ($c) => Hash::make($c), $plain)]);
        return $plain;
    }

    public function useRecoveryCode(UserTwoFactor $tf, string $code): bool
    {
        $code = strtolower(trim($code));
        $codes = $tf->recovery_codes ?? [];
        foreach ($codes as $i => $hash) {
            if (Hash::check($code, $hash)) {
                unset($codes[$i]);
                $tf->update(['recovery_codes' => array_values($codes)]);
                return true;
            }
        }
        return false;
    }
}
