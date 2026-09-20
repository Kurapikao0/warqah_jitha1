<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'customization_note',
        'reserved_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reserved_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockReservation(): HasOne
    {
        return $this->hasOne(StockReservation::class)->where('status', 'active');
    }

    public function isExpired(): bool
    {
        if (! $this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    public function remainingSeconds(): int
    {
        if (! $this->expires_at || $this->isExpired()) {
            return 0;
        }

        return max(0, (int) now()->diffInSeconds($this->expires_at, false));
    }
}
