<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    protected $fillable = ['restaurant_id', 'loyalty_account_id', 'order_id', 'type', 'points', 'note'];

    public function account()
    {
        return $this->belongsTo(LoyaltyAccount::class, 'loyalty_account_id');
    }
}
