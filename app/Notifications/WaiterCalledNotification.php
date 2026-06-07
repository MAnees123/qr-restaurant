<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WaiterCalledNotification extends Notification
{
    use Queueable;

    public $tableCall;

    public function __construct($tableCall)
    {
        $this->tableCall = $tableCall;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'waiter_called',
            'call_id' => $this->tableCall->id,
            'table_number' => $this->tableCall->table->table_number ?? 'N/A',
            'message' => 'Waiter requested at Table ' . ($this->tableCall->table->table_number ?? 'N/A') . '!',
            'url' => route('admin.dashboard'),
        ];
    }
}
