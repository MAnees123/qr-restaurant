<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id', 'name', 'owner_name', 'cuisine_type', 'logo', 'address',
        'phone', 'is_active', 'is_suspended',
        'country', 'city', 'timezone', 'currency',
        'subscription_plan', 'subscription_ends_at', 'billing_cycle',
        'payment_status', 'trial_ends_at',
        'max_branches', 'max_users', 'max_tables', 'max_storage_mb',
        'theme', 'granted_features', 'domain',
    ];

    protected $casts = [
        'is_active'           => 'boolean',
        'is_suspended'        => 'boolean',
        'subscription_ends_at'=> 'datetime',
        'trial_ends_at'       => 'datetime',
        'granted_features'    => 'array',
    ];

    // --- Relationships ---

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

    public function menuCategories()
    {
        return $this->hasMany(MenuCategory::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function billingInvoices()
    {
        return $this->hasMany(BillingInvoice::class);
    }

    // --- Feature helpers ---

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->granted_features ?? []);
    }

    public function isSubscriptionActive(): bool
    {
        if ($this->payment_status === 'paid') {
            return $this->subscription_ends_at === null || $this->subscription_ends_at->isFuture();
        }
        if ($this->payment_status === 'trial') {
            return $this->trial_ends_at !== null && $this->trial_ends_at->isFuture();
        }
        return false;
    }
}
