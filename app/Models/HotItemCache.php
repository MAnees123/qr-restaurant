<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotItemCache extends Model
{
    protected $fillable = [
        'restaurant_id',
        'menu_item_id',
        'rank_score',
        'badge_type',
        'period_type',
        'quantity_sold',
        'total_revenue',
        'growth_percentage',
    ];

    protected $casts = [
        'rank_score' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'growth_percentage' => 'decimal:2',
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
