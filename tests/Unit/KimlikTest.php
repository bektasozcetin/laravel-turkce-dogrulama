<?php

declare(strict_types=1);

use Bektasozcetin\TurkceDogrulama\TcKimlikNo;
use Bektasozcetin\TurkceDogrulama\VergiNo;

it('gecerli TC kimlik numarasini kabul eder', function () {
    expect(TcKimlikNo::gecerliMi('10000000146'))->toBeTrue()
        ->and(TcKimlikNo::gecerliMi('100 000 001 46'))->toBeTrue();
});

it('gecersiz TC kimlik numarasini reddeder', function (?string $no) {
    expect(TcKimlikNo::gecerliMi($no))->toBeFalse();
})->with([
    '12345678901',   // kontrol basamagi tutmuyor
    '01234567890',   // sifirla baslayamaz
    '10000000145',   // son hane yanlis
    '1000000014',    // 10 hane
    '100000001460',  // 12 hane
    '',
    null,
]);

it('TC kimlik numarasini maskeler', function () {
    expect(TcKimlikNo::maskele('10000000146'))->toBe('100•••••146')
        ->and(TcKimlikNo::maskele('kisa'))->toBe('•••');
});

it('gecerli vergi numarasini kabul eder', function (string $no) {
    expect(VergiNo::gecerliMi($no))->toBeTrue();
})->with(['4540536920', '1000000000', '1000000018', '1000000026']);

it('gecersiz vergi numarasini reddeder', function (?string $no) {
    expect(VergiNo::gecerliMi($no))->toBeFalse();
})->with(['4540536921', '123456789', '12345678901', '', null]);

it('numaranin TCKN mi VKN mi oldugunu soyler', function () {
    expect(VergiNo::tur('10000000146'))->toBe('tckn')
        ->and(VergiNo::tur('4540536920'))->toBe('vkn')
        ->and(VergiNo::tur('123'))->toBeNull();
});
