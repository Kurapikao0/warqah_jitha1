<?php

namespace App\Models;

use App\Enums\RawMaterialStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Tracks stock/reorder data for a raw material. `product_id` is nullable
 * and unique: it is set only when this raw material is also sold
 * directly as a catalog item (e.g. oud sticks sold as-is), linking it
 * 1-to-1 to its `products` row (`product_type = raw_material`). Raw
 * materials consumed only internally during production are never
 * linked to a product.
 *
 * There is still no bill-of-materials / consumption pivot in the
 * migrations (e.g. tracking how much of a raw material a given product
 * consumes to be manufactured) — that remains a schema gap, not
 * addressed here, since it would mean inventing a table.
 */
class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'unit',
        'quantity_available',
        'reorder_point',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity_available' => 'decimal:2',
            'reorder_point' => 'decimal:2',
            'status' => RawMaterialStatus::class,
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
