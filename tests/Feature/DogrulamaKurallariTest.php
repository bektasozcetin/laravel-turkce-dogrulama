<?php

declare(strict_types=1);

use Bektasozcetin\TurkceDogrulama\Rules;
use Illuminate\Support\Facades\Validator;

it('dize kural adlariyla calisir', function (string $kural, string $gecerli, string $gecersiz) {
    expect(Validator::make(['alan' => $gecerli], ['alan' => $kural])->passes())->toBeTrue()
        ->and(Validator::make(['alan' => $gecersiz], ['alan' => $kural])->passes())->toBeFalse();
})->with([
    ['plaka', '34 KLM 482', '82 ABC 123'],
    ['sase_no', 'WVWZZZ3CZLE104821', 'WVWZZZ3CZIE104821'],
    ['tc_kimlik_no', '10000000146', '12345678901'],
    ['vergi_no', '4540536920', '4540536921'],
    ['tr_iban', 'TR330006100519786457841326', 'TR330006100519786457841327'],
    ['cep_telefonu', '05321234567', '02123334455'],
]);

it('kural nesneleriyle calisir', function (string $sinif, string $gecerli, string $gecersiz) {
    $kural = new $sinif;

    expect(Validator::make(['alan' => $gecerli], ['alan' => [$kural]])->passes())->toBeTrue()
        ->and(Validator::make(['alan' => $gecersiz], ['alan' => [$kural]])->passes())->toBeFalse();
})->with([
    [Rules\Plaka::class, '34 KLM 482', '82 ABC 123'],
    [Rules\SaseNo::class, 'WVWZZZ3CZLE104821', 'WVWZZZ3CZIE104821'],
    [Rules\TcKimlikNo::class, '10000000146', '12345678901'],
    [Rules\VergiNo::class, '4540536920', '4540536921'],
    [Rules\TrIban::class, 'TR330006100519786457841326', 'TR330006100519786457841327'],
    [Rules\CepTelefonu::class, '05321234567', '02123334455'],
]);

it('turkce hata mesaji dondurur', function () {
    app()->setLocale('tr');

    $dogrulayici = Validator::make(['plaka' => 'gecersiz'], ['plaka' => [new Rules\Plaka]]);

    expect($dogrulayici->passes())->toBeFalse()
        ->and($dogrulayici->errors()->first('plaka'))->toContain('geçerli bir plaka');
});

it('dizi gibi skaler olmayan degerleri reddeder', function () {
    expect(Validator::make(['alan' => ['x']], ['alan' => [new Rules\Plaka]])->passes())->toBeFalse();
});
