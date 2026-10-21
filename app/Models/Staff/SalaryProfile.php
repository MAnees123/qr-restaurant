<?php

namespace App\Models\Staff;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SalaryProfile extends Model
{
    protected $fillable = ['restaurant_id', 'user_id', 'base_salary', 'allowances', 'deductions', 'overtime_rate', 'working_days'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
