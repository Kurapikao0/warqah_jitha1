<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {

        return [

            'id' => $this->id,

            'customer_id' => $this->customer_id,

            'recipient_name' => $this->recipient_name,

            'phone' => $this->phone,

            'country' => $this->country,

            'city' => $this->city,

            'district' => $this->district,

            'street' => $this->street,

            'postal_code' => $this->postal_code,

            'is_default' => (bool) $this->is_default,

            /*
            |--------------------------------------------------------------------------
            | Customer Relation
            |--------------------------------------------------------------------------
            */

            'customer' => new CustomerResource(
                $this->whenLoaded('customer')
            ),

            /*
            |--------------------------------------------------------------------------
            | Orders Relation
            |--------------------------------------------------------------------------
            */

            'orders' => OrderResource::collection(
                $this->whenLoaded('orders')
            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];

    }
}
