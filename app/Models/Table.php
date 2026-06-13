<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'branch_id',
        'portion_id',
        'table_number',
        'capacity',
        'is_active',
        'status',
        'auto_release_at',
        'secure_token',
    ];

    protected $casts = [
        'auto_release_at' => 'datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function portion()
    {
        return $this->belongsTo(Portion::class);
    }

    public function qrCode()
    {
        return $this->hasOne(QrCode::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if table has any active (non-terminal) orders.
     */
    public function hasActiveOrders(): bool
    {
        return $this->orders()
            ->whereNotIn('status', ['served', 'cancelled'])
            ->exists();
    }

    /**
     * Schedule auto-release 30 minutes from now.
     */
    public function scheduleAutoRelease(): void
    {
        $this->update(['auto_release_at' => now()->addMinutes(30)]);
    }

    /**
     * Cancel any pending auto-release (e.g. new order placed).
     */
    public function cancelAutoRelease(): void
    {
        $this->update(['auto_release_at' => null]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            if (empty($table->secure_token)) {
                $table->secure_token = bin2hex(random_bytes(16));
            }
        });
    }
}
