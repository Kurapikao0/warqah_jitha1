<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerNotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'type' => $this->type,

            'title' => $this->title,

            'body' => $this->body,

            'is_read' => $this->is_read,

            'created_at' => $this->created_at,

        ];
    }
}