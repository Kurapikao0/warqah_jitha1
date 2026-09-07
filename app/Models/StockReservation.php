<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'cart_item_id',
        'customer_id',
        'order_id',
        'session_id',
        'quantity',
        'reserved_at',
        'expires_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reserved_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItem(): BelongsTo
    {
        return $this->belongsTo(CartItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function release(): void
    {
        $this->update(['status' => 'released']);
    }

    public function consume(): void
    {
        $this->update(['status' => 'consumed']);
    }
}
