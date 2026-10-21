<?php

namespace App\Services\Platform;

use App\Models\Discount;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** CSV export/import definitions for menu categories, items, tables, discounts and (when present) customers. */
class CsvTransfer
{
    public const MAX_ROWS = 2000;

    public function entities(): array
    {
        $e = [
            'categories' => ['label' => 'Menu categories', 'columns' => ['name', 'description', 'sort_order', 'is_active'], 'sample' => ['Starters', 'Small plates', '1', '1']],
            'items'      => ['label' => 'Menu items', 'columns' => ['category', 'name', 'description', 'price', 'is_available'], 'sample' => ['Starters', 'Garlic bread', 'Toasted with butter', '4.50', '1']],
            'tables'     => ['label' => 'Tables', 'columns' => ['table_number', 'capacity', 'is_active'], 'sample' => ['T1', '4', '1']],
            'discounts'  => ['label' => 'Discounts', 'columns' => ['code', 'type', 'value', 'is_active', 'starts_at', 'expires_at', 'usage_limit'], 'sample' => ['SAVE10', 'percentage', '10', '1', '2026-01-01', '2026-12-31', '100']],
        ];
        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'restaurant_id') && Schema::hasColumn('customers', 'name')) {
            $cols = array_values(array_filter(['name', 'phone', 'email'], fn ($c) => Schema::hasColumn('customers', $c)));
            $e['customers'] = ['label' => 'Customers', 'columns' => $cols, 'sample' => array_slice(['Jane Doe', '555-0100', 'jane@example.com'], 0, count($cols))];
        }
        return $e;
    }

    public function has(string $entity): bool
    {
        return isset($this->entities()[$entity]);
    }

    public function exportRows(string $entity, int $rid): array
    {
        $cols = $this->entities()[$entity]['columns'];
        $rows = match ($entity) {
            'categories' => MenuCategory::where('restaurant_id', $rid)->orderBy('sort_order')->get()->map(fn ($c) => [$c->name, $c->description, $c->sort_order, (int) $c->is_active]),
            'items'      => MenuItem::with('category')->where('restaurant_id', $rid)->orderBy('id')->get()->map(fn ($i) => [$i->category?->name, $i->name, $i->description, $i->price, (int) $i->is_available]),
            'tables'     => Table::where('restaurant_id', $rid)->orderBy('id')->get()->map(fn ($t) => [$t->table_number, $t->capacity, (int) $t->is_active]),
            'discounts'  => Discount::where('restaurant_id', $rid)->orderBy('id')->get()->map(fn ($d) => [$d->code, $d->type, $d->value, (int) $d->is_active, $d->starts_at?->format('Y-m-d') ?? $d->starts_at, $d->expires_at?->format('Y-m-d') ?? $d->expires_at, $d->usage_limit]),
            'customers'  => DB::table('customers')->where('restaurant_id', $rid)->orderBy('id')->get()->map(fn ($c) => array_map(fn ($col) => $c->$col, $cols)),
        };
        return array_merge([$cols], $rows->map(fn ($r) => array_map(fn ($v) => $this->safeCell($v), $r))->all());
    }

    /** Neutralise spreadsheet formula injection. */
    private function safeCell($v)
    {
        return is_string($v) && $v !== '' && in_array($v[0], ['=', '+', '-', '@'], true) && ! is_numeric($v) ? "'" . $v : $v;
    }

    public function csv(array $rows): string
    {
        $h = fopen('php://temp', 'r+');
        fwrite($h, "\xEF\xBB\xBF");
        foreach ($rows as $r) fputcsv($h, $r, ',', '"', '\\');
        rewind($h);
        return stream_get_contents($h);
    }

    public function template(string $entity): string
    {
        $e = $this->entities()[$entity];
        return $this->csv([$e['columns'], $e['sample']]);
    }

    /** @return array{header_error:?string, rows: array<int, array{line:int, data:array, errors:array}>} */
    public function parse(string $entity, string $path, int $rid): array
    {
        $cols = $this->entities()[$entity]['columns'];
        $h = fopen($path, 'r');
        $header = fgetcsv($h, 0, ',', '"', '\\');
        if (! $header) return ['header_error' => 'The file is empty.', 'rows' => []];
        $header[0] = ltrim($header[0] ?? '', "\xEF\xBB\xBF");
        $header = array_map(fn ($c) => strtolower(trim((string) $c)), $header);
        $required = $this->required($entity);
        $missing = array_diff($required, $header);
        if ($missing) {
            return ['header_error' => 'Missing required column(s): ' . implode(', ', $missing) . '. Download the template for the expected format.', 'rows' => []];
        }

        $rows = [];
        $seen = [];
        $line = 1;
        while (($raw = fgetcsv($h, 0, ',', '"', '\\')) !== false) {
            $line++;
            if ($raw === [null] || count(array_filter($raw, fn ($v) => trim((string) $v) !== '')) === 0) continue;
            if (count($rows) >= self::MAX_ROWS) {
                return ['header_error' => 'Too many rows (max ' . self::MAX_ROWS . ' per import).', 'rows' => []];
            }
            $assoc = [];
            foreach ($cols as $c) {
                $i = array_search($c, $header, true);
                $assoc[$c] = $i === false ? null : trim((string) ($raw[$i] ?? ''));
            }
            [$clean, $errors] = $this->validateRow($entity, $assoc, $rid);
            $key = $this->naturalKey($entity, $clean);
            if ($key !== null) {
                if (isset($seen[$key])) $errors[] = "Duplicate of line {$seen[$key]} in this file.";
                else $seen[$key] = $line;
            }
            $rows[] = ['line' => $line, 'data' => $clean, 'errors' => $errors];
        }
        fclose($h);
        return ['header_error' => null, 'rows' => $rows];
    }

    private function required(string $entity): array
    {
        return match ($entity) {
            'categories' => ['name'], 'items' => ['category', 'name', 'price'], 'tables' => ['table_number'],
            'discounts' => ['code', 'type', 'value'], default => ['name'],
        };
    }

    private function naturalKey(string $entity, array $d): ?string
    {
        $k = match ($entity) {
            'categories' => $d['name'] ?? null, 'items' => ($d['category'] ?? '') . '|' . ($d['name'] ?? ''),
            'tables' => $d['table_number'] ?? null, 'discounts' => strtoupper($d['code'] ?? ''),
            default => $d['phone'] ?? $d['email'] ?? $d['name'] ?? null,
        };
        return $k === null || $k === '' || $k === '|' ? null : (string) $k;
    }

    private function bool($v): ?bool
    {
        if ($v === null || $v === '') return true;
        return match (strtolower($v)) { '1', 'yes', 'true', 'y' => true, '0', 'no', 'false', 'n' => false, default => null };
    }

    private function validateRow(string $entity, array $d, int $rid): array
    {
        $err = [];
        $need = function ($f) use (&$d, &$err) { if (($d[$f] ?? '') === '') $err[] = "'$f' is required."; };
        foreach ($d as $k => $v) {
            if (in_array($k, ['is_active', 'is_available'], true)) {
                $b = $this->bool($v);
                if ($b === null) $err[] = "'$k' must be 1/0 or yes/no.";
                $d[$k] = $b === null ? null : (int) $b;
            }
        }
        switch ($entity) {
            case 'categories':
                $need('name');
                if (($d['sort_order'] ?? '') !== '' && ! ctype_digit((string) $d['sort_order'])) $err[] = "'sort_order' must be a whole number.";
                break;
            case 'items':
                $need('category'); $need('name');
                if (($d['price'] ?? '') === '' || ! is_numeric($d['price']) || $d['price'] < 0) $err[] = "'price' must be a non-negative number.";
                break;
            case 'tables':
                $need('table_number');
                if (($d['capacity'] ?? '') !== '' && ! ctype_digit((string) $d['capacity'])) $err[] = "'capacity' must be a whole number.";
                break;
            case 'discounts':
                $need('code');
                $d['code'] = strtoupper((string) $d['code']);
                if (! in_array($d['type'], ['percentage', 'fixed'], true)) $err[] = "'type' must be percentage or fixed.";
                if (($d['value'] ?? '') === '' || ! is_numeric($d['value']) || $d['value'] < 0) $err[] = "'value' must be a non-negative number.";
                elseif ($d['type'] === 'percentage' && $d['value'] > 100) $err[] = 'Percentage cannot exceed 100.';
                foreach (['starts_at', 'expires_at'] as $f) {
                    if (($d[$f] ?? '') !== '' && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $d[$f])) $err[] = "'$f' must be YYYY-MM-DD.";
                }
                if (($d['usage_limit'] ?? '') !== '' && (! ctype_digit((string) $d['usage_limit']) || (int) $d['usage_limit'] < 1)) $err[] = "'usage_limit' must be a positive whole number.";
                if (! $err && Discount::where('code', $d['code'])->where('restaurant_id', '!=', $rid)->exists()) $err[] = 'This coupon code is already used by another restaurant.';
                break;
            case 'customers':
                $need('name');
                if (($d['email'] ?? '') !== '' && ! filter_var($d['email'], FILTER_VALIDATE_EMAIL)) $err[] = "'email' is not a valid email address.";
                break;
        }
        return [$d, $err];
    }

    /** Imports only the valid rows. @return array{created:int, updated:int, skipped:int} */
    public function commit(string $entity, array $parsedRows, int $rid): array
    {
        $created = $updated = $skipped = 0;
        DB::transaction(function () use ($entity, $parsedRows, $rid, &$created, &$updated, &$skipped) {
            foreach ($parsedRows as $row) {
                if ($row['errors']) { $skipped++; continue; }
                $d = array_map(fn ($v) => $v === '' ? null : $v, $row['data']);
                $model = match ($entity) {
                    'categories' => MenuCategory::firstOrNew(['restaurant_id' => $rid, 'name' => $d['name']]),
                    'items' => $this->itemModel($d, $rid),
                    'tables' => Table::firstOrNew(['restaurant_id' => $rid, 'table_number' => $d['table_number']]),
                    'discounts' => Discount::firstOrNew(['code' => $d['code'], 'restaurant_id' => $rid]),
                    default => null,
                };
                if ($entity === 'customers') {
                    $key = isset($d['phone']) ? ['phone' => $d['phone']] : (isset($d['email']) ? ['email' => $d['email']] : ['name' => $d['name']]);
                    $q = DB::table('customers')->where('restaurant_id', $rid)->where($key);
                    $vals = $d + ['updated_at' => now()];
                    if ($q->exists()) { $q->update($vals); $updated++; }
                    else { DB::table('customers')->insert($vals + ['restaurant_id' => $rid, 'created_at' => now()]); $created++; }
                    continue;
                }
                $payload = match ($entity) {
                    'categories' => ['description' => $d['description'], 'sort_order' => (int) ($d['sort_order'] ?? 0), 'is_active' => (bool) $d['is_active']],
                    'items' => ['description' => $d['description'], 'price' => $d['price'], 'is_available' => (bool) $d['is_available']],
                    'tables' => ['capacity' => $d['capacity'], 'is_active' => (bool) $d['is_active']],
                    'discounts' => ['type' => $d['type'], 'value' => $d['value'], 'is_active' => (bool) $d['is_active'], 'starts_at' => $d['starts_at'], 'expires_at' => $d['expires_at'], 'usage_limit' => $d['usage_limit']],
                };
                $model->exists ? $updated++ : $created++;
                $model->fill($payload)->save();
            }
        });
        return compact('created', 'updated', 'skipped');
    }

    private function itemModel(array $d, int $rid): MenuItem
    {
        $cat = MenuCategory::firstOrCreate(['restaurant_id' => $rid, 'name' => $d['category']], ['sort_order' => 0, 'is_active' => true]);
        return MenuItem::firstOrNew(['restaurant_id' => $rid, 'menu_category_id' => $cat->id, 'name' => $d['name']]);
    }
}
