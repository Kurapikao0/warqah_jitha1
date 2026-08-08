<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'hex_code',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_colors');
    }

    public function customizationRequests(): HasMany
    {
        return $this->hasMany(ProductCustomizationRequest::class);
    }
}
