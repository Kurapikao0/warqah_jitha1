<?php

namespace App\Http\Requests\ProductAttribute;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ProductAttributeInputType;

class UpdateProductAttributeRequest extends FormRequest
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
                Rule::unique('product_attributes')
                    ->ignore($this->productAttribute),
            ],

            'input_type' => [
                'required',
                new Enum(ProductAttributeInputType::class),
            ],

        ];
    }
}