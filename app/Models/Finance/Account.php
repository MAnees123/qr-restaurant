<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';
    protected $guarded = ['id'];
    public function lines() { return $this->hasMany(JournalLine::class); }
}
