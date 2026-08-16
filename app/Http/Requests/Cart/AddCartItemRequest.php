<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class AddCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {

        return [

            'product_id' => 'required|exists:products,id',

            'quantity' => 'required|integer|min:1',

            'customization_note' => 'nullable|string',

        ];

    }
}
