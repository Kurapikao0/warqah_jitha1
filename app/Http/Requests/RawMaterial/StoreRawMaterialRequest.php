<?php

namespace App\Http\Requests\RawMaterial;

use App\Enums\RawMaterialStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRawMaterialRequest extends FormRequest
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
                'integer',
                'exists:products,id',
                Rule::unique('raw_materials', 'product_id')
                    ->whereNotNull('product_id'),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'unit' => [
                'required',
                'string',
                'max:50',
            ],

            'quantity_available' => [
                'required',
                'numeric',
                'min:0',
            ],

            'reorder_point' => [
                'required',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                Rule::enum(RawMaterialStatus::class),
            ],
        ];
    }
}
