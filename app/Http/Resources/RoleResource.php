<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,

            'description' => $this->description,

            'permissions' => $this->whenLoaded(
                'permissions'
            ),

            'admin_users' => $this->whenLoaded(
                'adminUsers'
            ),

            'admin_users_count' => $this->when(
                isset($this->admin_users_count),
                $this->admin_users_count
            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}