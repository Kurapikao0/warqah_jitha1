<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'review_id' => $this->review_id,

            'image_url' => asset(
                'storage/'.$this->image_url
            ),

            'created_at' => $this->created_at,

        ];
    }
}
