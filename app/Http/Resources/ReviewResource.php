<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $customer = $this->whenLoaded('customer');
        $product = $this->whenLoaded('product');

        return [
            'id'             => $this->id,
            'rating'         => $this->rating,
            'comment'        => $this->comment,
            'status'         => $this->status,
            'admin_reply'    => $this->admin_reply,
            'admin_reply_at' => $this->admin_reply_at?->toIso8601String(),
            'customer_name'  => $customer?->full_name ?? $this->customer?->full_name,
            'product_name'   => $product?->name ?? $this->product?->name,
            'customer'       => $customer ? new CustomerResource($customer) : null,
            'product'        => $product ? new ProductResource($product) : null,
            'images'         => ReviewImageResource::collection($this->whenLoaded('images')),
            'created_at'     => $this->created_at?->toIso8601String(),
        ];
    }
}
