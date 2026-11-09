<?php

declare(strict_types=1);

namespace Bektasozcetin\TurkceDogrulama\Rules;

use Bektasozcetin\TurkceDogrulama\Iban as Dogrulayici;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** IBAN doğrulaması. Dize kural adı olarak "tr_iban" da kullanılabilir. */
final class TrIban implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Dogrulayici::gecerliMi(is_scalar($value) ? (string) $value : null)) {
            $fail('turkce-dogrulama::validation.tr_iban')->translate();
        }
    }
}
