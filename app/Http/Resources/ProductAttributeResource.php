<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $inputType = $this->input_type;

        if ($inputType instanceof \BackedEnum) {
            $inputType = $inputType->value;
        }

        return [

            'id' => $this->id,

            'name' => $this->name,

            'display_name' => $this->name,

            'input_type' => $inputType,

            'type' => $inputType,

            'values' => ProductAttributeValueResource::collection(
                $this->whenLoaded('values')
            ),

            'created_at' => $this->created_at,

        ];
    }
}
