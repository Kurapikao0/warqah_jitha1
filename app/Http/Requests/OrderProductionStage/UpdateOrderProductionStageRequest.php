<?php

namespace App\Http\Requests\OrderProductionStage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderProductionStageRequest extends FormRequest
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
                Rule::unique('order_production_stages')
                    ->ignore($this->orderProductionStage),
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:1',
            ],

        ];
    }

    public function messages(): array
    {
        return [

            'name.required' => 'اسم مرحلة الإنتاج مطلوب.',

            'sort_order.required' => 'ترتيب مرحلة الإنتاج مطلوب.',

            'sort_order.integer' => 'ترتيب المرحلة يجب أن يكون رقماً.',

            'sort_order.min' => 'ترتيب المرحلة يجب أن يكون أكبر من صفر.',

        ];
    }

    public function attributes(): array
    {
        return [

            'name' => 'اسم المرحلة',

            'sort_order' => 'ترتيب المرحلة',

        ];
    }
}
