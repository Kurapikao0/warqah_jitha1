<?php

namespace App\Http\Requests\Admin\Product;


use Illuminate\Foundation\Http\FormRequest;


class UpdateProductRequest extends FormRequest
{


public function authorize(): bool
{
    return auth()->check();
}



public function rules(): array
{

return [

'name'
=>'sometimes|string|max:255',


'sku'
=>'sometimes|string|unique:products,sku,'.$this->product->id,


'price'
=>'sometimes|numeric|min:0',


'stock_quantity'
=>'sometimes|integer|min:0',


'status'
=>'sometimes|string'

];


}


}