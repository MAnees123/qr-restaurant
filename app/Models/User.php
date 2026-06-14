<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name', 'email', 'password',
        'restaurant_id', 'role',
        'is_super_admin', 'is_suspended', 'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_super_admin'    => 'boolean',
        'is_suspended'      => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * The restaurant this user belongs to (null for Super Admins).
     */
    public function restaurant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Determine if the user is a Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin === true;
    }

    /**
     * Check whether the user's restaurant has a specific feature enabled.
     * Super admins always return true.
     */
    public function hasFeature(string $feature): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        return $this->restaurant?->hasFeature($feature) ?? false;
    }
}
