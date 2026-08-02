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

    public function messages(): array
    {
        return [

            'status.required' =>
                'حالة الطلب مطلوبة.',

            'status.enum' =>
                'حالة الطلب المحددة غير صحيحة.',

            'note.string' =>
                'الملاحظة يجب أن تكون نصاً.',

        ];
    }


    public function attributes(): array
    {
        return [

            'status' => 'حالة الطلب',
            'note' => 'الملاحظة',

        ];
    }
}