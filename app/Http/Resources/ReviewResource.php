<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'rating' => $this->rating,

            'comment' => $this->comment,

            'status' => $this->status,

            'admin_reply' => $this->admin_reply,

            'admin_reply_at' => $this->admin_reply_at,

            'customer' => $this->whenLoaded(
                'customer'
            ),

            'product' => $this->whenLoaded(
                'product'
            ),

            'order_item' => $this->whenLoaded(
                'orderItem'
            ),

            'images' => $this->whenLoaded(
                'images'
            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}