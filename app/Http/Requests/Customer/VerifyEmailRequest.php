<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'contact_value' => [
                'required',
                'email',
            ],

            'code_or_token' => [
                'required',
                'string',
                'size:6',
            ],

        ];
    }
}
