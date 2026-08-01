<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\OrderStatus;

class UpdateOrderStatusRequest extends FormRequest
{

    public function authorize(): bool
    {
        return auth('admin')->check();
    }


    public function rules(): array
    {
        return [

            'status' => [
                'required',
                Rule::enum(OrderStatus::class),
            ],

            'note' => [
                'nullable',
                'string'
            ],

        ];
    }
}