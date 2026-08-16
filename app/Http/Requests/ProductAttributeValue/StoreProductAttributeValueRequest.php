<?php

namespace App\Http\Requests\ProductAttributeValue;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [

            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'attribute_id' => [
                'required',
                'exists:product_attributes,id',
            ],

            'value' => [
                'required',
                'string',
                'max:255',
            ],

        ];
    }
}
