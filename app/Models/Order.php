<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'table_id',
        'order_number',
        'guest_token',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'coupon_code',
        'discount_amount',
        'total_amount',
        'notes',
        'estimated_completion_time',
        'order_type', 'customer_name', 'customer_phone', 'customer_email', 'delivery_address', 'delivery_fee',
        'delivery_status', 'driver_name', 'driver_phone', 'delivered_at', 'rejection_reason',
        'placed_by_user_id', 'loyalty_points_redeemed',
    ];

    protected $casts = [
        'estimated_completion_time' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
