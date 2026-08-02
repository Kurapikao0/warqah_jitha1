<?php

namespace App\Http\Requests\Customization;


use Illuminate\Foundation\Http\FormRequest;



class StoreCustomizationRequest extends FormRequest
{


public function authorize(): bool
{
    return auth()->check();
}



public function rules(): array
{


return [

'base_product_id'=>
'required|exists:products,id',


'color_id'=>
'nullable|exists:colors,id',


'design_pattern_id'=>
'nullable|exists:design_patterns,id',


'quantity'=>
'required|integer|min:1',


'length_cm'=>
'nullable|numeric',


'width_cm'=>
'nullable|numeric',


'height_cm'=>
'nullable|numeric',


'customer_notes'=>
'nullable|string|max:1000'


];


}



}