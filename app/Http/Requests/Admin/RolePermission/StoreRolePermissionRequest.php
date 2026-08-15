<?php

namespace App\Http\Requests\Admin\RolePermission;

use Illuminate\Foundation\Http\FormRequest;

class StoreRolePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'permission_id' => [
                'nullable',
                'required_without:permission_ids',
                'exists:permissions,id',
            ],
            'permission_ids' => [
                'nullable',
                'required_without:permission_id',
                'array',
            ],
            'permission_ids.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ];
    }
}