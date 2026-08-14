<?php

namespace App\Http\Requests\Admin\Customer;

use App\Enums\CustomerCategory;
use App\Rules\YemenPhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('sanctum')->check() && 
               auth('sanctum')->user() instanceof \App\Models\AdminUser;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')->id;

        return [
            'full_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('customers', 'email')->ignore($customerId)],
            'phone_country_code' => ['sometimes', 'string', 'max:10'],
            'phone' => ['sometimes', 'string', 'max:20', new YemenPhoneRule(), Rule::unique('customers', 'phone')->ignore($customerId)],
            'password' => ['sometimes', 'string', 'min:8'], // اختياري عند التحديث
            'category' => ['sometimes', Rule::enum(CustomerCategory::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل',
            'category.enum' => 'فئة العميل غير صحيحة',
        ];
    }
}