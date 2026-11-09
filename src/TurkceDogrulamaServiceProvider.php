<?php

declare(strict_types=1);

namespace Bektasozcetin\TurkceDogrulama;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

/**
 * Kural nesnelerinin yanında dize kural adlarını da kaydeder:
 * 'plaka', 'sase_no', 'tc_kimlik_no', 'vergi_no', 'tr_iban', 'cep_telefonu'.
 */
final class TurkceDogrulamaServiceProvider extends ServiceProvider
{
    /** @var array<string, callable(?string): bool> */
    private const KURALLAR = [
        'plaka' => [Plaka::class, 'gecerliMi'],
        'sase_no' => [SaseNo::class, 'gecerliMi'],
        'tc_kimlik_no' => [TcKimlikNo::class, 'gecerliMi'],
        'vergi_no' => [VergiNo::class, 'gecerliMi'],
        'tr_iban' => [Iban::class, 'gecerliMi'],
        'cep_telefonu' => [Telefon::class, 'gecerliMi'],
    ];

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'turkce-dogrulama');

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/turkce-dogrulama'),
        ], 'turkce-dogrulama-lang');

        foreach (self::KURALLAR as $ad => $dogrulayici) {
            Validator::extend(
                $ad,
                fn (string $attribute, mixed $value): bool => $dogrulayici(is_scalar($value) ? (string) $value : null),
                trans("turkce-dogrulama::validation.{$ad}"),
            );
        }
    }
}
