<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\PaymentStatus;
use Illuminate\Validation\Rules\Enum;

class UpdatePaymentStatusRequest extends FormRequest
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
                new Enum(PaymentStatus::class),
            ],
            'admin_note' => [
                'nullable',
                'string'
            ],
        ];
    }
}