<?php

namespace App\Models\Platform;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserTwoFactor extends Model
{
    protected $fillable = ['user_id', 'secret', 'recovery_codes', 'confirmed_at'];
    protected $casts = ['confirmed_at' => 'datetime', 'secret' => 'encrypted', 'recovery_codes' => 'array'];
    protected $hidden = ['secret', 'recovery_codes'];

    public function user() { return $this->belongsTo(User::class); }

    public function isEnabled(): bool
    {
        return $this->confirmed_at !== null;
    }
}
