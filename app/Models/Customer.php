<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['restaurant_id', 'name', 'phone', 'email', 'notes', 'tags'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tagList(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->tags))));
    }
}
