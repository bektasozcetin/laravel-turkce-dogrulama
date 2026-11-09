<?php

declare(strict_types=1);

use Bektasozcetin\TurkceDogrulama\Plaka;

it('gecerli plakalari kabul eder', function (string $plaka) {
    expect(Plaka::gecerliMi($plaka))->toBeTrue();
})->with(['34 KLM 482', '34klm482', '06 A 1234', '81 ABC 12345', '01-B-99']);

it('gecersiz plakalari reddeder', function (?string $plaka) {
    expect(Plaka::gecerliMi($plaka))->toBeFalse();
})->with(['82 ABC 123', '00 A 123', '34 KLMN 482', '34 KLM 1', '34 KLM 123456', 'abc', '', null]);

it('plakayi bicimler', function () {
    expect(Plaka::bicimle('34klm482'))->toBe('34 KLM 482')
        ->and(Plaka::bicimle(' 06-a-1234 '))->toBe('06 A 1234');
});

it('gecersiz plakada temizlenmis hali doner', function () {
    expect(Plaka::bicimle('abc'))->toBe('ABC');
});

it('turkce i harfini dogru buyutur', function () {
    expect(Plaka::anahtar('34 ıhk 482'))->toBe('34IHK482')
        ->and(Plaka::gecerliMi('34 ıhk 482'))->toBeTrue();
});

it('maskelerken girdiyi aynen geri vermez', function (string $plaka) {
    expect(Plaka::maskele($plaka))->not->toBe(Plaka::bicimle($plaka))->toContain('•');
})->with(['34 KLM 482', '06 A 1234', '81 ABC 12345']);

it('gecersiz plakayi tamamen maskeler', function () {
    expect(Plaka::maskele('abc'))->toBe('•••');
});

it('il kodunu cikarir', function () {
    expect(Plaka::ilKodu('34 KLM 482'))->toBe(34)
        ->and(Plaka::ilKodu('06 A 1234'))->toBe(6)
        ->and(Plaka::ilKodu('gecersiz'))->toBeNull();
});
