<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $table = 'expense_categories';
    protected $guarded = ['id'];
    public function expenses() { return $this->hasMany(Expense::class); }
}
