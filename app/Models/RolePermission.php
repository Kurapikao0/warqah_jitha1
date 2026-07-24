<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Explicit model for the `role_permissions` pivot table. Provided for
 * direct querying / seeding of the pivot; `Role::permissions()` and
 * `Permission::roles()` use `belongsToMany()` directly and do not
 * require this class.
 */
class RolePermission extends Model
{
    use HasFactory;

    /**
     * The `role_permissions` table has no timestamp columns at all.
     */
    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'permission_id',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
