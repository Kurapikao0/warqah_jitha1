<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;



class CartItemResource extends JsonResource
{


public function toArray(Request $request): array
{

return [

'id'=>$this->id,


'product'=>[

'id'=>$this->product->id,

'name'=>$this->product->name,

'price'=>$this->product->price

],


'quantity'=>$this->quantity,


'customization_note'=>
$this->customization_note


];


}


}