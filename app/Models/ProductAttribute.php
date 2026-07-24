<?php

namespace App\Models;

use App\Enums\ProductAttributeInputType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttribute extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'input_type',
    ];

    protected function casts(): array
    {
        return [
            'input_type' => ProductAttributeInputType::class,
        ];
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_attribute_values', 'attribute_id', 'product_id')
            ->withPivot('value');
    }
}
