<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'plan_id',
        'starts_at',
        'ends_at',
        'status',
        'trial_ends_at',
        'payment_status',
        'billing_cycle',
    ];

    protected $dates = [
        'starts_at',
        'ends_at',
        'trial_ends_at',
    ];

    /**
     * The restaurant this subscription belongs to.
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * The plan this subscription is based on.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Check if the subscription is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    /**
     * Remaining days until expiry (null if no expiry).
     */
    public function remainingDays(): ?int
    {
        if ($this->ends_at === null) {
            return null; // lifetime or no expiry set
        }
        return max(0, Carbon::now()->diffInDays($this->ends_at, false));
    }
}
?>
