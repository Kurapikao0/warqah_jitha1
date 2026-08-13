<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductMediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'product_id' => $this->product_id,

            'media_type' => $this->media_type,

            'url' => $this->url,

            'sort_order' => $this->sort_order,

            'is_primary' => $this->is_primary,

            'product' => $this->when(
                $this->relationLoaded('product'),
                fn() => new ProductResource($this->product)
            ),

            'created_at' => $this->created_at,

        ];
    }
}