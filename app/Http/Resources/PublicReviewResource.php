<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PublicReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'customer_name' => $this->customer?->full_name
                ? Str::of($this->customer->full_name)->before(' ')->toString()
                : 'عميل',
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
