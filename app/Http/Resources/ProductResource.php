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

            'status' => $this->status,

            'is_customizable' => $this->is_customizable,

            'category' => $this->whenLoaded('category', fn () => new ProductCategoryResource($this->category)),

            'media' => $this->whenLoaded('media', fn () => ProductMediaResource::collection($this->media)),

            'created_at' => $this->created_at,
        ];
    }
}
