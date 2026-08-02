<?php

namespace App\Http\Requests\Color;

use Illuminate\Foundation\Http\FormRequest;

class StoreColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                'max:100',
                'unique:colors,name'
            ],

            'hex_code' => [
                'required',
                'regex:/^#([A-Fa-f0-9]{6})$/'
            ],

        ];
    }
}