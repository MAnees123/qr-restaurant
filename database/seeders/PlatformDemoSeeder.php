<?php

namespace Database\Seeders;

use App\Models\Platform\AiSetting;
use App\Models\Platform\ApiToken;
use App\Models\Restaurant;
use App\Services\FeatureRegistry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Demo data for Platform modules: API tokens (hash only), security settings, login attempts,
 * PWA settings, AI settings. Also makes sure demo restaurants 1 and 2 hold every feature code
 * (additive only; never removes existing grants). Backups are file based (storage/app/backups), no table.
 */
class PlatformDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->grantAllFeatures();

        foreach (Restaurant::orderBy('id')->get() as $r) {
            mt_srand($r->id * 3301 + 9);
            $this->tokens($r);
            $this->security($r);
            $this->loginAttempts($r);
            $this->pwaAndAi($r);
        }
    }

    private function up(string $table, array $keys, array $vals, bool $ts = true): int
    {
        $now = now();
        $row = DB::table($table)->where($keys)->first();
        if ($row) {
            DB::table($table)->where('id', $row->id)->update($vals + ($ts ? ['updated_at' => $now] : []));
            return $row->id;
        }
        return DB::table($table)->insertGetId(array_merge($ts ? ['created_at' => $now, 'updated_at' => $now] : [], $keys, $vals));
    }

    private function grantAllFeatures(): void
    {
        $codes = FeatureRegistry::allCodes();
        foreach (Restaurant::whereIn('id', [1, 2])->get() as $r) {
            $r->granted_features = array_values(array_unique(array_merge($r->granted_features ?? [], $codes)));
            $r->save();
            foreach (DB::table('features')->whereIn('code', $codes)->get() as $f) {
                DB::table('restaurant_feature')->updateOrInsert(
                    ['restaurant_id' => $r->id, 'feature_id' => $f->id],
                    ['enabled' => true]
                );
            }
        }
    }

    private function tokens(Restaurant $r): void
    {
        $adminId = DB::table('users')->where('restaurant_id', $r->id)->where('role', 'admin')->orderBy('id')->value('id');
        $defs = [
            ['Mobile App (Android)', 'read,write', 2, null], ['Accounting Sync', 'read', 5, null],
            ['Website Menu Widget', 'read', 12, null], ['Old POS Integration', 'read,write', 30, 20],
        ];
        foreach ($defs as $i => [$name, $abilities, $lastDays, $revokedDays]) {
            $plain = 'qrk_demo' . $r->id . $i . str_repeat('x', 28); // fake secret, only the hash is stored
            $this->up('api_tokens', ['restaurant_id' => $r->id, 'name' => $name], [
                'created_by' => $adminId, 'token_prefix' => substr($plain, 0, 8), 'token_hash' => ApiToken::hash($plain),
                'abilities' => $abilities, 'last_used_at' => now()->subDays($lastDays)->subHours($i),
                'revoked_at' => $revokedDays ? now()->subDays($revokedDays) : null,
                'created_at' => now()->subDays(55 - $i * 6),
            ]);
        }
    }

    private function security(Restaurant $r): void
    {
        $this->up('restaurant_security_settings', ['restaurant_id' => $r->id], [
            'ip_allowlist' => null, // left open so demo logins are never blocked
            'password_min_length' => $r->id === 1 ? 10 : 8,
            'password_require_mixed' => $r->id !== 3, 'password_require_number' => true, 'password_require_symbol' => $r->id === 1,
        ]);
    }

    private function loginAttempts(Restaurant $r): void
    {
        $users = DB::table('users')->where('restaurant_id', $r->id)->orderBy('id')->get(['id', 'email']);
        $agents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/126.0 Safari/537.36',
            'Mozilla/5.0 (Linux; Android 14; Redmi Note 12) Chrome/126.0 Mobile Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) Safari/604.1',
        ];
        for ($n = 0; $n < 24; $n++) {
            $u = $users[$n % $users->count()];
            $roll = $n % 8;
            $result = $roll === 6 ? 'failed' : ($roll === 7 ? ($n % 16 === 7 ? 'two_factor_failed' : 'blocked_ip') : 'success');
            $ip = $result === 'blocked_ip' ? '203.0.113.' . (10 + $n) : '39.' . (40 + $r->id) . '.' . (10 + $n) . '.' . (20 + $n * 3 % 200);
            $at = now()->subDays(59 - (int) floor($n * 59 / 23))->setTime(8 + $n % 12, ($n * 7) % 60);
            $this->up('login_attempts', ['restaurant_id' => $r->id, 'email' => $u->email, 'created_at' => $at], [
                'user_id' => $result === 'blocked_ip' ? null : $u->id, 'ip_address' => $ip,
                'user_agent' => $agents[$n % 3], 'result' => $result,
            ], false);
        }
    }

    private function pwaAndAi(Restaurant $r): void
    {
        $short = mb_substr(explode(' ', $r->name)[0] . ' ' . (explode(' ', $r->name)[1] ?? ''), 0, 30);
        $this->up('pwa_settings', ['restaurant_id' => $r->id], [
            'short_name' => trim($short), 'theme_color' => ['#e8890c', '#2563eb', '#b91c1c'][($r->id - 1) % 3], 'background_color' => '#ffffff',
        ]);

        $ai = AiSetting::firstOrNew(['restaurant_id' => $r->id]);
        $ai->fill([
            'llm_url' => 'https://api.example.com/v1/chat/completions', 'llm_key' => 'demo-llm-key-' . $r->id,
            'llm_model' => 'demo-model-small', 'target_margin' => 60 + $r->id * 5,
        ])->save();
    }
}
