<?php

namespace App\Http\Requests\Address;

use App\Rules\YemenPhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [

            'recipient_name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'phone' => [
                'sometimes',
                'string',
                'max:20',
                new YemenPhoneRule,
            ],

            'country' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'city' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'district' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'street' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'is_default' => [
                'nullable',
                'boolean',
            ],

        ];

    }
}
