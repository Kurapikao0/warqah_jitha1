<?php

namespace App\Models;

use App\Enums\CustomDesignRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomDesignRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => CustomDesignRequestStatus::class,
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function images(): HasMany
    {
        return $this->hasMany(
            CustomDesignRequestImage::class,
            'custom_design_request_id'
        )->orderBy('sort_order');
    }
}
