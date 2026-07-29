<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;



class OrderItemResource extends JsonResource
{


public function toArray(Request $request): array
{


return [

'id'=>$this->id,


'product'=>[

'id'=>$this->product->id,

'name'=>$this->product->name

],


'quantity'=>$this->quantity,


'price'=>$this->unit_price,


'customized'=>$this->is_customized


];


}


}