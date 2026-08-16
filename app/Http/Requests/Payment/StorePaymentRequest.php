<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('customer')->check();
    }

    public function rules(): array
    {
        return [
            'order_id' => [
                'required',
                Rule::exists('orders', 'id')
                    ->where(function ($query) {
                        $query->where(
                            'customer_id',
                            auth('customer')->id()
                        );
                    }),
            ],
            'payment_method' => [
            'required',
            'in:jawali,jeeb,al_kuraimi',
        ],

            /*'payment_method'=>
'required|in:
jawali,
jeeb,
al_kuraimi',


'transaction_reference'=>
'required|string|max:255',*/

            /*'amount'=>
'required|numeric|min:0',*/

            /*'proof_image'=>
'nullable|image|max:2048'*/

        ];

    }

    public function messages(): array
    {
        return [

            'order_id.required' => 'الطلب مطلوب.',

            'order_id.exists' => 'الطلب المحدد غير موجود أو لا ينتمي إلى حسابك.',

            'payment_method.required' => 'طريقة الدفع مطلوبة.',

            'payment_method.in' => 'طريقة الدفع المحددة غير مدعومة.',

        ];
    }

    public function attributes(): array
    {
        return [

            'order_id' => 'الطلب',
            'payment_method' => 'طريقة الدفع',

        ];
    }
}
