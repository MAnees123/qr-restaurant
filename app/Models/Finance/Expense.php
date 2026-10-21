<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'expenses';
    protected $guarded = ['id'];
    protected $casts = ['expense_date' => 'date'];
    public function category() { return $this->belongsTo(ExpenseCategory::class, 'expense_category_id'); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
}
