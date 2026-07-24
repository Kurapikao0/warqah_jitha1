<?php

namespace App\Models;

use App\Enums\CustomDesignRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
