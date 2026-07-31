<?php

namespace App\Http\Requests\OrderProductionStage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderProductionStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                Rule::unique('order_production_stages')
                    ->ignore($this->orderProductionStage)
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:1'
            ],

        ];
    }
}