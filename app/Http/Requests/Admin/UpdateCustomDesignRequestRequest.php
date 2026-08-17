<?php

namespace App\Http\Requests\Admin;

use App\Models\AdminUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomDesignRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check() && auth('admin')->user() instanceof AdminUser;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', Rule::in(['new', 'in_review', 'quoted', 'converted', 'rejected'])],
            'description' => ['nullable', 'string', 'max:4000'],
        ];
    }
}
