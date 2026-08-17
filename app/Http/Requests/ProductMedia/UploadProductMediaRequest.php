<?php

namespace App\Http\Requests\ProductMedia;

use Illuminate\Foundation\Http\FormRequest;

class UploadProductMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check() || auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'exists:products,id',
            ],
            'media' => [
                'required',
                'array',
            ],
            'media.*' => [
                'file',
                'mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi',
                'max:20480', // Max 20MB
            ],
        ];
    }
}
