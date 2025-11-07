<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserTypeIs implements ValidationRule
{
    private $userType;

    public function __construct(string $userType)
    {
        $this->userType = $userType;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::where('email', $value)->first();

        if (!$user) {
            $fail("No account is linked to this email");
        } else if (strtolower($user->type) !== strtolower($this->userType)) {
            $fail("This email is not registered as {$this->userType}");
        }
    }
}
