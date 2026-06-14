<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id', 'user_id', 'action', 'description', 'ip_address', 'user_agent'
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, ?string $description = null, ?int $restaurantId = null): self
    {
        return self::create([
            'restaurant_id' => $restaurantId ?? (auth()->check() ? auth()->user()->restaurant_id : null),
            'user_id'       => auth()->id(),
            'action'        => $action,
            'description'   => $description,
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
        ]);
    }
}
