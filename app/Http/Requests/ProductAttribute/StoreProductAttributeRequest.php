<?php

namespace App\Http\Requests\ProductAttribute;

use App\Enums\ProductAttributeInputType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreProductAttributeRequest extends FormRequest
{
    public function prepareForValidation(): void
    {
        $data = $this->all();

        if (! array_key_exists('name', $data) && array_key_exists('display_name', $data)) {
            $data['name'] = $data['display_name'];
        }

        if (! array_key_exists('display_name', $data) && array_key_exists('name', $data)) {
            $data['display_name'] = $data['name'];
        }

        if (! array_key_exists('input_type', $data) && array_key_exists('type', $data)) {
            $data['input_type'] = $data['type'];
        }

        if (! array_key_exists('type', $data) && array_key_exists('input_type', $data)) {
            $data['type'] = $data['input_type'];
        }

        $this->replace($data);
    }

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

            'display_name' => [
                'required',
                'string',
                'max:255',
            ],

            'input_type' => [
                'required',
                new Enum(ProductAttributeInputType::class),
            ],

            'is_required' => [
                'sometimes',
                'boolean',
            ],

            'options' => [
                'nullable',
                'array',
            ],

            'options.*.value' => [
                'required_with:options',
                'string',
                'max:255',
            ],

            'options.*.label' => [
                'required_with:options',
                'string',
                'max:255',
            ],

        ];
    }
}
