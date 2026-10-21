<?php

namespace App\Services\Comms;

use App\Models\Comms\CommsSetting;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/** Renders a table QR as SVG, overlaying the restaurant logo when the qr_logo feature is granted. */
class QrRenderer
{
    public static function menuUrl(Table $table): string
    {
        return route('menu.show', $table->secure_token ?? ($table->qrCode->code ?? 'none'));
    }

    public static function svg(Restaurant $restaurant, string $url, ?array $settings = null, int $size = 300): string
    {
        $s = $settings ?? CommsSetting::read($restaurant->id, 'qr_logo');
        $useLogo = $restaurant->hasFeature('qr_logo') && !empty($s['enabled']);
        $fg = self::rgb($s['foreground'] ?? '#000000');

        $gen = QrCode::format('svg')->size($size)->margin(1)->errorCorrection($useLogo ? 'H' : 'M')->color($fg[0], $fg[1], $fg[2]);
        $svg = (string) $gen->generate($url);

        if (!$useLogo) {
            return $svg;
        }
        $path = ($s['source'] ?? 'restaurant') === 'custom' ? ($s['logo'] ?? null) : $restaurant->logo;
        if (!$path || !Storage::disk('public')->exists($path)) {
            return $svg;
        }
        if (!preg_match('/viewBox="0 0 ([\d.]+) ([\d.]+)"/', $svg, $m)) {
            return $svg;
        }
        $full = (float) $m[1];
        $pct = max(10, min(30, (int) ($s['size_percent'] ?? 22))) / 100;
        $box = $full * $pct;
        $x = ($full - $box) / 2;
        $mime = Storage::disk('public')->mimeType($path) ?: 'image/png';
        $data = base64_encode(Storage::disk('public')->get($path));
        $bg = htmlspecialchars($s['background'] ?? '#ffffff', ENT_QUOTES);
        $rx = !empty($s['rounded']) ? $box * 0.18 : 0;
        $overlay = '<g data-qr-logo="1"><rect x="' . $x . '" y="' . $x . '" width="' . $box . '" height="' . $box . '" rx="' . $rx . '" fill="' . $bg . '"/>'
            . '<image x="' . ($x + $box * 0.08) . '" y="' . ($x + $box * 0.08) . '" width="' . ($box * 0.84) . '" height="' . ($box * 0.84)
            . '" preserveAspectRatio="xMidYMid meet" href="data:' . $mime . ';base64,' . $data . '"/></g>';
        return str_replace('</svg>', $overlay . '</svg>', $svg);
    }

    private static function rgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
            return [0, 0, 0];
        }
        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }
}
