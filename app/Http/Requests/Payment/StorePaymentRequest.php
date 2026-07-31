<?php

namespace App\Http\Requests\Payment;


use Illuminate\Foundation\Http\FormRequest;



class StorePaymentRequest extends FormRequest
{


public function authorize(): bool
{
    return auth()->check();
}




public function rules(): array
{

return [


'order_id'=>
'required|exists:orders,id',


'payment_method'=>
'required|in:
jawali,
jeeb,
al_kuraimi',


'transaction_reference'=>
'required|string|max:255',


'amount'=>
'required|numeric|min:0',


'proof_image'=>
'nullable|image|max:2048'


];


}


}