<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';
    protected $guarded = ['id'];
    protected $casts = ['is_active' => 'boolean'];
    public function purchaseOrders() { return $this->hasMany(PurchaseOrder::class); }
    public function expenses() { return $this->hasMany(Expense::class); }
    /** Amount we owe: opening balance + unpaid on received/ordered POs. */
    public function getBalanceAttribute(): float
    {
        $open = $this->purchaseOrders()->where('status', '!=', 'cancelled')->selectRaw('COALESCE(SUM(total_amount - paid_amount),0) as due')->value('due');
        return round((float) $this->opening_balance + (float) $open, 2);
    }
}
