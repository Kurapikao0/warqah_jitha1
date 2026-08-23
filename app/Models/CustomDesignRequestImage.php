<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomDesignRequestImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_design_request_id',
        'url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function customDesignRequest(): BelongsTo
    {
        return $this->belongsTo(
            CustomDesignRequest::class,
            'custom_design_request_id'
        );
    }
}
