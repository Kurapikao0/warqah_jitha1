<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        return [

            'id' => $this->id,

            'order' => [

                'id' => $this->order?->id,

                'number' => $this->order?->order_number,
                'customer' => [
                    'id' => $this->order?->customer?->id,
                    'name' => $this->order?->customer?->full_name,
                ],

            ],

            'method' => $this->payment_method?->value ?? $this->payment_method,

            'amount' => $this->amount,

            /*'transaction_reference'=>
        $this->transaction_reference,*/

            'status' => $this->status?->value ?? $this->status,

            /*'proof_image'=>
        $this->proof_image,*/

            'created_at' => $this->created_at,

            'paid_at' => $this->paid_at,

        ];

    }
}
