<?php

namespace App\Http\Requests\Customization;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomizationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {

        return [
            'status' => 'required|in:pending_approval,in_production,completed',
        ];

    }
}
