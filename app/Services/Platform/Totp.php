<?php

namespace App\Services\Platform;

/** Minimal RFC 6238 TOTP (SHA-1, 6 digits, 30 s) with RFC 4648 base32. */
class Totp
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public static function generateSecret(int $bytes = 20): string
    {
        return self::base32Encode(random_bytes($bytes));
    }

    public static function base32Encode(string $bin): string
    {
        $bits = '';
        foreach (str_split($bin) as $c) $bits .= str_pad(decbin(ord($c)), 8, '0', STR_PAD_LEFT);
        $out = '';
        foreach (str_split($bits, 5) as $chunk) {
            $out .= self::ALPHABET[bindec(str_pad($chunk, 5, '0'))];
        }
        return $out;
    }

    public static function base32Decode(string $b32): string
    {
        $b32 = strtoupper(preg_replace('/[^A-Za-z2-7]/', '', $b32));
        $bits = '';
        foreach (str_split($b32) as $c) $bits .= str_pad(decbin(strpos(self::ALPHABET, $c)), 5, '0', STR_PAD_LEFT);
        $out = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) $out .= chr(bindec($byte));
        }
        return $out;
    }

    public static function code(string $secret, ?int $time = null, int $digits = 6, int $period = 30): string
    {
        $counter = intdiv($time ?? time(), $period);
        $hash = hash_hmac('sha1', pack('N*', 0, $counter), self::base32Decode($secret), true);
        $o = ord($hash[19]) & 0xf;
        $int = ((ord($hash[$o]) & 0x7f) << 24) | (ord($hash[$o + 1]) << 16) | (ord($hash[$o + 2]) << 8) | ord($hash[$o + 3]);
        return str_pad((string) ($int % (10 ** $digits)), $digits, '0', STR_PAD_LEFT);
    }

    public static function verify(string $secret, string $code, int $window = 1, ?int $time = null): bool
    {
        $code = preg_replace('/\s+/', '', $code);
        if (! preg_match('/^\d{6}$/', $code)) return false;
        $time ??= time();
        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::code($secret, $time + $i * 30), $code)) return true;
        }
        return false;
    }

    public static function uri(string $secret, string $account, string $issuer): string
    {
        return 'otpauth://totp/' . rawurlencode($issuer . ':' . $account)
            . '?secret=' . $secret . '&issuer=' . rawurlencode($issuer) . '&algorithm=SHA1&digits=6&period=30';
    }
}
