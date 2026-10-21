<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $table = 'journal_entries';
    protected $guarded = ['id'];
    protected $casts = ['entry_date' => 'date'];
    public function lines() { return $this->hasMany(JournalLine::class); }
}
