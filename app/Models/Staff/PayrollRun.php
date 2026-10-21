<?php

namespace App\Models\Staff;

use Illuminate\Database\Eloquent\Model;

class PayrollRun extends Model
{
    protected $fillable = ['restaurant_id', 'month', 'generated_by'];

    public function payslips()
    {
        return $this->hasMany(Payslip::class);
    }
}
