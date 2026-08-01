<?php

namespace App\Http\Requests\Order;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Address;


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

    Rule::exists('addresses','id')
        ->where(function($query){
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
        'mixed'
    ])
],

/*'items'=>
'required|array',*/
    'items' => [
        'required',
        'array',
        'min:1'
    ],

        'items.*.product_id' => [
            'required',
            'exists:products,id',
            'distinct'
        ],




        'items.*.quantity' => [
            'required',
            'integer',
            'min:1'
        ],

        'items.*.customization_id' => [
            'nullable',
            'exists:product_customization_requests,id'
        ],


];


}



}