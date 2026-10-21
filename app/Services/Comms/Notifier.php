<?php

namespace App\Services\Comms;

use App\Models\Comms\CommsSetting;
use App\Models\Comms\NotificationLog;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

/**
 * Sends order / reservation notifications over sms, whatsapp and email.
 * Drivers: "log" (records only), "mail" (Laravel Mail, email channel) and "http"
 * (POSTs JSON to a provider URL entered by the restaurant; sms / whatsapp channels).
 */
class Notifier
{
    public const CHANNELS = ['sms' => 'sms_notif', 'whatsapp' => 'whatsapp_notif', 'email' => 'email_notif'];

    public const EVENTS = [
        'order_placed'          => 'Order placed',
        'order_status_changed'  => 'Order status changed',
        'reservation_confirmed' => 'Reservation confirmed',
    ];

    public const DEFAULT_TEMPLATES = [
        'order_placed'          => 'Hi {customer_name}, your order {order_number} at {restaurant} was received. Total: {total}.',
        'order_status_changed'  => 'Your order {order_number} at {restaurant} is now: {status}.',
        'reservation_confirmed' => 'Hi {customer_name}, your reservation at {restaurant} on {date} {time} is confirmed.',
    ];

    public static function render(string $template, array $vars): string
    {
        $pairs = [];
        foreach ($vars as $k => $v) {
            $pairs['{' . $k . '}'] = (string) $v;
        }
        return strtr($template, $pairs);
    }

    /** @param array{phone?:?string,email?:?string} $contact */
    public function dispatch(Restaurant $restaurant, string $event, array $vars, array $contact, ?string $reference = null): void
    {
        foreach (self::CHANNELS as $channel => $feature) {
            if (!$restaurant->hasFeature($feature)) {
                continue;
            }
            $s = CommsSetting::read($restaurant->id, $channel);
            if (!$s['enabled'] || !in_array($event, $s['events'] ?? [], true)) {
                continue;
            }
            $recipient = $channel === 'email' ? ($contact['email'] ?? null) : ($contact['phone'] ?? null);
            $template = $s['templates'][$event] ?? self::DEFAULT_TEMPLATES[$event] ?? '';
            $message = self::render($template ?: self::DEFAULT_TEMPLATES[$event], $vars);
            $subject = $channel === 'email' ? self::render($s['subject'] ?? '', $vars) : null;

            if (!$recipient) {
                $this->log($restaurant, $channel, $event, $s['driver'], null, $subject, $message, 'skipped', 'No customer ' . ($channel === 'email' ? 'email' : 'phone') . ' on record.', $reference);
                continue;
            }
            $this->deliver($restaurant, $channel, $event, $s, $recipient, $subject, $message, $reference);
        }
    }

    /** Send a test message through a channel using its saved settings (ignores enabled/event filters). */
    public function sendTest(Restaurant $restaurant, string $channel, string $recipient): NotificationLog
    {
        $s = CommsSetting::read($restaurant->id, $channel);
        $message = 'Test message from ' . $restaurant->name . '.';
        $subject = $channel === 'email' ? 'Test message from ' . $restaurant->name : null;
        return $this->deliver($restaurant, $channel, 'test', $s, $recipient, $subject, $message, null);
    }

    private function deliver(Restaurant $restaurant, string $channel, string $event, array $s, string $recipient, ?string $subject, string $message, ?string $reference): NotificationLog
    {
        $driver = $s['driver'] ?? 'log';
        $status = 'logged';
        $response = null;

        try {
            if ($driver === 'mail' && $channel === 'email') {
                Mail::raw($message, function ($m) use ($recipient, $subject, $s, $restaurant) {
                    $m->to($recipient)->subject($subject ?: 'Notification');
                    $m->from(config('mail.from.address'), $s['from_name'] ?: $restaurant->name);
                });
                $status = 'sent';
                $response = 'Handed to the configured Laravel mailer.';
            } elseif ($driver === 'http' && $channel !== 'email') {
                if (!preg_match('#^https?://#i', (string) $s['provider_url'])) {
                    throw new \RuntimeException('Provider URL is not configured.');
                }
                $req = Http::timeout(10)->acceptJson();
                if (!empty($s['token'])) {
                    $req = $req->withToken($s['token']);
                }
                $res = $req->post($s['provider_url'], [
                    'to' => $recipient, 'from' => $s['sender'] ?? '', 'message' => $message, 'channel' => $channel,
                ]);
                $status = $res->successful() ? 'sent' : 'failed';
                $response = 'HTTP ' . $res->status() . ' ' . mb_substr($res->body(), 0, 500);
            } else {
                $status = 'logged';
                $response = 'Log driver: message recorded, nothing was delivered.';
            }
        } catch (\Throwable $e) {
            $status = 'failed';
            $response = mb_substr($e->getMessage(), 0, 500);
        }

        return $this->log($restaurant, $channel, $event, $driver, $recipient, $subject, $message, $status, $response, $reference);
    }

    public function log(Restaurant $restaurant, string $channel, string $event, ?string $driver, ?string $recipient, ?string $subject, ?string $message, string $status, ?string $response, ?string $reference): NotificationLog
    {
        return NotificationLog::create([
            'restaurant_id' => $restaurant->id, 'channel' => $channel, 'event' => $event, 'driver' => $driver,
            'recipient' => $recipient, 'subject' => $subject, 'message' => $message, 'status' => $status,
            'response' => $response, 'reference' => $reference,
        ]);
    }
}
