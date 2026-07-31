<?php

namespace App\Http\Requests\DesignPattern;

use Illuminate\Foundation\Http\FormRequest;

class StoreDesignPatternRequest extends FormRequest
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
                'max:255',
                'unique:design_patterns,name'
            ],

            'description' => [
                'nullable',
                'string'
            ],

            'preview_image_url' => [
                'nullable',
                'url'
            ],

        ];
    }
}