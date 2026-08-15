<?php

namespace App\Http\Requests\Auth;

use App\Enums\CustomerCategory;
use App\Rules\YemenPhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Public registration - لا نحتاج مصادقة
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:customers,email'],
            'phone_country_code' => ['required', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:20', new YemenPhoneRule(), 'unique:customers,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
            'avatar'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'الاسم الكامل مطلوب',
            'full_name.string' => 'الاسم الكامل يجب أن يكون نصاً',
            'full_name.max' => 'الاسم الكامل يجب ألا يتجاوز 255 حرف',

            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مسجل بالفعل',

            'phone_country_code.required' => 'رمز الدولة مطلوب',
            'phone_country_code.string' => 'رمز الدولة يجب أن يكون نصاً',

            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.unique' => 'رقم الهاتف مسجل بالفعل',

            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور لا يطابق',

            'password_confirmation.required' => 'تأكيد كلمة المرور مطلوب',
        ];
    }
}
