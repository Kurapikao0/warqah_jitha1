<?php

namespace App\Http\Requests\Admin\ProductCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $category = $this->route('category') ?? $this->route('product_category');
        $categoryId = is_object($category) ? $category->id : $category;

        return [
            'name' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'slug' => [
                'sometimes',
                'string',
                'max:120',
                Rule::unique('product_categories', 'slug')->ignore($categoryId),
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],

            'image_url' => [
                'nullable',
                'string',
                'max:255',
            ],

            'parent_id' => [
                'nullable',
                'integer',
                'exists:product_categories,id',
            ],
        ];
    }
}
