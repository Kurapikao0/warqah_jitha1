<?php

namespace App\Http\Requests\Admin\AdminUser;

use App\Rules\YemenPhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'role_id' => [
                'required',
                'exists:roles,id',
            ],

            'full_name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'unique:admin_users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
                new YemenPhoneRule,
            ],

            'avatar_url' => [
                'nullable',
                'string',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
            ],

        ];
    }
}
