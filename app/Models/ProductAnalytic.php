<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAnalytic extends Model
{
    protected $fillable = [
        'restaurant_id',
        'menu_item_id',
        'quantity_sold',
        'total_revenue',
        'total_discount',
        'total_profit',
        'period_type',
        'period_date',
    ];

    protected $casts = [
        'period_date' => 'datetime',
        'total_revenue' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'total_profit' => 'decimal:2',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
