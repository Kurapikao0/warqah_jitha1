<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        return [

            'id' => $this->id,

            'order_number' => $this->order_number,

            'type' => $this->order_type?->value,

            'status' => $this->status,

            'customer' => [

                'id' => $this->customer?->id,

                'name' => $this->customer?->full_name,

                'phone' => $this->customer?->phone,

            ],

            'items' => OrderItemResource::collection(
                $this->whenLoaded('items')
            ),

            'payment' => new PaymentResource(
                $this->whenLoaded('payment')
            ),

            'total' => $this->total_amount,

            'subtotal' => $this->subtotal,

            'shipping_fee' => $this->shipping_fee,

            'expected_delivery_date' => $this->expected_delivery_date,

            'address' => $this->whenLoaded('address', function () {
                return [
                    'id' => $this->address?->id,
                    'city' => $this->address?->city,
                    'district' => $this->address?->district,
                    'street' => $this->address?->street,
                    'phone' => $this->address?->phone,
                ];
            }),

            'status_history' => OrderStatusHistoryResource::collection(
                $this->whenLoaded('statusHistory')
            ),

            'created_at' => $this->created_at,

        ];

    }
}
