<?php

namespace App\Http\Requests\Admin\AdminUser;

use App\Rules\YemenPhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $adminUser = $this->route('adminUser') ?? $this->route('admin_user');

        return [

            'role_id' => [
                'sometimes',
                'exists:roles,id',
            ],


            'full_name' => [
                'sometimes',
                'string',
                'max:255',
            ],


            'email' => [
                'sometimes',
                'email',
                Rule::unique(
                    'admin_users',
                    'email'
                )->ignore(
                    $adminUser
                ),
            ],


            'phone' => [
                'nullable',
                'string',
                'max:20',
                new YemenPhoneRule(),
                Rule::unique(
                    'admin_users',
                    'phone'
                )->ignore(
                    $adminUser
                ),
            ],


            'avatar_url' => [
                'nullable',
                'string',
            ],


            'password' => [
                'nullable',
                'string',
                'min:8',
            ],

        ];
    }
}