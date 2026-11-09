<?php

declare(strict_types=1);

namespace Bektasozcetin\TurkceDogrulama;

/**
 * Türkiye telefon numarası biçimlendirme.
 *
 * Cep numaraları uluslararası biçime çevrilir: 905321234567.
 * Sabit hat ve geçersiz uzunluktaki numaralar için cep() null döner.
 */
final class Telefon
{
    /**
     * Kabul edilen girdiler: 5321234567, 05321234567, 905321234567,
     * +90 532 123 45 67, 0 (532) 123-45-67 …
     */
    public static function cep(?string $telefon): ?string
    {
        $rakam = self::normalize($telefon);

        return $rakam !== null && $rakam[2] === '5' ? $rakam : null;
    }

    public static function gecerliMi(?string $telefon): bool
    {
        return self::cep($telefon) !== null;
    }

    /** "905321234567" → "0532 123 45 67"; geçersizse boş dize. */
    public static function bicimle(?string $telefon): string
    {
        $rakam = self::cep($telefon);

        if ($rakam === null) {
            return '';
        }

        $yerel = substr($rakam, 2); // 5321234567

        return sprintf('0%s %s %s %s',
            substr($yerel, 0, 3),
            substr($yerel, 3, 3),
            substr($yerel, 6, 2),
            substr($yerel, 8, 2),
        );
    }

    /** "905321234567" → "0532 ••• •• 67" */
    public static function maskele(?string $telefon): string
    {
        $rakam = self::cep($telefon);

        if ($rakam === null) {
            return '•••';
        }

        $yerel = substr($rakam, 2);

        return sprintf('0%s ••• •• %s', substr($yerel, 0, 3), substr($yerel, 8, 2));
    }

    /** Rakamları ayıklar ve 12 haneli 90… biçimine getirir. */
    private static function normalize(?string $telefon): ?string
    {
        $rakam = (string) preg_replace('/\D/', '', (string) $telefon);

        return match (true) {
            strlen($rakam) === 12 && str_starts_with($rakam, '90') => $rakam,
            strlen($rakam) === 11 && str_starts_with($rakam, '0') => '9'.$rakam,
            strlen($rakam) === 10 => '90'.$rakam,
            default => null,
        };
    }
}
