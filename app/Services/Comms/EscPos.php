<?php

namespace App\Services\Comms;

use App\Models\Order;

/** Minimal ESC/POS builder and raw TCP sender (port 9100 network printers). */
class EscPos
{
    public static function ticket(Order $order, array $s, string $type): string
    {
        $cols = ((int) ($s['paper_width'] ?? 80)) === 58 ? 32 : 48;
        $out = "\x1B@";                                   // init
        $out .= "\x1Ba\x01\x1BE\x01" . ($type === 'kitchen' ? 'KITCHEN TICKET' : ($order->restaurant->name ?? 'BILL')) . "\x1BE\x00\n";
        if (!empty($s['header'])) {
            $out .= $s['header'] . "\n";
        }
        $out .= "\x1Ba\x00" . str_repeat('-', $cols) . "\n";
        $out .= 'Order: ' . $order->order_number . "\n";
        $out .= 'Table: ' . ($order->table->table_number ?? '-') . "\n";
        $out .= str_repeat('-', $cols) . "\n";
        foreach ($order->orderItems as $it) {
            $name = ($it->quantity) . ' x ' . ($it->menuItem->name ?? 'Item');
            if ($type === 'bill') {
                $price = number_format((float) $it->subtotal, 2);
                $name = str_pad(mb_substr($name, 0, $cols - strlen($price) - 1), $cols - strlen($price)) . $price;
            }
            $out .= $name . "\n";
        }
        $out .= str_repeat('-', $cols) . "\n";
        if ($type === 'bill') {
            $out .= 'TOTAL: ' . number_format((float) $order->total_amount, 2) . "\n";
        }
        if (!empty($s['footer'])) {
            $out .= "\x1Ba\x01" . $s['footer'] . "\n";
        }
        return $out . "\n\n\n\x1DV\x00";                  // feed + cut
    }

    /** Returns null on success or an error message. */
    public static function send(string $host, int $port, string $payload, float $timeout = 3.0): ?string
    {
        $errno = 0;
        $errstr = '';
        $fp = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, $timeout);
        if (!$fp) {
            return "Could not connect to printer {$host}:{$port} ({$errstr})";
        }
        stream_set_timeout($fp, (int) $timeout);
        $written = @fwrite($fp, $payload);
        fclose($fp);
        return $written === false ? 'Failed writing to printer.' : null;
    }
}
