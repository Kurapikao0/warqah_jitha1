<?php

namespace App\Http\Requests\OrderProductionStage;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderProductionStageRequest extends FormRequest
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
                'max:255',
                'unique:order_production_stages,name',
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:1',
            ],

        ];
    }
}
