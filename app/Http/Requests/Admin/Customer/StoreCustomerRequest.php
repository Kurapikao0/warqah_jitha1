<?php

namespace App\Http\Requests\Admin\Customer;

use App\Enums\CustomerCategory;
use App\Rules\YemenPhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // تأكد من أنك Admin User
        return auth('sanctum')->check() &&
                auth('sanctum')->user() instanceof \App\Models\AdminUser;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:customers,email'],
            'phone_country_code' => ['required', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:20', new YemenPhoneRule(), 'unique:customers,phone'],
            'password' => ['required', 'string', 'min:8'],
            'category' => ['required', Rule::enum(CustomerCategory::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'البريد الإلكتروني موجود بالفعل',
            'phone.unique' => 'رقم الهاتف موجود بالفعل',
            'category.enum' => 'فئة العميل غير صحيحة',
        ];
    }
}
