<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'order_placed',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'table_number' => $this->order->table ? $this->order->table->table_number : 'N/A',
            'message' => 'New Order from Table ' . ($this->order->table ? $this->order->table->table_number : 'N/A') . '!',
            'url' => route('admin.orders.show', $this->order->id),
        ];
    }
}
