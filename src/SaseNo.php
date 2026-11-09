<?php

declare(strict_types=1);

namespace Bektasozcetin\TurkceDogrulama;

/**
 * Şase numarası (VIN, ISO 3779): 17 karakter.
 *
 * I, O ve Q harfleri 1/0 ile karışmasın diye kullanılmaz.
 * 1–3. hane üreticiyi (WMI), 10. hane model yılını verir.
 */
final class SaseNo
{
    /** 10. hanede kullanılan yıl kodları; I, O, Q, U, Z ve 0 yoktur. */
    private const YIL_KODLARI = 'ABCDEFGHJKLMNPRSTVWXY123456789';

    public static function temizle(?string $sase): string
    {
        return (string) preg_replace('/[^A-Z0-9]/', '', mb_strtoupper((string) $sase, 'UTF-8'));
    }

    public static function gecerliMi(?string $sase): bool
    {
        return (bool) preg_match('/^[A-HJ-NPR-Z0-9]{17}$/', self::temizle($sase));
    }

    /** Dünya üretici kodu (World Manufacturer Identifier): ilk 3 hane. */
    public static function wmi(?string $sase): string
    {
        return substr(self::temizle($sase), 0, 3);
    }

    /**
     * 10. haneden model yılı.
     *
     * Kod 30 yılda bir tekrar eder (1980–2009, 2010–2039). Gelecek yıldan
     * büyük bir sonuç çıkarsa bir önceki döngüye düşülür.
     */
    public static function modelYili(?string $sase): ?int
    {
        $kod = substr(self::temizle($sase), 9, 1);

        if ($kod === '') {
            return null;
        }

        $sira = strpos(self::YIL_KODLARI, $kod);

        if ($sira === false) {
            return null;
        }

        $yil = 2010 + $sira;

        return $yil > (int) date('Y') + 1 ? $yil - 30 : $yil;
    }

    /**
     * Kuzey Amerika'da zorunlu olan 9. hane kontrol basamağı.
     *
     * Avrupa üreticileri bu basamağı doldurmaz, bu yüzden gecerliMi()
     * içinde kullanılmaz; yalnızca açıkça istendiğinde çağır.
     */
    public static function kontrolBasamagiTutuyorMu(?string $sase): bool
    {
        $temiz = self::temizle($sase);

        if (! self::gecerliMi($temiz)) {
            return false;
        }

        $agirliklar = [8, 7, 6, 5, 4, 3, 2, 10, 0, 9, 8, 7, 6, 5, 4, 3, 2];
        $degerler = [
            'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8,
            'J' => 1, 'K' => 2, 'L' => 3, 'M' => 4, 'N' => 5, 'P' => 7, 'R' => 9,
            'S' => 2, 'T' => 3, 'U' => 4, 'V' => 5, 'W' => 6, 'X' => 7, 'Y' => 8, 'Z' => 9,
        ];

        $toplam = 0;
        foreach (str_split($temiz) as $i => $karakter) {
            $deger = ctype_digit($karakter) ? (int) $karakter : ($degerler[$karakter] ?? 0);
            $toplam += $deger * $agirliklar[$i];
        }

        $kalan = $toplam % 11;
        $beklenen = $kalan === 10 ? 'X' : (string) $kalan;

        return $temiz[8] === $beklenen;
    }

    /** "WVWZZZ3CZLE104821" → "WVWZZZ3C•••••4821" */
    public static function maskele(?string $sase): string
    {
        $temiz = self::temizle($sase);

        return strlen($temiz) === 17
            ? substr($temiz, 0, 8).'•••••'.substr($temiz, -4)
            : '•••';
    }
}
