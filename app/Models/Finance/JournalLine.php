<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class JournalLine extends Model
{
    protected $table = 'journal_lines';
    protected $guarded = ['id'];
    public function account() { return $this->belongsTo(Account::class); }
    public function entry() { return $this->belongsTo(JournalEntry::class, 'journal_entry_id'); }
}
