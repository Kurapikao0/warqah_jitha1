<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;



class OrderStatusHistoryResource extends JsonResource
{


public function toArray(Request $request): array
{

return [

'id'=>$this->id,


'status'=>$this->status,


'note'=>$this->note,


'changed_by'=>
$this->changedBy?->name,


'created_at'=>$this->created_at


];


}


}