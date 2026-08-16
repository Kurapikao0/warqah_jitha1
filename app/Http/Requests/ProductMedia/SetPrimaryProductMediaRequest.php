<?php

namespace App\Http\Requests\ProductMedia;

use Illuminate\Foundation\Http\FormRequest;

class SetPrimaryProductMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check() || auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'nullable',
                'exists:products,id',
            ],
        ];
    }
}
