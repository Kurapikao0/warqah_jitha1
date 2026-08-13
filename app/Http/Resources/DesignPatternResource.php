<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignPatternResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $imageUrl = $this->preview_image_url;

        if ($imageUrl && ! preg_match('/^https?:\/\//i', $imageUrl)) {
            $imageUrl = asset('storage/' . $imageUrl);
        }

        return [

            'id' => $this->id,

            'name' => $this->name,

            'description' => $this->description,

            'preview_image_url' => $imageUrl,

            'image_url' => $imageUrl,

            'created_at' => $this->created_at,

        ];
    }
}
