<?php

namespace App\Http\Requests\ProductMedia;

use App\Enums\ProductMediaType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreProductMediaRequest extends FormRequest
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

            'media_type' => [
                'required',
                new Enum(ProductMediaType::class),
            ],

            'url' => [
                'required',
                'url',
                'max:255'
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'is_primary' => [
                'boolean',
            ],

        ];
    }
}
