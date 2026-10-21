<?php

namespace App\Services\Platform;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Tenant-scoped JSON backups. Tables are discovered at runtime: every table with a restaurant_id column,
 * plus child tables that hold a foreign key into one of them (e.g. order_items -> orders).
 * Users, billing and the platform security/API/AI settings are deliberately NOT part of a backup.
 */
class BackupService
{
    private const EXCLUDED = [
        'users', 'restaurants', 'sessions', 'migrations', 'plans', 'subscriptions', 'billing_invoices', 'activity_logs',
        'restaurant_feature', 'restaurant_role', 'api_tokens', 'login_attempts', 'restaurant_security_settings', 'user_two_factors', 'pwa_settings', 'ai_settings',
        'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'password_reset_tokens', 'notifications',
    ];

    public function dir(int $restaurantId): string
    {
        return storage_path('app/backups/' . $restaurantId);
    }

    /** @return array<int, array{table:string, fk:?string, parent:?string}> parents always precede children */
    public function spec(): array
    {
        $db = DB::getDatabaseName();
        $tables = array_values(array_unique(array_map(
            fn ($t) => $t['name'],
            array_filter(Schema::getTables(), fn ($t) => ! isset($t['schema']) || $t['schema'] === null || $t['schema'] === $db)
        )));
        $spec = [];
        $known = [];
        foreach ($tables as $t) {
            if (in_array($t, self::EXCLUDED, true)) continue;
            if (Schema::hasColumn($t, 'restaurant_id')) {
                $spec[] = ['table' => $t, 'fk' => null, 'parent' => null];
                $known[$t] = true;
            }
        }
        do {
            $added = false;
            foreach ($tables as $t) {
                if (isset($known[$t]) || in_array($t, self::EXCLUDED, true)) continue;
                foreach (Schema::getForeignKeys($t) as $fk) {
                    if (isset($known[$fk['foreign_table']]) && count($fk['columns']) === 1 && $fk['foreign_columns'] === ['id']) {
                        $spec[] = ['table' => $t, 'fk' => $fk['columns'][0], 'parent' => $fk['foreign_table']];
                        $known[$t] = true;
                        $added = true;
                        break;
                    }
                }
            }
        } while ($added);

        return $spec;
    }

    private function scoped(array $spec, array $item, int $rid)
    {
        $q = DB::table($item['table']);
        if ($item['fk'] === null) {
            return $q->where('restaurant_id', $rid);
        }
        $parent = collect($spec)->firstWhere('table', $item['parent']);
        return $q->whereIn($item['fk'], $this->scoped($spec, $parent, $rid)->select('id'));
    }

    public function create(int $rid): array
    {
        $spec = $this->spec();
        $data = [];
        $counts = [];
        foreach ($spec as $item) {
            $rows = $this->scoped($spec, $item, $rid)->get()->map(fn ($r) => (array) $r)->all();
            $data[$item['table']] = $rows;
            $counts[$item['table']] = count($rows);
        }
        $payload = [
            'meta' => ['version' => 1, 'restaurant_id' => $rid, 'created_at' => now()->toIso8601String(), 'counts' => $counts],
            'data' => $data,
        ];
        File::ensureDirectoryExists($this->dir($rid));
        $name = 'backup-' . now()->format('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.json';
        File::put($this->dir($rid) . '/' . $name, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return ['name' => $name, 'counts' => $counts];
    }

    public function list(int $rid): array
    {
        $out = [];
        foreach (File::glob($this->dir($rid) . '/backup-*.json') ?: [] as $f) {
            $out[] = ['name' => basename($f), 'size' => File::size($f), 'created' => File::lastModified($f)];
        }
        usort($out, fn ($a, $b) => [$b['created'], $b['name']] <=> [$a['created'], $a['name']]);
        return $out;
    }

    /** Returns the absolute path or null when the name is invalid / not this tenant's file. */
    public function path(int $rid, string $name): ?string
    {
        if (! preg_match('/^backup-\d{8}-\d{6}-[0-9a-f]{6}\.json$/', $name)) return null;
        $p = $this->dir($rid) . '/' . $name;
        return File::exists($p) ? $p : null;
    }

    public function delete(int $rid, string $name): bool
    {
        $p = $this->path($rid, $name);
        return $p ? File::delete($p) : false;
    }

    /** Replaces the tenant's data with the backup contents, atomically. */
    public function restore(int $rid, string $name): array
    {
        $p = $this->path($rid, $name);
        if (! $p) {
            throw new \InvalidArgumentException('Backup not found.');
        }
        $payload = json_decode(File::get($p), true);
        if (! is_array($payload) || ($payload['meta']['restaurant_id'] ?? null) !== $rid || ! isset($payload['data'])) {
            throw new \InvalidArgumentException('Backup file is invalid or belongs to another restaurant.');
        }

        $spec = $this->spec();
        $mysql = DB::getDriverName() === 'mysql';
        if ($mysql) DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            DB::transaction(function () use ($spec, $payload, $rid) {
                foreach (array_reverse($spec) as $item) {
                    $this->scoped($spec, $item, $rid)->delete();
                }
                foreach ($spec as $item) {
                    $rows = $payload['data'][$item['table']] ?? [];
                    if (! $rows) continue;
                    $cols = array_flip(Schema::getColumnListing($item['table']));
                    $rows = array_map(function ($r) use ($cols, $item, $rid) {
                        $r = array_intersect_key($r, $cols);
                        if ($item['fk'] === null) $r['restaurant_id'] = $rid;
                        return $r;
                    }, $rows);
                    foreach (array_chunk($rows, 200) as $chunk) {
                        DB::table($item['table'])->insert($chunk);
                    }
                }
            });
        } finally {
            if ($mysql) DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        return $payload['meta']['counts'] ?? [];
    }
}
