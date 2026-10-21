<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';
    protected $guarded = ['id'];
    protected $casts = ['order_date' => 'date', 'received_at' => 'datetime'];
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function items() { return $this->hasMany(PurchaseOrderItem::class); }
    public function getDueAttribute(): float { return round((float) $this->total_amount - (float) $this->paid_amount, 2); }
    public function getPaymentStatusAttribute(): string
    {
        if ((float) $this->paid_amount <= 0) return 'unpaid';
        return $this->due > 0 ? 'partial' : 'paid';
    }
}
