<?php

declare(strict_types=1);

namespace Bektasozcetin\TurkceDogrulama\Rules;

use Bektasozcetin\TurkceDogrulama\VergiNo as Dogrulayici;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** Vergi kimlik numarası doğrulaması. Dize kural adı olarak "vergi_no" da kullanılabilir. */
final class VergiNo implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Dogrulayici::gecerliMi(is_scalar($value) ? (string) $value : null)) {
            $fail('turkce-dogrulama::validation.vergi_no')->translate();
        }
    }
}
