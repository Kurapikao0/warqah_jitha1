<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomDesignRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer' => [
                'id' => $this->customer?->id,
                'full_name' => $this->customer?->full_name,
            ],
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'images' => $this->whenLoaded(
                'images',
                fn () => $this->images->map(
                    fn ($image) => [
                        'id' => $image->id,
                        'url' => $image->url,
                        'sort_order' => $image->sort_order,
                    ]
                )->values()
            ),
        ];
    }
}
