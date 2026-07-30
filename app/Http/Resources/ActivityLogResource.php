<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'action' => $this->action,

            'entity_type' => $this->entity_type,

            'entity_id' => $this->entity_id,

            'meta' => $this->meta,

            'admin_user' => $this->whenLoaded(
                'adminUser',
                function () {
                    return [
                        'id' => $this->adminUser->id,
                        'name' => $this->adminUser->full_name,
                    ];
                }
            ),

            'created_at' => $this->created_at,

        ];
    }
}