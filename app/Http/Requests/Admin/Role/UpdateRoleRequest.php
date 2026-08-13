<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Role;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine whether the authenticated admin
     * is authorized to update this role.
     */
    public function authorize(): bool
    {


        return $this->user('admin')?->can('update', $role) ?? false;
    }

    /**
     * Validation rules.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $role = $this->route('role');

        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                //  Rule::unique('roles', 'name'),
                Rule::unique('roles', 'name')
                    ->ignore($this->role->id), // هذا تعديلي 

            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'permissions' => [
                'sometimes',
                'array',
            ],

            'permissions.*' => [
                'integer',
                Rule::exists('permissions', 'id'),
            ],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Role name is required.',
            'name.unique' => 'Another role already uses this name.',
            'name.min' => 'Role name must contain at least 2 characters.',
            'name.max' => 'Role name may not exceed 100 characters.',
            'description.max' => 'Description may not exceed 1000 characters.',
            'permissions.array' => 'Permissions must be an array.',
            'permissions.*.exists' => 'One or more selected permissions are invalid.',
        ];
    }

    /**
     * Human-readable attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'role name',
            'description' => 'description',
            'permissions' => 'permissions',
        ];
    }

    /**
     * Prepare request data before validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('name')) {
            $this->merge([
                'name' => trim((string) $this->input('name')),
            ]);
        }

        if ($this->has('description') && $this->input('description') !== null) {
            $this->merge([
                'description' => trim((string) $this->input('description')),
            ]);
        }
    }
}
