<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RawMaterialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'product_id' => $this->product_id,

            'name' => $this->name,

            'unit' => $this->unit,

            'quantity_available' =>
                $this->quantity_available,

            'reorder_point' =>
                $this->reorder_point,

            'status' =>
                $this->status,

            'product' =>
                $this->whenLoaded('product'),

            'created_at' =>
                $this->created_at,

            'updated_at' =>
                $this->updated_at,
        ];
    }
}