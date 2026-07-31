<?php

namespace App\Http\Requests\Payment;


use Illuminate\Foundation\Http\FormRequest;



class UpdatePaymentStatusRequest extends FormRequest
{


public function authorize(): bool
{

return auth()->check();

}




public function rules(): array
{

return [

'status'=>
'required|in:
pending,
paid,
rejected',


'admin_note'=>
'nullable|string'


];


}


}