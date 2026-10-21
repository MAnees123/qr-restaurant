<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyAccount extends Model
{
    protected $fillable = ['restaurant_id', 'phone', 'name', 'balance', 'lifetime_earned'];

    public function transactions()
    {
        return $this->hasMany(LoyaltyTransaction::class)->latest('id');
    }
}
