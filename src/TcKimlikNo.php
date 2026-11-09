<?php

declare(strict_types=1);

namespace Bektasozcetin\TurkceDogrulama;

/**
 * T.C. kimlik numarası: 11 hane, ilk hane 0 olamaz.
 *
 * 10. hane: (tek sıradakilerin toplamı × 7 − çift sıradakilerin toplamı) mod 10
 * 11. hane: ilk 10 hanenin toplamı mod 10
 *
 * Bu yalnızca biçim ve kontrol basamağı denetimidir; numaranın gerçekten
 * birine ait olduğunu söylemez. Kimlik doğrulaması için NVİ servisini kullan.
 */
final class TcKimlikNo
{
    public static function temizle(?string $no): string
    {
        return (string) preg_replace('/\D/', '', (string) $no);
    }

    public static function gecerliMi(?string $no): bool
    {
        $t = self::temizle($no);

        if (strlen($t) !== 11 || $t[0] === '0') {
            return false;
        }

        $hane = array_map('intval', str_split($t));

        $tekler = $hane[0] + $hane[2] + $hane[4] + $hane[6] + $hane[8];
        $ciftler = $hane[1] + $hane[3] + $hane[5] + $hane[7];

        if ((($tekler * 7) - $ciftler) % 10 !== $hane[9]) {
            return false;
        }

        return array_sum(array_slice($hane, 0, 10)) % 10 === $hane[10];
    }

    /** "12345678901" → "123•••••901" */
    public static function maskele(?string $no): string
    {
        $t = self::temizle($no);

        return strlen($t) === 11
            ? substr($t, 0, 3).'•••••'.substr($t, -3)
            : '•••';
    }
}
