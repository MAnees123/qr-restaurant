<?php

namespace App\Models\Staff;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    protected $fillable = [
        'restaurant_id', 'payroll_run_id', 'user_id', 'days_present', 'days_absent', 'overtime_hours',
        'base_salary', 'allowances', 'deductions', 'overtime_pay', 'absence_deduction', 'net_pay', 'status', 'paid_at',
    ];

    protected $casts = ['paid_at' => 'datetime'];

    public function run()
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
