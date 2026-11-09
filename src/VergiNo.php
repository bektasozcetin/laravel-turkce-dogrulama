<?php

declare(strict_types=1);

namespace Bektasozcetin\TurkceDogrulama;

/**
 * Vergi kimlik numarası (VKN): 10 hane.
 *
 * Maliye Bakanlığı algoritması: her hane için
 * geçici = (hane + (10 − sıra)) mod 10
 * geçici 0 ise katkı 0, değilse (geçici × 2^(10−sıra)) mod 9 — sonuç 0 ise 9.
 * Son hane: (10 − (katkıların toplamı mod 10)) mod 10.
 */
final class VergiNo
{
    public static function temizle(?string $no): string
    {
        return (string) preg_replace('/\D/', '', (string) $no);
    }

    public static function gecerliMi(?string $no): bool
    {
        $t = self::temizle($no);

        if (strlen($t) !== 10) {
            return false;
        }

        $toplam = 0;

        for ($i = 0; $i < 9; $i++) {
            $gecici = ((int) $t[$i] + (10 - ($i + 1))) % 10;

            if ($gecici === 0) {
                continue;
            }

            $katki = ($gecici * (2 ** (10 - ($i + 1)))) % 9;
            $toplam += $katki === 0 ? 9 : $katki;
        }

        return (10 - ($toplam % 10)) % 10 === (int) $t[9];
    }

    /**
     * Girilen numara TCKN mi VKN mi? Bireysel/kurumsal ayrımı için.
     *
     * @return 'tckn'|'vkn'|null
     */
    public static function tur(?string $no): ?string
    {
        $t = self::temizle($no);

        return match (true) {
            strlen($t) === 11 && TcKimlikNo::gecerliMi($t) => 'tckn',
            strlen($t) === 10 && self::gecerliMi($t) => 'vkn',
            default => null,
        };
    }
}
