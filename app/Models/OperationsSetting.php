<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationsSetting extends Model
{
    protected $fillable = [
        'restaurant_id', 'online_ordering_open', 'delivery_fee', 'delivery_min_order',
        'loyalty_enabled', 'loyalty_points_per_currency', 'loyalty_point_value', 'loyalty_min_redeem',
    ];

    protected $casts = [
        'online_ordering_open' => 'boolean',
        'loyalty_enabled' => 'boolean',
        'delivery_fee' => 'float',
        'delivery_min_order' => 'float',
        'loyalty_points_per_currency' => 'float',
        'loyalty_point_value' => 'float',
    ];

    public static function forRestaurant(int $restaurantId): self
    {
        return static::firstOrCreate(['restaurant_id' => $restaurantId], [
            'online_ordering_open' => true, 'delivery_fee' => 0, 'delivery_min_order' => 0,
            'loyalty_enabled' => true, 'loyalty_points_per_currency' => 1,
            'loyalty_point_value' => 1, 'loyalty_min_redeem' => 10,
        ])->refresh();
    }
}
