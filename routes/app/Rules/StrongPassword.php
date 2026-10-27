<?php

namespace App\Rules;

use App\Services\ProjectPasswordService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class StrongPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $passwordService = app(ProjectPasswordService::class);
        $errors = $passwordService->validatePassword($value);

        if (! empty($errors)) {
            $fail('The :attribute must meet the following requirements: '.implode(', ', $errors));
        }
    }
}
