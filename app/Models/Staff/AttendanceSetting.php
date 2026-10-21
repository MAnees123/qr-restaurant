<?php

namespace App\Models\Staff;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    protected $fillable = ['restaurant_id', 'shift_start', 'late_grace_minutes', 'standard_hours'];

    public static function forRestaurant(int $restaurantId): self
    {
        return static::firstOrCreate(["restaurant_id" => $restaurantId])->refresh();
    }
}
