<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'code',
        'qr_image_path',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
