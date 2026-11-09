# laravel-turkce-dogrulama

Türkiye'ye özgü doğrulama kuralları ve biçimlendiriciler: **plaka, şase (VIN), T.C. kimlik no, vergi no, IBAN ve cep telefonu.**

Her değer için üç şey var: doğrulama (`gecerliMi`), okunabilir biçim (`bicimle`) ve herkese açık sayfalarda kullanmak için maskeleme (`maskele`).

> Turkish-specific validation rules and formatters for Laravel: licence plate, VIN, national ID, tax number, IBAN and mobile phone. The API is in Turkish because the domain terms are; English validation messages ship in `lang/en`.

[![Tests](https://github.com/bektasozcetin/laravel-turkce-dogrulama/actions/workflows/tests.yml/badge.svg)](https://github.com/bektasozcetin/laravel-turkce-dogrulama/actions/workflows/tests.yml)
[![License](https://img.shields.io/badge/license-MIT-blue)](LICENSE)

## Kurulum

```bash
composer require bektasozcetin/laravel-turkce-dogrulama
```

Laravel paketi kendiliğinden keşfeder. Gerekirse mesajları yayımla:

```bash
php artisan vendor:publish --tag=turkce-dogrulama-lang
```

**Gereksinimler:** PHP 8.2+, Laravel 11 veya 12.

## Doğrulama

İki kullanım da aynı işi yapar — kural nesnesi IDE tamamlaması verir, dize kısa yazılır.

```php
use Bektasozcetin\TurkceDogrulama\Rules;

$request->validate([
    'plaka'    => ['required', new Rules\Plaka],
    'sase_no'  => ['required', new Rules\SaseNo],
    'tckn'     => ['required', new Rules\TcKimlikNo],
    'vergi_no' => ['required', new Rules\VergiNo],
    'iban'     => ['required', new Rules\TrIban],
    'telefon'  => ['required', new Rules\CepTelefonu],
]);

// ya da dize kural adlarıyla
$request->validate([
    'plaka'   => 'required|plaka',
    'telefon' => 'required|cep_telefonu',
]);
```

## Sınıflar

### Plaka

İl kodu (01–81) + 1–3 harf + 2–5 rakam.

```php
use Bektasozcetin\TurkceDogrulama\Plaka;

Plaka::gecerliMi('34klm482');   // true
Plaka::bicimle('34klm482');     // "34 KLM 482"
Plaka::anahtar('34 KLM 482');   // "34KLM482"  → veritabanında arama için
Plaka::maskele('34 KLM 482');   // "34 K•• •82"
Plaka::ilKodu('34 KLM 482');    // 34
```

`anahtar()` Türkçe `ı` harfini `I` ile eşler; `mb_strtoupper` tek başına `i` harfini `İ` yapıp deseni bozardı.

### SaseNo (VIN)

17 karakter, I/O/Q kullanılmaz. 1–3. hane üretici, 10. hane model yılı.

```php
use Bektasozcetin\TurkceDogrulama\SaseNo;

SaseNo::gecerliMi('WVWZZZ3CZLE104821');   // true
SaseNo::wmi('WVWZZZ3CZLE104821');         // "WVW"
SaseNo::modelYili('WVWZZZ3CZLE104821');   // 2020
SaseNo::maskele('WVWZZZ3CZLE104821');     // "WVWZZZ3C•••••4821"

// Kuzey Amerika'da zorunlu olan 9. hane kontrol basamağı.
// Avrupa üreticileri doldurmaz, bu yüzden gecerliMi() içinde değil:
SaseNo::kontrolBasamagiTutuyorMu('1HGBH41JXMN109186'); // true
```

Yıl kodu 30 yılda bir tekrar eder (1980–2009, 2010–2039). `modelYili()` gelecek yıldan büyük bir sonuç çıkarsa bir önceki döngüye düşer.

### TcKimlikNo

```php
use Bektasozcetin\TurkceDogrulama\TcKimlikNo;

TcKimlikNo::gecerliMi('10000000146');  // true
TcKimlikNo::maskele('10000000146');    // "100•••••146"
```

> Bu yalnızca biçim ve kontrol basamağı denetimidir. Numaranın gerçekten birine ait olduğunu söylemez — kimlik doğrulaması için NVİ servisini kullan.

### VergiNo

```php
use Bektasozcetin\TurkceDogrulama\VergiNo;

VergiNo::gecerliMi('4540536920');  // true
VergiNo::tur('10000000146');       // "tckn"  → bireysel/kurumsal ayrımı için
VergiNo::tur('4540536920');        // "vkn"
```

### Iban

```php
use Bektasozcetin\TurkceDogrulama\Iban;

Iban::gecerliMi('TR33 0006 1005 1978 6457 8413 26');  // true
Iban::bicimle('TR330006100519786457841326');          // "TR33 0006 1005 1978 6457 8413 26"
Iban::maskele('TR330006100519786457841326');          // "TR33 •••• •••• •••• •••• •••• 26"
Iban::bankaKodu('TR330006100519786457841326');        // "00061"
```

Mod-97 hesabı parça parça yapılır; `bcmath` veya `gmp` eklentisi gerekmez.

### Telefon

```php
use Bektasozcetin\TurkceDogrulama\Telefon;

Telefon::cep('0 (532) 123-45-67');  // "905321234567"  → saklama biçimi
Telefon::bicimle('905321234567');   // "0532 123 45 67"
Telefon::maskele('905321234567');   // "0532 ••• •• 67"
Telefon::cep('02123334455');        // null  → sabit hat
```

## Maskeleme hakkında

`maskele()` çıktısı hiçbir zaman girdinin aynısı değildir. Tek harfli plakalarda harf gizlenemediği için rakamlar da gizlenir (`06 A 1234` → `06 A ••34`); kural her iki bölüme birden uygulanır.

## Test

```bash
composer install
composer test
```

## Lisans

MIT. Ayrıntı için [LICENSE](LICENSE).
