<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code_or_token' => [
                'required',
                'string',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'email' => [
                'required',
                'email',
                'exists:customers,email',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'رابط إعادة التعيين غير صالح.',

            'password.required' => 'كلمة المرور مطلوبة.',

            'password.min' => 'يجب أن تكون كلمة المرور 8 أحرف على الأقل.',

            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            
            'email.required' => 'البريد الإلكتروني مطلوب.',

            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',

            'email.exists' => 'لا يوجد حساب بهذا البريد الإلكتروني.',
        ];
    }
}