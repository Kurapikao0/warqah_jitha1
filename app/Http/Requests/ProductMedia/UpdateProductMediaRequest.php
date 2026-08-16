<?php

namespace App\Http\Requests\ProductMedia;

use App\Enums\ProductMediaType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateProductMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [

            'media_type' => [
                'sometimes',
                new Enum(ProductMediaType::class),
            ],

            'url' => [
                'sometimes',
                'url',
            ],

            'sort_order' => [
                'sometimes',
                'integer',
                'min:1',
            ],

            'is_primary' => [
                'sometimes',
                'boolean',
            ],

        ];
    }
}
