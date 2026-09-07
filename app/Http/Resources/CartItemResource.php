<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'price' => $this->product->price,
                'stock_quantity' => $this->product->stock_quantity,
                'reserved_quantity' => $this->product->reserved_quantity,
                'available_stock' => $this->product->available_stock,
                'media' => ProductMediaResource::collection($this->whenLoaded('product.media') ?? $this->product->media ?? []),
            ],
            'quantity' => $this->quantity,
            'reserved_quantity' => $this->reserved_at && ! $this->isExpired()
                ? (int) $this->quantity
                : 0,
            'customization_note' => $this->customization_note,
            'reserved_at' => $this->reserved_at,
            'expires_at' => $this->expires_at,
            'remaining_seconds' => $this->remainingSeconds(),
            'is_expired' => $this->isExpired(),
        ];
    }
}
