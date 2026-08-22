<?php

namespace App\Http\Requests\Admin;

use App\Models\AdminUser;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomDesignRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check() && auth('admin')->user() instanceof AdminUser;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'description' => ['required', 'string', 'max:4000'],
            'status' => ['nullable', 'in:new,in_review,quoted,converted,rejected'],
        ];
    }
}
