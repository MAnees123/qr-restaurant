<?php

namespace App\Models\Comms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class CommsSetting extends Model
{
    protected $table = 'comms_settings';
    protected $fillable = ['restaurant_id', 'section', 'data'];
    protected $casts = ['data' => 'array'];

    public const DEFAULTS = [
        'sms' => [
            'enabled' => false, 'driver' => 'log', 'provider_url' => '', 'token' => '', 'sender' => '',
            'events' => ['order_placed'], 'templates' => [],
        ],
        'whatsapp' => [
            'enabled' => false, 'driver' => 'log', 'provider_url' => '', 'token' => '', 'sender' => '',
            'events' => ['order_placed'], 'templates' => [],
        ],
        'email' => [
            'enabled' => false, 'driver' => 'log', 'from_name' => '', 'subject' => 'Update on your order {order_number}',
            'events' => ['order_placed'], 'templates' => [],
        ],
        'branding' => [
            'primary' => '#e8890c', 'secondary' => '#c0441a', 'tagline' => '', 'logo' => null, 'favicon' => null,
        ],
        'printer' => [
            'paper_width' => 80, 'header' => '', 'footer' => 'Thank you! Visit again.', 'auto_print' => false,
            'printer_host' => '', 'printer_port' => 9100, 'show_logo' => true,
        ],
        'qr_logo' => [
            'enabled' => true, 'source' => 'restaurant', 'logo' => null, 'size_percent' => 22,
            'background' => '#ffffff', 'foreground' => '#000000', 'rounded' => true,
        ],
    ];

    /** Settings for a section with defaults merged in. Token is returned decrypted. */
    public static function read(int $restaurantId, string $section): array
    {
        $row = static::where('restaurant_id', $restaurantId)->where('section', $section)->first();
        $data = array_merge(static::DEFAULTS[$section] ?? [], $row?->data ?? []);
        if (!empty($data['token'])) {
            try {
                $data['token'] = Crypt::decryptString($data['token']);
            } catch (\Throwable) {
                $data['token'] = '';
            }
        }
        return $data;
    }

    /** Merge-save a section. A "token" key is stored encrypted. */
    public static function write(int $restaurantId, string $section, array $values): void
    {
        $row = static::firstOrNew(['restaurant_id' => $restaurantId, 'section' => $section]);
        $data = $row->data ?? [];
        if (array_key_exists('token', $values)) {
            $values['token'] = $values['token'] === '' || $values['token'] === null
                ? ''
                : Crypt::encryptString($values['token']);
        }
        $row->data = array_merge($data, $values);
        $row->save();
    }
}
