<?php

namespace App\Http\Requests\ProductMedia;

use Illuminate\Foundation\Http\FormRequest;

class ReorderProductMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check() || auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'exists:products,id'
            ],
            'orderedIds' => [
                'nullable',
                'array'
            ],
            'orderedIds.*' => [
                'integer',
                'exists:product_media,id'
            ],
            'ordered_ids' => [
                'nullable',
                'array'
            ],
            'ordered_ids.*' => [
                'integer',
                'exists:product_media,id'
            ],
        ];
    }
}
