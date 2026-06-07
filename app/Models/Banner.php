<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'title',
        'subtitle',
        'image_path',
        'redirect_url',
        'is_active',
        'sort_order',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
