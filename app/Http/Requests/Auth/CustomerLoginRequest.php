<?php

namespace App\Http\Requests\Auth;

use App\Rules\YemenPhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class CustomerLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone'    => ['required', 'string', 'max:20', new YemenPhoneRule()],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required'    => 'رقم الهاتف مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
        ];
    }
}