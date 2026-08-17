<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('customer')->check();
    }

    public function rules(): array
    {

        return [
            /*'address_id'=>
        'required|exists:addresses,id',*/
            'address_id' => [
                'required',
                Rule::exists('addresses', 'id')
                    ->where(function ($query) {
                        $query->where(
                            'customer_id',
                            auth('customer')->id()
                        );
                    }),
            ],
            /*'order_type'=>
        'required|in:
        ready_made,
        custom,
        mixed',*/
            'order_type' => [
                'required',
                Rule::in([
                    'ready_made',
                    'custom',
                    'mixed',
                ]),
            ],
            /*'items'=>
        'required|array',*/
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.product_id' => [
                'required',
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
                'exists:product_customization_requests,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [

            'address_id.required' => 'يرجى اختيار عنوان الشحن.',

            'address_id.exists' => 'عنوان الشحن المحدد غير موجود أو لا ينتمي إلى حسابك.',

            'order_type.required' => 'نوع الطلب مطلوب.',

            'order_type.in' => 'نوع الطلب المحدد غير صحيح.',

            'items.required' => 'يجب إضافة منتجات إلى الطلب.',

            'items.array' => 'بيانات المنتجات غير صحيحة.',

            'items.min' => 'يجب إضافة منتج واحد على الأقل.',

            'items.*.product_id.required' => 'معرف المنتج مطلوب.',

            'items.*.product_id.exists' => 'أحد المنتجات المحددة غير موجود.',

            'items.*.product_id.distinct' => 'لا يمكن تكرار نفس المنتج في الطلب.',

            'items.*.quantity.required' => 'كمية المنتج مطلوبة.',

            'items.*.quantity.integer' => 'كمية المنتج يجب أن تكون رقماً صحيحاً.',

            'items.*.quantity.min' => 'يجب أن تكون كمية المنتج أكبر من صفر.',

            'items.*.customization_id.exists' => 'طلب التخصيص المحدد غير موجود.',

        ];
    }

    public function attributes(): array
    {
        return [

            'address_id' => 'عنوان الشحن',
            'order_type' => 'نوع الطلب',
            'items' => 'المنتجات',
            'items.*.product_id' => 'المنتج',
            'items.*.quantity' => 'الكمية',
            'items.*.customization_id' => 'التخصيص',

        ];
    }
}
