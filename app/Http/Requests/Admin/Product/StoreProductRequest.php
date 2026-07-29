<?php

namespace App\Http\Requests\Admin\Product;


use Illuminate\Foundation\Http\FormRequest;



class StoreProductRequest extends FormRequest
{


    public function authorize(): bool
    {
        return auth()->check();
    }



    public function rules(): array
    {

        return [

            'category_id'
                =>'required|exists:product_categories,id',

            'name'
                =>'required|string|max:255',

            'sku'
                =>'required|string|unique:products,sku',

            'description'
                =>'nullable|string',

            'price'
                =>'required|numeric|min:0',

            'stock_quantity'
                =>'required|integer|min:0',

            'is_customizable'
                =>'boolean',

            'status'
                =>'required|string'

        ];

    }



    public function messages(): array
    {

        return [

            'sku.unique'
            =>'Product SKU already exists',

            'price.numeric'
            =>'Price must be numeric'

        ];

    }

}