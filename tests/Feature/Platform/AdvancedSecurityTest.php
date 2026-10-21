<?php

namespace Tests\Feature\Platform;

use App\Models\Platform\LoginAttempt;
use App\Models\Platform\SecuritySetting;
use App\Models\Platform\UserTwoFactor;
use App\Services\Platform\Totp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreatesTenants;
use Tests\TestCase;

class AdvancedSecurityTest extends TestCase
{
    use RefreshDatabase, CreatesTenants;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function enable2fa($user): string
    {
        $secret = Totp::generateSecret();
        UserTwoFactor::create(['user_id' => $user->id, 'secret' => $secret, 'confirmed_at' => now()]);
        return $secret;
    }

    public function test_totp_matches_rfc6238_vectors(): void
    {
        $secret = Totp::base32Encode('12345678901234567890');
        $this->assertSame('287082', Totp::code($secret, 59));
        $this->assertSame('081804', Totp::code($secret, 1111111109));
        $this->assertSame('050471', Totp::code($secret, 1111111111));
        $this->assertTrue(Totp::verify($secret, Totp::code($secret, 59), 1, 59));
        $this->assertFalse(Totp::verify($secret, '000000', 1, 59));
        $this->assertSame('12345678901234567890', Totp::base32Decode($secret));
    }

    public function test_setup_confirm_and_recovery_codes(): void
    {
        $admin = $this->adminWith(['advanced_security']);
        $this->actingAs($admin)->get(route('admin.security.index'))->assertOk();
        $this->actingAs($admin)->post(route('admin.security.2fa.start'))->assertRedirect();
        $tf = UserTwoFactor::where('user_id', $admin->id)->first();
        $this->assertNull($tf->confirmed_at);
        $this->actingAs($admin)->get(route('admin.security.index'))->assertOk()->assertSee('otpauth://totp/', false)->assertSee('<svg', false);

        $this->actingAs($admin)->post(route('admin.security.2fa.confirm'), ['code' => '000000'])->assertSessionHasErrors('code');
        $this->assertNull($tf->fresh()->confirmed_at);
        $this->actingAs($admin)->post(route('admin.security.2fa.confirm'), [])->assertSessionHasErrors('code');

        $this->actingAs($admin)->post(route('admin.security.2fa.confirm'), ['code' => Totp::code($tf->secret)])->assertSessionHas('recovery_codes');
        $this->assertNotNull($tf->fresh()->confirmed_at);
        $this->assertCount(8, $tf->fresh()->recovery_codes);
        // secret is encrypted at rest
        $this->assertNotSame($tf->secret, DB::table('user_two_factors')->where('user_id', $admin->id)->value('secret'));

        $this->actingAs($admin)->delete(route('admin.security.2fa.disable'), ['password' => 'wrong'])->assertSessionHasErrors('password');
        $this->assertSame(1, UserTwoFactor::count());
        $this->actingAs($admin)->delete(route('admin.security.2fa.disable'), ['password' => 'password'])->assertRedirect();
        $this->assertSame(0, UserTwoFactor::count());
    }

    public function test_login_requires_totp_when_enabled_and_feature_granted(): void
    {
        $admin = $this->adminWith(['advanced_security']);
        $secret = $this->enable2fa($admin);

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect(route('two-factor.challenge'));
        $this->assertGuest();
        $this->get(route('two-factor.challenge'))->assertOk();

        $this->post(route('two-factor.challenge.store'), ['code' => '123456'])->assertSessionHasErrors('code');
        $this->assertGuest();
        $this->post(route('two-factor.challenge.store'), ['code' => Totp::code($secret)])->assertRedirect();
        $this->assertAuthenticatedAs($admin);

        $results = LoginAttempt::where('restaurant_id', $admin->restaurant_id)->pluck('result')->all();
        $this->assertContains('password_ok_2fa_pending', $results);
        $this->assertContains('two_factor_failed', $results);
        $this->assertContains('success', $results);
    }

