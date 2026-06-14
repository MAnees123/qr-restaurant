<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id', 'plan_id', 'invoice_number', 'amount', 'tax', 'discount',
        'payment_status', 'billing_cycle', 'payment_method', 'paid_at'
    ];

    protected $casts = [
        'amount'   => 'decimal:2',
        'tax'      => 'decimal:2',
        'discount' => 'decimal:2',
        'paid_at'  => 'datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
