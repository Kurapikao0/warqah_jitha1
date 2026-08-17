<?php

namespace App\Http\Requests\OrderProduction;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [

            'stage_id' => [
                'required',
                'integer',
                'exists:order_production_stages,id',
            ],

        ];
    }

    public function messages(): array
    {
        return [

            'stage_id.required' => 'مرحلة الإنتاج مطلوبة.',

            'stage_id.integer' => 'معرف مرحلة الإنتاج يجب أن يكون رقماً.',

            'stage_id.exists' => 'مرحلة الإنتاج غير موجودة.',

        ];
    }

    public function attributes(): array
    {
        return [

            'stage_id' => 'مرحلة الإنتاج',

        ];
    }
}
