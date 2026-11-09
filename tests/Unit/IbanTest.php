<?php

declare(strict_types=1);

use Bektasozcetin\TurkceDogrulama\Iban;

const GECERLI = 'TR330006100519786457841326';

it('gecerli IBAN kabul eder', function (string $iban) {
    expect(Iban::gecerliMi($iban))->toBeTrue();
})->with([GECERLI, 'TR33 0006 1005 1978 6457 8413 26', 'tr33 0006 1005 1978 6457 8413 26']);

it('gecersiz IBAN reddeder', function (?string $iban) {
    expect(Iban::gecerliMi($iban))->toBeFalse();
})->with([
    'TR330006100519786457841327',  // kontrol hanesi tutmuyor
    'DE89370400440532013000',      // TR degil
    'TR3300061005197864578413',    // kisa
    'TR33000610051978645784132A',  // harf icermez
    '',
    null,
]);

it('IBAN bicimler ve maskeler', function () {
    expect(Iban::bicimle(GECERLI))->toBe('TR33 0006 1005 1978 6457 8413 26')
        ->and(Iban::maskele(GECERLI))->toBe('TR33 •••• •••• •••• •••• •••• 26')
        ->and(Iban::maskele('kisa'))->toBe('•••');
});

it('banka kodunu verir', function () {
    expect(Iban::bankaKodu(GECERLI))->toBe('00061')
        ->and(Iban::bankaKodu('gecersiz'))->toBeNull();
});
