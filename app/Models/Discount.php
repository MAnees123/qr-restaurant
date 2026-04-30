<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'restaurant_id', 'code', 'type', 'value', 'is_active', 
        'starts_at', 'expires_at', 'usage_limit', 'used_count'
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}