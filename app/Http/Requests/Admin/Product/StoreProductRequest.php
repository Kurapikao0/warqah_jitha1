<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use App\Enums\ProductStatus;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    protected function prepareForValidation(): void
    {
        if (empty($this->slug) && ! empty($this->name)) {
            $baseSlug = Str::slug($this->name);
            if (empty($baseSlug)) {
                $baseSlug = 'prd-'.strtolower(Str::random(6));
            } else {
                $baseSlug = $baseSlug.'-'.strtolower(Str::random(4));
            }
            $this->merge([
                'slug' => $baseSlug,
            ]);
        }
    }

    public function rules(): array
    {

        return [

            'category_id' => 'required|exists:product_categories,id',

            'name' => 'required|string|max:255',

            'slug' => 'required|string|max:255|unique:products,slug',

            'sku' => 'required|string|unique:products,sku',

            'description' => 'nullable|string',

            'price' => 'required|numeric|min:0',

            'stock_quantity' => 'required|integer|min:0',

            'is_customizable' => 'boolean',

            'status' => ['required', Rule::enum(ProductStatus::class)],
        ];

    }

    public function messages(): array
    {

        return [

            'sku.unique' => 'Product SKU already exists',

            'price.numeric' => 'Price must be numeric',

        ];

    }
}
