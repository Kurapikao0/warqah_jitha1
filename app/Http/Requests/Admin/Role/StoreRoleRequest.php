<?php

namespace App\Http\Requests\Admin\Role;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
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
