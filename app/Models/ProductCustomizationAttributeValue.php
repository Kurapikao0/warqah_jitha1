<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCustomizationAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'customization_request_id',
        'attribute_id',
        'value',
    ];

    public function customizationRequest(): BelongsTo
    {
        return $this->belongsTo(
            ProductCustomizationRequest::class,
            'customization_request_id'
        );
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(
            ProductAttribute::class,
            'attribute_id'
        );
    }
}
