<?php

declare(strict_types=1);

namespace App\Http\Requests\OrderProductionStage;

use Illuminate\Foundation\Http\FormRequest;

class ReorderOrderProductionStagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'stage_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'stage_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:order_production_stages,id',
            ],
        ];
    }
}
