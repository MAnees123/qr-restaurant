<?php

namespace App\Models\Staff;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    public const STATUSES = ['present', 'late', 'absent', 'leave', 'half_day'];

    protected $fillable = ['restaurant_id', 'user_id', 'work_date', 'clock_in', 'clock_out', 'status', 'note'];

    protected $casts = ['work_date' => 'date', 'clock_in' => 'datetime', 'clock_out' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workedMinutes(): int
    {
        if (!$this->clock_in || !$this->clock_out || $this->clock_out->lt($this->clock_in)) {
            return 0;
        }

        return (int) $this->clock_in->diffInMinutes($this->clock_out, true);
    }

    public function workedLabel(): string
    {
        $m = $this->workedMinutes();

        return $m ? intdiv($m, 60) . 'h ' . str_pad((string) ($m % 60), 2, '0', STR_PAD_LEFT) . 'm' : '-';
    }
}
