<?php

declare(strict_types=1);

namespace Bektasozcetin\TurkceDogrulama\Rules;

use Bektasozcetin\TurkceDogrulama\Telefon as Dogrulayici;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** Cep telefonu doğrulaması. Dize kural adı olarak "cep_telefonu" da kullanılabilir. */
final class CepTelefonu implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Dogrulayici::gecerliMi(is_scalar($value) ? (string) $value : null)) {
            $fail('turkce-dogrulama::validation.cep_telefonu')->translate();
        }
    }
}
