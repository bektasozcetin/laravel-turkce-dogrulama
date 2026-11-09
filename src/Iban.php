<?php

declare(strict_types=1);

namespace Bektasozcetin\TurkceDogrulama;

/**
 * Türkiye IBAN'ı: TR + 2 kontrol hanesi + 5 hane banka kodu + 1 rezerv + 16 hane hesap = 26 karakter.
 *
 * Doğrulama ISO 13616 mod-97 kuralıdır: ilk dört karakter sona alınır,
 * harfler sayıya çevrilir (A=10 … Z=35) ve kalan 1 olmalıdır.
 */
final class Iban
{
    public const UZUNLUK = 26;

    public static function temizle(?string $iban): string
    {
        return (string) preg_replace('/[^A-Z0-9]/', '', mb_strtoupper((string) $iban, 'UTF-8'));
    }

    public static function gecerliMi(?string $iban): bool
    {
        $t = self::temizle($iban);

        if (strlen($t) !== self::UZUNLUK || ! str_starts_with($t, 'TR')) {
            return false;
        }

        if (! ctype_digit(substr($t, 2))) {
            return false;
        }

        return self::mod97(substr($t, 4).substr($t, 0, 4)) === 1;
    }

    /** "TR330006100519786457841326" → "TR33 0006 1005 1978 6457 8413 26" */
    public static function bicimle(?string $iban): string
    {
        return self::dortluGrupla(self::temizle($iban));
    }

    /** Banka kodu: 5. haneden başlayan 5 hane. Geçersizse null. */
    public static function bankaKodu(?string $iban): ?string
    {
        $t = self::temizle($iban);

        return self::gecerliMi($t) ? substr($t, 4, 5) : null;
    }

    /** "TR33 0006 1005 1978 6457 8413 26" → "TR33 •••• •••• •••• •••• •••• 26" */
    public static function maskele(?string $iban): string
    {
        $t = self::temizle($iban);

        if (strlen($t) !== self::UZUNLUK) {
            return '•••';
        }

        return self::dortluGrupla(substr($t, 0, 4).str_repeat('•', 20).substr($t, -2));
    }

    /**
     * Dörtlü gruplara ayırır. chunk_split() bayt sayar; maskeleme için
     * kullanılan "•" karakteri 3 bayt olduğundan karakter bazlı bölüyoruz.
     *
     * @param  string  $deger  26 karakterlik IBAN ya da maskelenmiş hâli
     */
    private static function dortluGrupla(string $deger): string
    {
        return implode(' ', array_map(
            static fn (array $parca): string => implode('', $parca),
            array_chunk(mb_str_split($deger), 4),
        ));
    }

    /**
     * Büyük sayıyı parça parça işleyerek mod 97 hesaplar;
     * bcmath/gmp olmadan da 26 haneden uzun değerlerde taşma olmaz.
     */
    private static function mod97(string $deger): int
    {
        $kalan = '';

        foreach (str_split($deger) as $karakter) {
            $kalan .= ctype_digit($karakter)
                ? $karakter
                : (string) (ord($karakter) - 55);

            while (strlen($kalan) >= 9) {
                $kalan = (string) ((int) substr($kalan, 0, 9) % 97).substr($kalan, 9);
            }
        }

        return (int) $kalan % 97;
    }
}
