<?php

namespace App\Http\Requests\Customer;

use App\Enums\VerificationPurpose;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class VerifyVerificationCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'purpose' => [
                'required',
                new Enum(VerificationPurpose::class),
            ],

            'contact_value' => [
                'required',
                'string',
                'max:255',
            ],

            'code_or_token' => [
                'required',
                'string',
                'max:255',
            ],

        ];
    }
}
