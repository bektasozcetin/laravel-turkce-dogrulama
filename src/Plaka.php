<?php

declare(strict_types=1);

namespace Bektasozcetin\TurkceDogrulama;

/**
 * Türkiye plakası: il kodu (01–81) + 1–3 harf + 2–5 rakam.
 *
 * Saklama biçimi "34 KLM 482", arama anahtarı "34KLM482".
 */
final class Plaka
{
    private const DESEN = '/^(0[1-9]|[1-7]\d|8[01])([A-Z]{1,3})(\d{2,5})$/';

    /**
     * Boşluk, tire ve küçük/büyük harf farkı olmadan karşılaştırma anahtarı.
     *
     * Türkçe "ı" harfi İngilizce "I" ile eşlenir; aksi hâlde mb_strtoupper
     * "i" harfini "İ" yapar ve desen tutmaz.
     */
    public static function anahtar(?string $plaka): string
    {
        $buyuk = mb_strtoupper(strtr((string) $plaka, ['i' => 'I', 'ı' => 'I']), 'UTF-8');

        return (string) preg_replace('/[^A-Z0-9]/', '', $buyuk);
    }

    public static function gecerliMi(?string $plaka): bool
    {
        return (bool) preg_match(self::DESEN, self::anahtar($plaka));
    }

    /**
     * "34klm482" → "34 KLM 482". Geçersizse temizlenmiş hâli döner.
     */
    public static function bicimle(?string $plaka): string
    {
        $temiz = self::anahtar($plaka);

        return preg_match(self::DESEN, $temiz, $p) ? "{$p[1]} {$p[2]} {$p[3]}" : $temiz;
    }

    /**
     * Herkese açık sayfalar için: "34 KLM 482" → "34 K•• •82".
     *
     * İl kodu, ilk harf ve son iki rakam görünür kalır. Tek harfli plakalarda
     * harf gizlenemeyeceği için rakamların gizlenmesi şart; bu yüzden kural
     * her iki bölüme de uygulanır ve çıktı hiçbir zaman girdinin aynısı olmaz.
     */
    public static function maskele(?string $plaka): string
    {
        $temiz = self::anahtar($plaka);

        if (! preg_match(self::DESEN, $temiz, $p)) {
            return '•••';
        }

        $harf = mb_substr($p[2], 0, 1).str_repeat('•', mb_strlen($p[2]) - 1);
        $rakam = str_repeat('•', max(0, strlen($p[3]) - 2)).substr($p[3], -2);

        return "{$p[1]} {$harf} {$rakam}";
    }

    /**
     * Plakanın il kodu: "34 KLM 482" → 34. Geçersizse null.
     */
    public static function ilKodu(?string $plaka): ?int
    {
        return preg_match(self::DESEN, self::anahtar($plaka), $p) ? (int) $p[1] : null;
    }
}
