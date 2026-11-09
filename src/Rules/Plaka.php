<?php

declare(strict_types=1);

namespace Bektasozcetin\TurkceDogrulama\Rules;

use Bektasozcetin\TurkceDogrulama\Plaka as Dogrulayici;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** Plaka doğrulaması. Dize kural adı olarak "plaka" da kullanılabilir. */
final class Plaka implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Dogrulayici::gecerliMi(is_scalar($value) ? (string) $value : null)) {
            $fail('turkce-dogrulama::validation.plaka')->translate();
        }
    }
}
