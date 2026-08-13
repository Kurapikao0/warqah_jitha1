<?php

namespace App\Http\Requests\DesignPattern;

use Illuminate\Foundation\Http\FormRequest;

class StoreDesignPatternRequest extends FormRequest
{
    public function prepareForValidation(): void
    {
        $data = $this->all();

        if (! array_key_exists('preview_image_url', $data) && array_key_exists('image_url', $data)) {
            $data['preview_image_url'] = $data['image_url'];
        }

        if (! array_key_exists('image_url', $data) && array_key_exists('preview_image_url', $data)) {
            $data['image_url'] = $data['preview_image_url'];
        }

        $this->replace($data);
    }

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

            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],

            'preview_image_url' => [
                'nullable',
                'string',
                'url',
            ],

            'image_url' => [
                'nullable',
                'string',
                'url',
            ],

        ];
    }
}
