<?php

namespace App\Models;

use App\Enums\AdminNotificationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'admin_user_id',
        'type',
        'title',
        'body',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'type' => AdminNotificationType::class,
            'is_read' => 'boolean',
            'created_at' => 'datetime',

        ];
    }

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class);
    }
}
