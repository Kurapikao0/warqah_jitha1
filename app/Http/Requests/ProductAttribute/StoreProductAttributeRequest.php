<?php

namespace App\Http\Requests\ProductAttribute;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ProductAttributeInputType;

class StoreProductAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                'max:255',
                'unique:product_attributes,name',
            ],

            'input_type' => [
                'required',
                new Enum(ProductAttributeInputType::class),
            ],

        ];
    }
}