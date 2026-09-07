<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Order;

use App\Enums\OrderType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id',
            ],
            'order_type' => [
                'required',
                Rule::enum(OrderType::class),
            ],
            'expected_delivery_date' => [
                'nullable',
                'date',
            ],
            'shipping_fee' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
                'distinct',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'items.*.customization_id' => [
                'nullable',
                'integer',
                'exists:product_customization_requests,id',
            ],
            'items.*.customization_note' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'يرجى اختيار العميل.',
            'customer_id.exists' => 'العميل المحدد غير موجود.',
            'order_type.required' => 'نوع الطلب مطلوب.',
            'order_type.enum' => 'نوع الطلب المحدد غير صحيح.',
            'items.required' => 'يجب إضافة منتج واحد على الأقل.',
            'items.min' => 'يجب إضافة منتج واحد على الأقل.',
            'items.*.product_id.required' => 'معرف المنتج مطلوب.',
            'items.*.product_id.exists' => 'أحد المنتجات المحددة غير موجود.',
            'items.*.product_id.distinct' => 'لا يمكن تكرار نفس المنتج في الطلب.',
            'items.*.quantity.required' => 'كمية المنتج مطلوبة.',
            'items.*.quantity.integer' => 'كمية المنتج يجب أن تكون رقماً صحيحاً.',
            'items.*.quantity.min' => 'يجب أن تكون كمية المنتج أكبر من صفر.',
            'items.*.customization_id.exists' => 'طلب التخصيص المحدد غير موجود.',
            'shipping_fee.min' => 'رسوم الشحن لا يمكن أن تكون سالبة.',
        ];
    }
}
