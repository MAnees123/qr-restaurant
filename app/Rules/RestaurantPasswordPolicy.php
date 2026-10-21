<?php

namespace App\Rules;

use App\Models\Platform\SecuritySetting;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** Enforces the restaurant's password policy (only when advanced_security is granted). */
class RestaurantPasswordPolicy implements ValidationRule
{
    public function __construct(private ?User $user) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->user || $this->user->is_super_admin || ! $this->user->restaurant_id
            || ! $this->user->restaurant?->hasFeature('advanced_security')) {
            return;
        }
        $s = SecuritySetting::forRestaurant($this->user->restaurant_id);
        $v = (string) $value;
        if (mb_strlen($v) < $s->password_min_length) $fail("The password must be at least {$s->password_min_length} characters.");
        if ($s->password_require_mixed && (! preg_match('/\p{Ll}/u', $v) || ! preg_match('/\p{Lu}/u', $v))) $fail('The password must contain upper and lower case letters.');
        if ($s->password_require_number && ! preg_match('/\d/', $v)) $fail('The password must contain a number.');
        if ($s->password_require_symbol && ! preg_match('/[^\p{L}\d]/u', $v)) $fail('The password must contain a symbol.');
    }
}
