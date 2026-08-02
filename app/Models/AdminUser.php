<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use App\Enums\AdminNotificationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens ;

    protected $fillable = [
        'role_id',
        'full_name',
        'email',
        'phone',
        'avatar_url',
        'password_hash',
        'two_factor_enabled',
        'last_login_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'two_factor_enabled' => 'boolean',
            'last_login_at' => 'datetime',
            'password_hash' => 'hashed',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * The auth column is `password_hash`, not the framework default
     * `password`. Overriding this keeps Illuminate\Auth working without
     * renaming the migration column.
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function passwordResets(): HasMany
    {
        return $this->hasMany(AdminPasswordReset::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(AdminNotification::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function orderStatusChanges(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class, 'changed_by');
    }

    public function orderProductionStageChanges(): HasMany
    {
        return $this->hasMany(OrderProductionStageHistory::class, 'changed_by');
    }
}
