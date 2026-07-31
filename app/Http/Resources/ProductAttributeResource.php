<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'name' => $this->name,

            'input_type' => $this->input_type,

            'values' => ProductAttributeValueResource::collection(
                $this->whenLoaded('values')
            ),

            'created_at' => $this->created_at,

        ];
    }
}