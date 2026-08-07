<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,


            'customer_id' => $this->customer_id,


            'product_id' => $this->product_id,


            /*
            |--------------------------------------------------------------------------
            | Product Details
            |--------------------------------------------------------------------------
            */

            'product' => new ProductResource(
                $this->whenLoaded('product')
            ),


            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}