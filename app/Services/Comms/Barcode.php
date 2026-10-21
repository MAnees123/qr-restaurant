<?php

namespace App\Services\Comms;

/** Small self-contained EAN-13 and Code 128 (set B) SVG barcode generator. */
class Barcode
{
    private const L = ['0001101','0011001','0010011','0111101','0100011','0110001','0101111','0111011','0110111','0001011'];
    private const G = ['0100111','0110011','0011011','0100001','0011101','0111001','0000101','0010001','0001001','0010111'];
    private const R = ['1110010','1100110','1101100','1000010','1011100','1001110','1010000','1000100','1001000','1110100'];
    private const PARITY = ['LLLLLL','LLGLGG','LLGGLG','LLGGGL','LGLLGG','LGGLLG','LGGGLL','LGLGLG','LGLGGL','LGGLGL'];

    private const C128 = [
        '212222','222122','222221','121223','121322','131222','122213','122312','132212','221213',
        '221312','231212','112232','122132','122231','113222','123122','123221','223211','221132',
        '221231','213212','223112','312131','311222','321122','321221','312212','322112','322211',
        '212123','212321','232121','111323','131123','131321','112313','132113','132311','211313',
        '231113','231311','112133','112331','132131','113123','113321','133121','313121','211331',
        '231131','213113','213311','213131','311123','311321','331121','312113','312311','332111',
        '314111','221411','431111','111224','111422','121124','121421','141122','141221','112214',
        '112412','122114','122411','142112','142211','241211','221114','413111','241112','134111',
        '111242','121142','121241','114212','124112','124211','411212','421112','421211','212141',
        '214121','412121','111143','111341','131141','114113','114311','411113','411311','113141',
        '114131','311141','411131','211412','211214','211232','2331112',
    ];

    public static function ean13CheckDigit(string $twelve): int
    {
        $sum = 0;
        foreach (str_split($twelve) as $i => $d) {
            $sum += (int) $d * ($i % 2 === 0 ? 1 : 3);
        }
        return (10 - $sum % 10) % 10;
    }

    public static function isValidEan13(string $code): bool
    {
        return preg_match('/^\d{13}$/', $code) === 1 && (int) $code[12] === self::ean13CheckDigit(substr($code, 0, 12));
    }

    /** Build a valid EAN-13 from a 12 digit string. */
    public static function makeEan13(string $twelve): string
    {
        return $twelve . self::ean13CheckDigit($twelve);
    }

    /** Bit string (1 = bar) for EAN-13. */
    public static function ean13Bits(string $code): string
    {
        if (!self::isValidEan13($code)) {
            throw new \InvalidArgumentException('Invalid EAN-13 code.');
        }
        $parity = self::PARITY[(int) $code[0]];
        $bits = '101';
        for ($i = 0; $i < 6; $i++) {
            $d = (int) $code[$i + 1];
            $bits .= $parity[$i] === 'L' ? self::L[$d] : self::G[$d];
        }
        $bits .= '01010';
        for ($i = 7; $i < 13; $i++) {
            $bits .= self::R[(int) $code[$i]];
        }
        return $bits . '101';
    }

    /** Code 128 symbol values (start B, data, checksum, stop). */
    public static function code128Values(string $text): array
    {
        $vals = [104];
        $sum = 104;
        foreach (str_split($text) as $i => $ch) {
            $v = ord($ch) - 32;
            if ($v < 0 || $v > 95) {
                throw new \InvalidArgumentException('Code 128 (B) supports printable ASCII only.');
            }
            $vals[] = $v;
            $sum += $v * ($i + 1);
        }
        $vals[] = $sum % 103;
        $vals[] = 106;
        return $vals;
    }

    public static function code128Bits(string $text): string
    {
        $bits = '';
        foreach (self::code128Values($text) as $v) {
            $isBar = true;
            foreach (str_split(self::C128[$v]) as $w) {
                $bits .= str_repeat($isBar ? '1' : '0', (int) $w);
                $isBar = !$isBar;
            }
        }
        return $bits;
    }

    /** Picks EAN-13 when the code is a valid 13-digit EAN, otherwise Code 128. */
    public static function svg(string $code, int $height = 60, int $module = 2, bool $withText = true): string
    {
        $isEan = self::isValidEan13($code);
        $bits = $isEan ? self::ean13Bits($code) : self::code128Bits($code);
        $quiet = 10;
        $width = (strlen($bits) + 2 * $quiet) * $module;
        $textH = $withText ? 16 : 0;
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . ($height + $textH)
            . '" viewBox="0 0 ' . $width . ' ' . ($height + $textH) . '" data-barcode-type="' . ($isEan ? 'ean13' : 'code128') . '">'
            . '<rect width="100%" height="100%" fill="#fff"/>';
        $x = 0;
        $n = strlen($bits);
        while ($x < $n) {
            if ($bits[$x] === '1') {
                $start = $x;
                while ($x < $n && $bits[$x] === '1') {
                    $x++;
                }
                $svg .= '<rect x="' . (($start + $quiet) * $module) . '" y="0" width="' . (($x - $start) * $module) . '" height="' . $height . '" fill="#000"/>';
            } else {
                $x++;
            }
        }
        if ($withText) {
            $svg .= '<text x="' . ($width / 2) . '" y="' . ($height + 13) . '" font-family="monospace" font-size="12" text-anchor="middle">'
                . htmlspecialchars($code, ENT_QUOTES) . '</text>';
        }
        return $svg . '</svg>';
    }
}
