<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ProductResource extends JsonResource
{


public function toArray(Request $request): array
{


return [

'id'=>$this->id,

'name'=>$this->name,

'sku'=>$this->sku,


'description'=>$this->description,


'price'=>$this->price,


'stock'=>$this->stock_quantity,


'status'=>$this->status,


'is_customizable'=>$this->is_customizable,


'category'=>new CategoryResource(
    $this->whenLoaded('category')
),


'media'=>$this->whenLoaded('media'),


'created_at'=>$this->created_at


];


}


}