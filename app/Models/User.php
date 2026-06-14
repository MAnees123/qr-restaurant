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
        'name', 'email', 'password', 'is_super_admin', 'avatar',
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
    ];

    /**
     * Determine if the user is a Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin === true;
    }
}
