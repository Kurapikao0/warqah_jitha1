<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $activeExpiresAt = $this->items
            ->whereNotNull('expires_at')
            ->pluck('expires_at')
            ->sort()
            ->first();

        $remainingSeconds = $activeExpiresAt ? max(0, (int) now()->diffInSeconds($activeExpiresAt, false)) : 0;

        return [
            'id' => $this->id,
            'items' => CartItemResource::collection($this->items),
            'expires_at' => $activeExpiresAt,
            'remaining_seconds' => $remainingSeconds,
            'is_reservation_expired' => $this->items->isNotEmpty() && ($remainingSeconds <= 0),
            'total' => $this->items->sum(function ($item) {
                return $item->product ? ($item->product->price * $item->quantity) : 0;
            }),
        ];
    }
}