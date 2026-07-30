<?php

namespace App\Http\Requests\RawMaterial;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRawMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'product_id' => [
                'nullable',
                'exists:products,id',
            ],

            'name' => [
                'sometimes',
                'string',
                'max:255'
            ],

            'unit' => [
                'sometimes',
                'string',
                'max:50'
            ],

            'quantity_available' => [
                'sometimes',
                'numeric',
                'min:0'
            ],

            'reorder_point' => [
                'sometimes',
                'numeric',
                'min:0'
            ],

            'status' => [
                'sometimes',
                'string'
            ],
        ];
    }
}