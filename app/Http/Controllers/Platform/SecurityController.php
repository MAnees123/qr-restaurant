<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Platform\LoginAttempt;
use App\Models\Platform\SecuritySetting;
use App\Models\Platform\UserTwoFactor;
use App\Services\Platform\LoginSecurity;
use App\Services\Platform\Totp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SecurityController extends Controller
{
    public function index(Request $request, LoginSecurity $sec)
    {
        $user = auth()->user();
        $rid = $user->restaurant_id;
        $tf = UserTwoFactor::where('user_id', $user->id)->first();

        $setup = null;
        if ($tf && ! $tf->isEnabled()) {
            $uri = Totp::uri($tf->secret, $user->email, $user->restaurant->name ?? config('app.name'));
            $setup = ['secret' => $tf->secret, 'uri' => $uri, 'svg' => (string) QrCode::format('svg')->size(180)->generate($uri)];
        }

        $sessions = DB::table('sessions')->where('user_id', $user->id)->orderByDesc('last_activity')->get()
            ->map(fn ($s) => (object) [
                'id' => $s->id, 'ip' => $s->ip_address, 'agent' => $s->user_agent,
                'last' => \Carbon\Carbon::createFromTimestamp($s->last_activity), 'current' => $s->id === $request->session()->getId(),
            ]);

        return view('platform.security.index', [
            'enabled'  => (bool) $tf?->isEnabled(),
            'setup'    => $setup,
            'recovery' => session('recovery_codes'),
            'codesLeft'=> $tf?->isEnabled() ? count($tf->recovery_codes ?? []) : 0,
            'settings' => SecuritySetting::forRestaurant($rid),
            'sessions' => $sessions,
            'attempts' => LoginAttempt::where('restaurant_id', $rid)->latest('id')->limit(50)->get(),
            'ip'       => $request->ip(),
        ]);
    }

    public function start()
    {
        $user = auth()->user();
        $tf = UserTwoFactor::where('user_id', $user->id)->first();
        if ($tf?->isEnabled()) {
            return redirect()->route('admin.security.index');
        }
        UserTwoFactor::updateOrCreate(['user_id' => $user->id], ['secret' => Totp::generateSecret(), 'recovery_codes' => null, 'confirmed_at' => null]);
        return redirect()->route('admin.security.index');
    }

    public function confirm(Request $request, LoginSecurity $sec)
    {
        $request->validate(['code' => 'required|string|max:10']);
        $tf = UserTwoFactor::where('user_id', auth()->id())->first();
        if (! $tf || $tf->isEnabled()) {
            return redirect()->route('admin.security.index');
        }
        if (! Totp::verify($tf->secret, $request->input('code'))) {
            return back()->withErrors(['code' => 'That code is not valid. Check your authenticator app clock and try again.']);
        }
        $tf->update(['confirmed_at' => now()]);
        $codes = $sec->newRecoveryCodes($tf);
        return redirect()->route('admin.security.index')->with('success', 'Two-factor authentication enabled.')->with('recovery_codes', $codes);
    }

    public function regenerate(Request $request, LoginSecurity $sec)
    {
        $this->requirePassword($request);
        $tf = UserTwoFactor::where('user_id', auth()->id())->first();
        abort_unless($tf?->isEnabled(), 404);
        return redirect()->route('admin.security.index')->with('success', 'New recovery codes generated. Old codes no longer work.')
            ->with('recovery_codes', $sec->newRecoveryCodes($tf));
    }

    public function disable(Request $request)
    {
        $this->requirePassword($request);
        UserTwoFactor::where('user_id', auth()->id())->delete();
        return redirect()->route('admin.security.index')->with('success', 'Two-factor authentication disabled.');
    }

    private function requirePassword(Request $request): void
    {
        $request->validate(['password' => 'required|string']);
        if (! Hash::check($request->input('password'), auth()->user()->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages(['password' => 'The password is incorrect.']);
        }
    }

    public function logoutOthers(Request $request)
    {
        $n = DB::table('sessions')->where('user_id', auth()->id())->where('id', '!=', $request->session()->getId())->delete();
        return redirect()->route('admin.security.index')->with('success', "Logged out of $n other session(s).");
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'ip_allowlist'    => ['nullable', 'string', 'max:1000', function ($attr, $value, $fail) {
                foreach (preg_split('/[\s,]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY) as $ip) {
                    if (! filter_var($ip, FILTER_VALIDATE_IP)) $fail("'$ip' is not a valid IP address.");
                }
            }],
            'password_min_length' => 'required|integer|min:6|max:64',
        ]);
        $ips = array_values(array_filter(preg_split('/[\s,]+/', (string) ($data['ip_allowlist'] ?? ''))));
        if ($ips && ! in_array($request->ip(), $ips, true)) {
            return back()->withInput()->withErrors(['ip_allowlist' => 'Your current IP (' . $request->ip() . ') must be on the list, or you would lock yourself out.']);
        }
        SecuritySetting::updateOrCreate(['restaurant_id' => auth()->user()->restaurant_id], [
            'ip_allowlist' => $ips ? implode("\n", $ips) : null,
            'password_min_length' => $data['password_min_length'],
            'password_require_mixed' => $request->boolean('password_require_mixed'),
            'password_require_number' => $request->boolean('password_require_number'),
            'password_require_symbol' => $request->boolean('password_require_symbol'),
        ]);
        return redirect()->route('admin.security.index')->with('success', 'Security settings saved.');
    }
}
