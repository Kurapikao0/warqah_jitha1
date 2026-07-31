<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'name' => $this->name,

            'hex_code' => $this->hex_code,

            'created_at' => $this->created_at,

        ];
    }
}