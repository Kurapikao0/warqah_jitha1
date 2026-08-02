<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;



class OrderResource extends JsonResource
{


public function toArray(Request $request): array
{


return [

'id'=>$this->id,


'order_number'=>$this->order_number,


'type'=>$this->order_type,


'status'=>$this->status,



'customer'=>[

'id'=>$this->customer?->id,

'name'=>$this->customer?->full_name

],



'items'=>
OrderItemResource::collection(
$this->whenLoaded('items')
),



'payment'=>
new PaymentResource(
$this->whenLoaded('payment')
),



'total'=>$this->total_amount,


'created_at'=>$this->created_at


];


}


}