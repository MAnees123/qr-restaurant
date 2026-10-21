<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $table = 'tax_rates';
    protected $guarded = ['id'];
    protected $casts = ['is_active' => 'boolean', 'is_default' => 'boolean'];
}
