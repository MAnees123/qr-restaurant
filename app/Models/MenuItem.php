<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_category_id',
        'restaurant_id',
        'name',
        'description',
        'price',
        'image',
        'is_available',
        'sort_order',
        'preparation_time',
    ];

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
