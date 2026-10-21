<?php

namespace App\Models\Platform;

use Illuminate\Database\Eloquent\Model;

class SecuritySetting extends Model
{
    protected $table = 'restaurant_security_settings';
    protected $fillable = ['restaurant_id', 'ip_allowlist', 'password_min_length', 'password_require_mixed', 'password_require_number', 'password_require_symbol'];
    protected $casts = [
        'password_require_mixed' => 'boolean', 'password_require_number' => 'boolean', 'password_require_symbol' => 'boolean',
    ];

    public static function forRestaurant(int $id): self
    {
        return static::firstOrNew(['restaurant_id' => $id], ['password_min_length' => 8]);
    }

    public function allowedIps(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/[\s,]+/', (string) $this->ip_allowlist))));
    }
}
