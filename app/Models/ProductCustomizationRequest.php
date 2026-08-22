<?php

namespace App\Models;

use App\Enums\ProductCustomizationRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCustomizationRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'request_code',
        'customer_id',
        'base_product_id',
        'color_id',
        'design_pattern_id',
        'quantity',
        'length_cm',
        'width_cm',
        'height_cm',
        'customer_notes',
        'craftsman_notes',
        'base_price',
        'customization_fee',
        'packaging_shipping_fee',
        'total_price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'length_cm' => 'decimal:2',
            'width_cm' => 'decimal:2',
            'height_cm' => 'decimal:2',
            'base_price' => 'decimal:2',
            'customization_fee' => 'decimal:2',
            'packaging_shipping_fee' => 'decimal:2',
            'total_price' => 'decimal:2',
            'status' => ProductCustomizationRequestStatus::class,
            'deleted_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function baseProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'base_product_id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function designPattern(): BelongsTo
    {
        return $this->belongsTo(DesignPattern::class);
    }

    public function orderItem(): HasOne
    {
        return $this->hasOne(OrderItem::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(
            ProductCustomizationAttributeValue::class,
            'customization_request_id'
        );
    }
}
