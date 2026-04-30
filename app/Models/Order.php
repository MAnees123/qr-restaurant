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
        'status',
        'payment_status',
        'subtotal',
        'coupon_code',
        'discount_amount',
        'total_amount',
        'notes',
        'estimated_completion_time',
    ];

    protected $casts = [
        'estimated_completion_time' => 'datetime',
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
