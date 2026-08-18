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

            'display_name' => $this->display_name,

            'input_type' => $inputType,

            'values' => ProductAttributeValueResource::collection(
                $this->whenLoaded('values')
            ),
            'is_required' => $this->is_required,

        'options' => $this->options,

            'created_at' => $this->created_at,

        ];
    }
}
