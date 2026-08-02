<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id'=>$this->id,

            'full_name'=>$this->full_name,

            'email'=>$this->email,

            'phone'=>$this->phone,

            'avatar_url'=>$this->avatar_url,

            'role'=>$this->whenLoaded(
                'role'
            ),

            'created_at'=>$this->created_at,

        ];
    }
}