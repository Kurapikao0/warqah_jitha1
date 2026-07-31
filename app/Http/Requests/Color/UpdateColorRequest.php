<?php

namespace App\Http\Requests\Color;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateColorRequest extends FormRequest
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
                Rule::unique('colors')
                    ->ignore($this->color)
            ],

            'hex_code' => [
                'required',
                'regex:/^#([A-Fa-f0-9]{6})$/'
            ],

        ];
    }
}