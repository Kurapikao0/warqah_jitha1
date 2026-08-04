<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\CustomerCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class Customer extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens, Notifiable;
    protected $fillable = [
        'full_name',
        'email',
        'phone_country_code',
        'phone',
        'password_hash',
        'avatar_url',
        'category',
        'email_verified_at',
        'phone_verified_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'category' => CustomerCategory::class,
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'total_orders' => 'integer',
            'total_purchases' => 'decimal:2',
            'last_order_at' => 'datetime',
            'password_hash' => 'hashed',
            'deleted_at' => 'datetime',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function verificationCodes(): HasMany
    {
        return $this->hasMany(VerificationCode::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(CustomerNotification::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function customDesignRequests(): HasMany
    {
        return $this->hasMany(CustomDesignRequest::class);
    }

    public function customizationRequests(): HasMany
    {
        return $this->hasMany(ProductCustomizationRequest::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}

