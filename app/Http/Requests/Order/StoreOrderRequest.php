<?php

namespace App\Http\Requests\Order;


use Illuminate\Foundation\Http\FormRequest;



class StoreOrderRequest extends FormRequest
{


public function authorize(): bool
{
return auth()->check();
}




public function rules(): array
{

return [

'address_id'=>
'required|exists:addresses,id',


'order_type'=>
'required|in:
ready_made,
custom,
mixed',


'items'=>
'required|array',


'items.*.product_id'=>
'required|exists:products,id',


'items.*.quantity'=>
'required|integer|min:1',


'items.*.customization_id'=>
'nullable|exists:product_customization_requests,id'


];


}



}