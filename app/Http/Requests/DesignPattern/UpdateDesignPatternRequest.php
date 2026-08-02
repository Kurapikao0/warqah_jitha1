<?php

namespace App\Http\Requests\DesignPattern;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDesignPatternRequest extends FormRequest
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
                Rule::unique('design_patterns')
                    ->ignore($this->designPattern)
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