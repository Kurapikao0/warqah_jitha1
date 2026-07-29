<?php

namespace App\Http\Requests\Order;


use Illuminate\Foundation\Http\FormRequest;



class UpdateOrderStatusRequest extends FormRequest
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
received,
in_production,
in_transit,
cancelled',


'note'=>
'nullable|string'

];


}


}