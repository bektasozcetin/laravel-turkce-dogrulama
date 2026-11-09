<?php

declare(strict_types=1);

namespace Bektasozcetin\TurkceDogrulama\Rules;

use Bektasozcetin\TurkceDogrulama\SaseNo as Dogrulayici;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** Şase (VIN) numarası doğrulaması. Dize kural adı olarak "sase_no" da kullanılabilir. */
final class SaseNo implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Dogrulayici::gecerliMi(is_scalar($value) ? (string) $value : null)) {
            $fail('turkce-dogrulama::validation.sase_no')->translate();
        }
    }
}
