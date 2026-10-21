<?php

namespace App\Models\Platform;

use Illuminate\Database\Eloquent\Model;

class PwaSetting extends Model
{
    protected $fillable = ['restaurant_id', 'short_name', 'theme_color', 'background_color'];

    public static function forRestaurant(int $id): self
    {
        return static::firstOrNew(['restaurant_id' => $id], ['theme_color' => '#059669', 'background_color' => '#ffffff']);
    }
}
