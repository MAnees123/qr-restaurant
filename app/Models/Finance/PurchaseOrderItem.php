<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $table = 'purchase_order_items';
    protected $guarded = ['id'];

}
