<?php

namespace App\Http\Requests\Admin\AdminUser;

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
                    $this->adminUser
                ),
            ],


            'phone' => [
                'nullable',
                'string',
                'max:50',
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