<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Framework-default `users` table (Laravel's built-in auth scaffold).
 *
 * NOTE (architectural finding — see Schema Analysis Report): no domain
 * table holds a constrained foreign key to `users`. The `sessions.user_id`
 * column references it only by convention (no DB-level FK). The actual
 * application actors are `admin_users` and `customers`, each with their
 * own dedicated tables/guards. This model is kept because the migration
 * exists and is part of Laravel's default scaffolding, but it is not
 * wired into any domain relationship, since none is declared in the
 * migrations.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
