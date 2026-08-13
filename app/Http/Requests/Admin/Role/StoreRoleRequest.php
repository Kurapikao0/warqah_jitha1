<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Role;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine whether the admin is authorized
     * to create a role.
     */
    public function authorize(): bool
    {
        return true; //هذا تعديلي 
        // استخدام guard الصريح أو الـ Null-safe operator يمنع Error 500
        // $user = $this->user('sanctum') ?? $this->user();

        //return $user?->can('create-roles') ?? true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */


    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'permissions' => [
                'nullable',
                'array',
            ],

            'permissions.*' => [
                'exists:permissions,id',
            ],
        ];
    }









    /** 
     * public function rules(): array
     *  {
     *   return [
     *      'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
     *    'permissions.*' => ['exists:permissions,id'],
     * ];
     * } */
}
