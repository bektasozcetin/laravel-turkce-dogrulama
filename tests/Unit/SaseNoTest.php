<?php

declare(strict_types=1);

use Bektasozcetin\TurkceDogrulama\SaseNo;

it('gecerli sase numaralarini kabul eder', function (string $sase) {
    expect(SaseNo::gecerliMi($sase))->toBeTrue();
})->with(['WVWZZZ3CZLE104821', '1HGBH41JXMN109186', 'wvwzzz3czle104821']);

it('I O Q harflerini ve yanlis uzunlugu reddeder', function (?string $sase) {
    expect(SaseNo::gecerliMi($sase))->toBeFalse();
})->with(['WVWZZZ3CZIE104821', 'WVWZZZ3CZOE104821', 'WVWZZZ3CZQE104821', 'WVWZZZ3CZLE10482', '', null]);

it('uretici kodunu verir', function () {
    expect(SaseNo::wmi('WVWZZZ3CZLE104821'))->toBe('WVW');
});

it('model yilini 10. haneden cozer', function () {
    expect(SaseNo::modelYili('WVWZZZ3CZLE104821'))->toBe(2020)  // L
        ->and(SaseNo::modelYili('1HGBH41JXMN109186'))->toBe(2021); // M
});

it('gelecekteki yil icin bir onceki donguye duser', function () {
    // 10. hane "Y" => 2000 + ... ; bugunden cok ileri bir yil donmemeli
    expect(SaseNo::modelYili('WVWZZZ3CZYE104821'))->toBeLessThanOrEqual((int) date('Y') + 1);
});

it('gecersiz yil kodunda null doner', function () {
    expect(SaseNo::modelYili('kisa'))->toBeNull();
});

it('sase numarasini maskeler', function () {
    expect(SaseNo::maskele('WVWZZZ3CZLE104821'))->toBe('WVWZZZ3C•••••4821')
        ->and(SaseNo::maskele('kisa'))->toBe('•••');
});
