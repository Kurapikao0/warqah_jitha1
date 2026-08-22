<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCustomizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'request_code' => $this->request_code,

            'product' => [
                'id' => $this->baseProduct?->id,
                'name' => $this->baseProduct?->name,
            ],

            'color' => $this->color?->name,

            'design_pattern' => $this->designPattern?->name,

            'quantity' => $this->quantity,

            'dimensions' => [
                'length' => $this->length_cm,
                'width' => $this->width_cm,
                'height' => $this->height_cm,
            ],

            'attributes' => $this->whenLoaded(
                'attributeValues',
                fn () => $this->attributeValues
                    ->map(
                        fn ($attributeValue) => [
                            'id' => $attributeValue->id,
                            'attribute_id' => $attributeValue->attribute_id,
                            'name' => $attributeValue->attribute?->name,
                            'display_name' => $attributeValue->attribute?->display_name,
                            'input_type' => $attributeValue->attribute?->input_type,
                            'value' => $attributeValue->value,
                        ]
                    )
                    ->values()
            ),

            'price' => [
                'base' => $this->base_price,
                'customization' => $this->customization_fee,
                'total' => $this->total_price,
            ],

            'status' => $this->status,

            'created_at' => $this->created_at,
        ];
    }
}
