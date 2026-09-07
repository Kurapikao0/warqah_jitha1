<?php

declare(strict_types=1);

namespace App\Http\Requests\Customization;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminCustomizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id',
            ],

            'base_product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'color_id' => [
                'nullable',
                'integer',
                'exists:colors,id',
            ],

            'design_pattern_id' => [
                'nullable',
                'integer',
                'exists:design_patterns,id',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'length_cm' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'width_cm' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'height_cm' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'customer_notes' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'attribute_values' => [
                'nullable',
                'array',
            ],

            'attribute_values.*.attribute_id' => [
                'required',
                'integer',
                'distinct',
                'exists:product_attributes,id',
            ],

            'attribute_values.*.value' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }
}
