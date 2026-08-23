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
        return [
            'id' => $this->id,

            'name' => $this->name,

            'slug' => $this->slug,

            'sku' => $this->sku,

            'category_id' => $this->category_id,

            'description' => $this->description,

            'price' => $this->price,

            'stock_quantity' => $this->stock_quantity,
            'reserved_quantity' => $this->reserved_quantity,

            'status' => $this->status,

            'is_customizable' => $this->is_customizable,

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
            'created_at' => $this->created_at,
        ];
    }
}
