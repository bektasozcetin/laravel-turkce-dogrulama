<?php

declare(strict_types=1);

namespace Bektasozcetin\TurkceDogrulama\Rules;

use Bektasozcetin\TurkceDogrulama\TcKimlikNo as Dogrulayici;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** T.C. kimlik numarası doğrulaması. Dize kural adı olarak "tc_kimlik_no" da kullanılabilir. */
final class TcKimlikNo implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Dogrulayici::gecerliMi(is_scalar($value) ? (string) $value : null)) {
            $fail('turkce-dogrulama::validation.tc_kimlik_no')->translate();
        }
    }
}
