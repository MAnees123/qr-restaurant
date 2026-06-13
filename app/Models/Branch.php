<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['restaurant_id', 'name', 'is_active'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function portions()
    {
        return $this->hasMany(Portion::class);
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }
}
