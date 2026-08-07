<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\FavoriteResource;

class CustomerResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {

        return [

            'id' => $this->id,


            'full_name' => $this->full_name,


            'email' => $this->email,


            'phone_country_code' =>
                $this->phone_country_code,


            'phone' =>
                $this->phone,


            'avatar_url' => 
                $this->avatar_url ? asset($this->avatar_url) : null,    


            'category' =>
                $this->category,


            'email_verified_at' =>
                $this->email_verified_at,


            'phone_verified_at' =>
                $this->phone_verified_at,


            'total_orders' =>
                $this->total_orders,


            'total_purchases' =>
                $this->total_purchases,


            'last_order_at' =>
                $this->last_order_at,



            /*
            |--------------------------------------------------------------------------
            | Relations
            |--------------------------------------------------------------------------
            */


            'addresses' => AddressResource::collection(
                $this->whenLoaded('addresses')
            ),



            'orders' => OrderResource::collection(
                $this->whenLoaded('orders')
            ),



            'reviews' => ReviewResource::collection(
                $this->whenLoaded('reviews')
            ),



            'favorites' => FavoriteResource::collection(
                $this->whenLoaded('favorites')
            ),



            'notifications' =>
                CustomerNotificationResource::collection(
                    $this->whenLoaded('notifications')
                ),



            'cart' =>
                new CartResource(
                    $this->whenLoaded('cart')
                ),



            'created_at' =>
                $this->created_at,


            'updated_at' =>
                $this->updated_at,


        ];

    }

}