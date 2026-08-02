<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'product_type',
        'sku',
        'name',
        'slug',
        'description',
        'price',
        'compare_at_price',
        'stock_quantity',
        'reserved_quantity',
        'length_cm',
        'width_cm',
        'height_cm',
        'is_customizable',
        'is_handmade',
        'is_new',
        'is_bestseller',
        'is_limited_edition',
        'status',
    ];

    /**
     * `average_rating` and `reviews_count` are derived/aggregate fields
     * maintained by the application (e.g. after review moderation) and
     * are intentionally excluded from `$fillable` to prevent them being
     * set directly via mass assignment.
     */
    protected function casts(): array
    {
        return [
            'product_type' => ProductType::class,
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'reserved_quantity' => 'integer',
            'length_cm' => 'decimal:2',
            'width_cm' => 'decimal:2',
            'height_cm' => 'decimal:2',
            'is_customizable' => 'boolean',
            'is_handmade' => 'boolean',
            'is_new' => 'boolean',
            'is_bestseller' => 'boolean',
            'is_limited_edition' => 'boolean',
            'average_rating' => 'decimal:1',
            'reviews_count' => 'integer',
            'status' => ProductStatus::class,
            'deleted_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class);
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class, 'product_colors');
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttribute::class, 'product_attribute_values', 'product_id', 'attribute_id')
            ->withPivot('value');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function customizationRequests(): HasMany
    {
        return $this->hasMany(ProductCustomizationRequest::class, 'base_product_id');
    }

    /**
     * Present only when this catalog product also represents a sellable
     * raw material (`raw_materials.product_id` set). Optional 1-to-1 —
     * most products (finished/semi-finished goods) will not have one.
     */
    public function rawMaterial(): HasOne
    {
        return $this->hasOne(RawMaterial::class);
    }

    
}
