<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminPasswordResetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'contact_value' => $this->contact_value,

            'expires_at' => $this->expires_at,

            'consumed_at' => $this->consumed_at,

            'created_at' => $this->created_at,

        ];
    }
}
