<?php

namespace App\Models\Platform;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiToken extends Model
{
    protected $fillable = ['restaurant_id', 'created_by', 'name', 'token_prefix', 'token_hash', 'abilities', 'last_used_at', 'revoked_at'];
    protected $casts = ['last_used_at' => 'datetime', 'revoked_at' => 'datetime'];
    protected $hidden = ['token_hash'];

    public function restaurant() { return $this->belongsTo(Restaurant::class); }

    public static function hash(string $plain): string
    {
        return hash('sha256', $plain);
    }

    /** @return array{0: self, 1: string} model and the plain token (shown once) */
    public static function issue(int $restaurantId, string $name, bool $write, ?int $userId = null): array
    {
        $plain = 'qrk_' . Str::random(40);
        $token = static::create([
            'restaurant_id' => $restaurantId,
            'created_by'    => $userId,
            'name'          => $name,
            'token_prefix'  => substr($plain, 0, 8),
            'token_hash'    => static::hash($plain),
            'abilities'     => $write ? 'read,write' : 'read',
        ]);
        return [$token, $plain];
    }

    public function can(string $ability): bool
    {
        return in_array($ability, explode(',', $this->abilities), true);
    }
}
