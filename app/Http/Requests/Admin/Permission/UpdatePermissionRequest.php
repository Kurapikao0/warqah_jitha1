<?php

namespace App\Http\Requests\Admin\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // نحدد الـ ID سواء كان الـ Route يحمل model object أو مجرد ID رقمي
        $permissionId = $this->route('permission')?->id ?? $this->route('permission');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($permissionId),
            ],
            'module' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],
        ];
    }
}
