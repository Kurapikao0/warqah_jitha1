<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_customization_request_id',
        'quantity',
        'unit_price',
        'customization_note',
        'is_customized',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'is_customized' => 'boolean',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customizationRequest(): BelongsTo
    {
        return $this->belongsTo(ProductCustomizationRequest::class, 'product_customization_request_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
