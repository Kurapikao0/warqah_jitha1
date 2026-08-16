<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $adminUser = $this->adminUser;
        $action = (string) $this->action;

        /*if (str_contains($action, 'created')) {
            $normalizedAction = 'created';
        } elseif (str_contains($action, 'updated')) {
            $normalizedAction = 'updated';
        } elseif (str_contains($action, 'deleted')) {
            $normalizedAction = 'deleted';
        } else {
            $normalizedAction = 'updated';
        }*/

        $meta = is_array($this->meta) ? $this->meta : [];
        $description = $meta['description'] ?? $this->action;

        return [
            'id' => $this->id,
            'user_id' => $adminUser?->id,
            'user_name' => $adminUser?->full_name ?? '—',
            'action' => $action,
            'subject_type' => $this->entity_type,
            'subject_id' => $this->entity_id,
            'entity_type' => $this->entity_type,
            'entity_id' => $this->entity_id,
            'description' => $description,
            'meta' => $meta,
            'admin_user' => $adminUser ? [
                'id' => $adminUser->id,
                'name' => $adminUser->full_name,
                'avatar_url' => $adminUser->avatar_url ? asset($adminUser->avatar_url) : null,
            ] : null,
            'created_at' => $this->created_at,
        ];
    }
}
