<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $availableStock = $this->available_stock ?? max(0, (int) $this->stock_quantity - (int) ($this->reserved_quantity ?? 0));

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'category_id' => $this->category_id,
            'description' => $this->description,
            'price' => $this->price,
            'compare_at_price' => $this->compare_at_price,
            'stock_quantity' => $this->stock_quantity,
            'reserved_quantity' => $this->reserved_quantity ?? 0,
            'available_stock' => $availableStock,
            'in_stock' => $availableStock > 0 && $this->status?->value !== 'inactive',
            'status' => $this->status,
            'is_customizable' => $this->is_customizable,
            'is_handmade' => $this->is_handmade,
            'is_new' => $this->is_new,
            'is_bestseller' => $this->is_bestseller,
            'is_limited_edition' => $this->is_limited_edition,
            'average_rating' => $this->average_rating,
            'reviews_count' => $this->reviews_count,
            'category' => $this->whenLoaded('category', fn () => new ProductCategoryResource($this->category)),
            'media' => $this->whenLoaded('media', fn () => ProductMediaResource::collection($this->media)),
            'attributes' => $this->whenLoaded('attributes', function () {
                return $this->attributes->map(function ($attribute) {
                    return [
                        'id' => $attribute->id,
                        'name' => $attribute->name,
                        'input_type' => $attribute->input_type,
                        'value' => $attribute->pivot->value,
                    ];
                });
            }),

            'category' => $this->whenLoaded(
                'category',
                fn () => new ProductCategoryResource($this->category)
            ),

            'media' => $this->whenLoaded(
                'media',
                fn () => ProductMediaResource::collection($this->media)
            ),

            'attributes' => $this->whenLoaded(
                'attributes',
                function () {
                    return $this->attributes->map(
                        function ($attribute): array {
                            $inputType = $attribute->input_type;

                            if ($inputType instanceof \BackedEnum) {
                                $inputType = $inputType->value;
                            }

                            return [
                                'id' => $attribute->id,
                                'name' => $attribute->name,
                                'display_name' => $attribute->display_name,
                                'input_type' => $inputType,
                                'is_required' => $attribute->is_required,
                                'options' => $attribute->options,
                                'value' => $attribute->pivot->value ?? null,
                                'attribute_value_id' => $attribute->pivot->id ?? null,
                            ];
                        }
                    );
                }
            ),

            'created_at' => $this->created_at,
        ];
    }
}