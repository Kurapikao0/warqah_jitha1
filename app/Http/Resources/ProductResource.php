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

            'sku' => $this->sku,

            'description' => $this->description,

            'price' => $this->price,

            'stock_quantity' => $this->stock_quantity,

            'status' => $this->status,

            'is_customizable' => $this->is_customizable,

            'category' => new ProductCategoryResource(
                $this->whenLoaded('category')
            ),

            'media' => ProductMediaResource::collection(
                $this->whenLoaded('media')
            ),

            'created_at' => $this->created_at,
        ];
    }
}
