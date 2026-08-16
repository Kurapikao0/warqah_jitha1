<?php

namespace App\Http\Requests\ProductAttributeValue;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [

            'value' => [
                'required',
                'string',
                'max:255',
            ],

        ];
    }
}
