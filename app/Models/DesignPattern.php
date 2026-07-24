<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DesignPattern extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'description',
        'preview_image_url',
    ];

    public function customizationRequests(): HasMany
    {
        return $this->hasMany(ProductCustomizationRequest::class);
    }
}
