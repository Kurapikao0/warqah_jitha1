<?php

namespace App\Models;

use App\Enums\CustomerNotificationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerNotification extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'customer_id',
        'type',
        'title',
        'body',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'type' => CustomerNotificationType::class,
            'is_read' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
