<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class YemenPhoneRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) && !is_numeric($value)) {
            $fail('رقم الهاتف يجب أن يكون نصاً أو أرقاماً.');
            return;
        }

        $phone = trim((string) $value);

        // Check if contains only digits
        if (!preg_match('/^\d+$/', $phone)) {
            $fail('رقم الهاتف يجب أن يتكون من أرقام فقط.');
            return;
        }

        // Check length based on database varchar(20) and local canonical phone format (7 to 15 digits)
        if (strlen($phone) !== 9) {
            $fail('رقم الهاتف غير صحيح، يجب أن يتكون من 9 أرقام.');
            return;
        }
    }
}
