<?php

namespace App\Http\Requests\User\Profile;

use App\Rules\YemenPhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customer = $this->user();

        return [

            'full_name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'email' => [
                'sometimes',
                'email',
                Rule::unique('customers', 'email')->ignore($customer->id),
            ],

            'phone_country_code' => [
                'sometimes',
                'string',
                'max:10',
            ],

            'phone' => [
                'sometimes',
                'string',
                'max:20',
                new YemenPhoneRule,
                Rule::unique('customers', 'phone')->ignore($customer->id),
            ],

            'avatar_url' => [
                'nullable',
                'url',
            ],

        ];
    }
}
