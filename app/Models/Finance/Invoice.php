<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $guarded = ['id'];
    protected $casts = ['invoice_date' => 'date'];
    public function order() { return $this->belongsTo(\App\Models\Order::class); }
}
