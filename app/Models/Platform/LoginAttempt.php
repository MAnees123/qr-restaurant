<?php

namespace App\Models\Platform;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    public $timestamps = false;
    protected $fillable = ['restaurant_id', 'user_id', 'email', 'ip_address', 'user_agent', 'result', 'created_at'];
    protected $casts = ['created_at' => 'datetime'];
}
