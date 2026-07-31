<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignPatternResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'name' => $this->name,

            'description' => $this->description,

            'preview_image_url' => $this->preview_image_url,

            'created_at' => $this->created_at,

        ];
    }
}