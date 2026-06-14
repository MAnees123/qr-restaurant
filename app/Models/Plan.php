<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'price_monthly', 'price_yearly', 'trial_days',
        'max_branches', 'max_users', 'max_tables', 'max_storage_mb', 'features', 'is_active',
    ];

    protected $casts = [
        'features'      => 'array',
        'is_active'     => 'boolean',
        'price_monthly' => 'decimal:2',
        'price_yearly'  => 'decimal:2',
    ];

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class);
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }
}
