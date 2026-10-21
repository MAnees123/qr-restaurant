<?php

namespace App\Models\Comms;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $table = 'comms_notification_logs';
    protected $fillable = [
        'restaurant_id', 'channel', 'event', 'driver', 'recipient', 'subject',
        'message', 'status', 'response', 'reference',
    ];
}