    public function test_recovery_code_logs_in_once_only(): void
    {
        $admin = $this->adminWith(['advanced_security']);
        $secret = $this->enable2fa($admin);
        $tf = UserTwoFactor::first();
        $codes = app(\App\Services\Platform\LoginSecurity::class)->newRecoveryCodes($tf);

        $this->post('/login', ['email' => $admin->email, 'password' => 'password']);
        $this->post(route('two-factor.challenge.store'), ['code' => $codes[0]])->assertRedirect();
        $this->assertAuthenticatedAs($admin);
        $this->assertCount(7, $tf->fresh()->recovery_codes);

        auth()->logout();
        $this->post('/login', ['email' => $admin->email, 'password' => 'password']);
        $this->post(route('two-factor.challenge.store'), ['code' => $codes[0]])->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_2fa_not_enforced_without_feature_or_when_user_did_not_enable(): void
    {
        $noFeature = $this->adminWith([]);
        $this->enable2fa($noFeature);
        $this->post('/login', ['email' => $noFeature->email, 'password' => 'password'])->assertRedirect();
        $this->assertAuthenticatedAs($noFeature);
        auth()->logout();

        $plain = $this->adminWith(['advanced_security']);
        $this->post('/login', ['email' => $plain->email, 'password' => 'password'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($plain);
    }

    public function test_challenge_without_pending_login_redirects(): void
    {
        $this->get(route('two-factor.challenge'))->assertRedirect(route('login'));
        $this->post(route('two-factor.challenge.store'), ['code' => '123456'])->assertRedirect(route('login'));
    }

    public function test_failed_login_is_logged_with_ip_and_agent(): void
    {
        $admin = $this->adminWith(['advanced_security']);
        $this->withHeader('User-Agent', 'TestBrowser/1.0')->post('/login', ['email' => $admin->email, 'password' => 'nope']);
        $row = LoginAttempt::where('restaurant_id', $admin->restaurant_id)->first();
        $this->assertSame('failed', $row->result);
        $this->assertSame('TestBrowser/1.0', $row->user_agent);
        $this->assertNotEmpty($row->ip_address);

        $this->actingAs($admin)->get(route('admin.security.index'))->assertOk()->assertSee('TestBrowser/1.0');
    }

    public function test_attempts_of_other_restaurant_not_visible(): void
    {
        $a = $this->adminWith(['advanced_security']);
        $b = $this->adminWith(['advanced_security']);
        LoginAttempt::create(['restaurant_id' => $b->restaurant_id, 'email' => 'leak@other.test', 'result' => 'failed', 'created_at' => now()]);
        $this->actingAs($a)->get(route('admin.security.index'))->assertOk()->assertDontSee('leak@other.test');
    }

    public function test_ip_allowlist_blocks_login_and_cannot_lock_self_out(): void
    {
        $admin = $this->adminWith(['advanced_security']);
        $this->actingAs($admin)->put(route('admin.security.settings'), ['ip_allowlist' => '10.9.9.9', 'password_min_length' => 8])
            ->assertSessionHasErrors('ip_allowlist');
        $this->actingAs($admin)->put(route('admin.security.settings'), ['ip_allowlist' => 'not-an-ip', 'password_min_length' => 8])
            ->assertSessionHasErrors('ip_allowlist');
        $this->assertNull(SecuritySetting::first());

        // include current IP: ok
        $this->actingAs($admin)->put(route('admin.security.settings'), ['ip_allowlist' => "127.0.0.1\n10.9.9.9", 'password_min_length' => 8])->assertRedirect();
        $this->assertSame(['127.0.0.1', '10.9.9.9'], SecuritySetting::first()->allowedIps());
        auth()->logout();

        // restrict to a foreign IP directly in the DB: login from 127.0.0.1 is refused
        SecuritySetting::first()->update(['ip_allowlist' => '10.9.9.9']);
        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertDatabaseHas('login_attempts', ['restaurant_id' => $admin->restaurant_id, 'result' => 'blocked_ip']);
    }

    public function test_password_policy_enforced_on_password_change(): void
    {
        $admin = $this->adminWith(['advanced_security']);
        $this->actingAs($admin)->put(route('admin.security.settings'), [
            'password_min_length' => 12, 'password_require_mixed' => 1, 'password_require_number' => 1, 'password_require_symbol' => 1,
        ])->assertRedirect();

        $this->actingAs($admin)->put(route('password.update'), ['current_password' => 'password', 'password' => 'Short1!', 'password_confirmation' => 'Short1!'])
            ->assertSessionHasErrorsIn('updatePassword', 'password');
        $this->actingAs($admin)->put(route('password.update'), ['current_password' => 'password', 'password' => 'alllowercaselongpassword', 'password_confirmation' => 'alllowercaselongpassword'])
            ->assertSessionHasErrorsIn('updatePassword', 'password');
        $this->assertTrue(Hash::check('password', $admin->fresh()->password));

        $good = 'Str0ng!Passw0rd-ok';
        $this->actingAs($admin)->put(route('password.update'), ['current_password' => 'password', 'password' => $good, 'password_confirmation' => $good])
            ->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check($good, $admin->fresh()->password));
    }

    public function test_policy_validation_and_not_applied_without_feature(): void
    {
        $admin = $this->adminWith(['advanced_security']);
        $this->actingAs($admin)->put(route('admin.security.settings'), ['password_min_length' => 3])->assertSessionHasErrors('password_min_length');

        $plain = $this->adminWith([]);
        SecuritySetting::create(['restaurant_id' => $plain->restaurant_id, 'password_min_length' => 30]);
        $this->actingAs($plain)->put(route('password.update'), ['current_password' => 'password', 'password' => 'abcd1234', 'password_confirmation' => 'abcd1234'])
            ->assertSessionHasNoErrors();
    }

    public function test_sessions_list_and_logout_other_devices(): void
    {
        $admin = $this->adminWith(['advanced_security']);
        $other = $this->adminWith(['advanced_security']);
        $row = fn ($id, $uid) => ['id' => $id, 'user_id' => $uid, 'ip_address' => '1.2.3.4', 'user_agent' => 'Agent-' . $id, 'payload' => 'x', 'last_activity' => time()];
        DB::table('sessions')->insert([$row('sess-a', $admin->id), $row('sess-b', $admin->id), $row('sess-other', $other->id)]);

        $this->actingAs($admin)->get(route('admin.security.index'))->assertOk()->assertSee('Agent-sess-a')->assertDontSee('Agent-sess-other');
        $this->actingAs($admin)->delete(route('admin.security.sessions.others'))->assertRedirect();
        $this->assertSame(0, DB::table('sessions')->where('user_id', $admin->id)->whereIn('id', ['sess-a', 'sess-b'])->count());
        $this->assertSame(1, DB::table('sessions')->where('id', 'sess-other')->count());
    }

    public function test_security_screens_forbidden_without_feature(): void
    {
        $admin = $this->adminWith([]);
        $this->actingAs($admin)->get(route('admin.security.index'))->assertStatus(403);
        $this->actingAs($admin)->post(route('admin.security.2fa.start'))->assertStatus(403);
        $this->actingAs($admin)->put(route('admin.security.settings'), ['password_min_length' => 10])->assertStatus(403);
        $this->assertSame(0, UserTwoFactor::count());
    }
}
