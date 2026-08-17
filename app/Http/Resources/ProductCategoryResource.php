<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $imageUrl = $this->image_url;

        if ($imageUrl && ! preg_match('/^https?:\/\//i', $imageUrl)) {
            $imageUrl = asset('storage/'.$imageUrl);
        }

        return [

            'id' => $this->id,

            'name' => $this->name,

            'slug' => $this->slug,

            'image_url' => $imageUrl,

            'parent_id' => $this->parent_id,

            'children_count' => $this->whenCounted(
                'children'
            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}
