<?php

namespace App\Rules;

use App\Actions\AuthActions;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ExistingEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $authActions = new AuthActions;
        
        $isValid = $authActions->validateEmail($value);

        if (!$isValid) {
            $fail("The email adress does not exist or is invalid");
        }
    }
}
