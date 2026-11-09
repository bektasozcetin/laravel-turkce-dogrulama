<?php

declare(strict_types=1);

use Bektasozcetin\TurkceDogrulama\Telefon;

it('her girdi bicimini 90... haline getirir', function (string $girdi) {
    expect(Telefon::cep($girdi))->toBe('905321234567');
})->with(['5321234567', '05321234567', '905321234567', '+90 532 123 45 67', '0 (532) 123-45-67']);

it('sabit hat ve gecersiz numaralari reddeder', function (?string $girdi) {
    expect(Telefon::cep($girdi))->toBeNull();
})->with(['02123334455', '2123334455', '123', '', null]);

it('okunabilir bicim ve maske uretir', function () {
    expect(Telefon::bicimle('905321234567'))->toBe('0532 123 45 67')
        ->and(Telefon::maskele('905321234567'))->toBe('0532 ••• •• 67')
        ->and(Telefon::bicimle('gecersiz'))->toBe('')
        ->and(Telefon::maskele('gecersiz'))->toBe('•••');
});
