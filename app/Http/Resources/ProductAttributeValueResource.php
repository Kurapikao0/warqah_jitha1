<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeValueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'value' => $this->value,

            'product' => new ProductResource(
                $this->whenLoaded('product')
            ),

            'attribute' => new ProductAttributeResource(
                $this->whenLoaded('attribute')
            ),

        ];
    }
}
