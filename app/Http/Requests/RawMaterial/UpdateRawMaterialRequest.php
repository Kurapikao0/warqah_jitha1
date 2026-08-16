<?php

namespace App\Http\Requests\RawMaterial;

use App\Enums\RawMaterialStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRawMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // استخراج الـ ID بأمان سواء كان الـ Route يحمل Model أو مجرد ID
        $rawMaterialId = $this->route('raw_material')?->id ?? $this->route('raw_material');

        return [
            'product_id' => [
                'nullable',
                'integer',
                'exists:products,id',
                Rule::unique('raw_materials', 'product_id')
                    ->ignore($rawMaterialId)
                    ->whereNotNull('product_id'),
            ],

            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'unit' => [
                'sometimes',
                'required',
                'string',
                'max:50',
            ],

            'quantity_available' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
            ],

            'reorder_point' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
            ],

            'status' => [
                'sometimes',
                'required',
                Rule::enum(RawMaterialStatus::class),
            ],
        ];
    }
}
