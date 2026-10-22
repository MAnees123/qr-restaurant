<?php

namespace Database\Seeders;

use App\Models\Comms\CommsSetting;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Demo data for Comms / branding / hardware: SMS, WhatsApp, email, branding, printer and QR-logo settings
 * plus notification logs. Re-runnable (section upserts + keyed log rows).
 */
class CommsDemoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Restaurant::orderBy('id')->get() as $r) {
            mt_srand($r->id * 5309 + 2);
            $this->settings($r);
            $this->logs($r);
        }
    }

    private function settings(Restaurant $r): void
    {
        $rid = $r->id;
        $events = ['order_placed', 'order_ready', 'order_delivered', 'reservation_confirmed'];
        $colors = [1 => ['#e8890c', '#c0441a'], 2 => ['#2563eb', '#1e3a8a'], 3 => ['#b91c1c', '#7f1d1d']][$rid] ?? ['#059669', '#065f46'];

        CommsSetting::write($rid, 'sms', [
            'enabled' => true, 'driver' => 'log', 'provider_url' => 'https://sms.example.pk/api/send',
            'token' => 'demo-sms-token-' . $rid, 'sender' => strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $r->name), 0, 8)),
            'events' => $events,
            'templates' => ['order_placed' => 'Dear customer, your order {order_number} of Rs {total} has been received.'],
        ]);
        CommsSetting::write($rid, 'whatsapp', [
            'enabled' => $rid !== 3, 'driver' => 'log', 'provider_url' => 'https://graph.example.com/v1/messages',
            'token' => 'demo-wa-token-' . $rid, 'sender' => '92300' . sprintf('%07d', 1234000 + $rid),
            'events' => ['order_placed', 'order_ready'],
            'templates' => ['order_ready' => 'Good news! Your order {order_number} is ready.'],
        ]);
        CommsSetting::write($rid, 'email', [
            'enabled' => true, 'driver' => 'log', 'from_name' => $r->name,
            'subject' => 'Update on your order {order_number}', 'events' => ['order_placed', 'order_delivered'],
        ]);
        CommsSetting::write($rid, 'branding', [
            'primary' => $colors[0], 'secondary' => $colors[1],
            'tagline' => ['Taste the tradition', 'Fresh. Fast. Flavourful.', 'Where every meal is a celebration'][$rid % 3],
        ]);
        CommsSetting::write($rid, 'printer', [
            'paper_width' => $rid === 2 ? 58 : 80, 'header' => $r->name . "\n" . $r->address,
            'footer' => 'Thank you! GST included. Visit again.', 'auto_print' => $rid === 1,
            'printer_host' => '192.168.1.' . (50 + $rid), 'printer_port' => 9100, 'show_logo' => true,
        ]);
        CommsSetting::write($rid, 'qr_logo', [
            'enabled' => true, 'source' => 'restaurant', 'size_percent' => 22,
            'background' => '#ffffff', 'foreground' => '#111827', 'rounded' => true,
        ]);

        // Give a few menu items barcodes (EAN-13 style) when none have one yet.
        $items = DB::table('menu_items')->where('restaurant_id', $rid)->orderBy('id')->limit(15)->pluck('id');
        foreach ($items as $i => $id) {
            DB::table('menu_items')->where('id', $id)->whereNull('barcode')
                ->update(['barcode' => sprintf('890%d%08d', $rid, 10000 + $i * 37 + $id)]);
        }
    }

    private function logs(Restaurant $r): void
    {
        $rid = $r->id;
        $events = [
            ['sms', 'order_placed', 'Dear customer, your order %s of Rs %s has been received.', 'sent'],
            ['whatsapp', 'order_ready', 'Good news! Your order %s is ready for pickup.', 'sent'],
            ['email', 'order_delivered', 'Your order %s has been delivered. Total Rs %s. Enjoy your meal!', 'logged'],
            ['sms', 'order_ready', 'Your order %s is ready. Please collect from the counter.', 'sent'],
            ['printer', 'receipt', 'Receipt printed for order %s (Rs %s)', 'logged'],
            ['whatsapp', 'order_placed', 'Thanks for ordering! Order %s of Rs %s is being prepared.', 'failed'],
            ['sms', 'reservation_confirmed', 'Your table reservation %s is confirmed. Total deposit Rs %s.', 'skipped'],
        ];
        $pool = StaffDemoSeeder::customerPool($rid);
        for ($n = 1; $n <= 28; $n++) {
            [$channel, $event, $tpl, $status] = $events[$n % count($events)];
            $ref = sprintf('ORD-D%d%03d', $rid, $n);
            $total = number_format(mt_rand(8, 55) * 100);
            $c = $pool[$n % count($pool)];
            $at = now()->subDays(59 - (int) floor(($n - 1) * 59 / 27))->setTime(mt_rand(11, 22), mt_rand(0, 59));
            $row = DB::table('comms_notification_logs')->where(['restaurant_id' => $rid, 'reference' => $ref, 'channel' => $channel, 'event' => $event])->first();
            $vals = [
                'driver' => 'log',
                'recipient' => $channel === 'email' ? $c['email'] : ($channel === 'printer' ? 'Kitchen printer' : $c['phone']),
                'subject' => $channel === 'email' ? "Update on your order {$ref}" : null,
                'message' => sprintf($tpl, $ref, $total),
                'status' => $status,
                'response' => $status === 'failed' ? 'Provider timeout (HTTP 504)' : ($status === 'skipped' ? 'Event disabled in settings' : 'Logged by log driver'),
                'created_at' => $at, 'updated_at' => $at,
            ];
            if ($row) {
                DB::table('comms_notification_logs')->where('id', $row->id)->update($vals);
            } else {
                DB::table('comms_notification_logs')->insert($vals + ['restaurant_id' => $rid, 'channel' => $channel, 'event' => $event, 'reference' => $ref]);
            }
        }
    }
}
