<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VerificationPurpose;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VerificationCode extends Model
{    use HasFactory;

    protected $table = 'verification_codes';

    public const UPDATED_AT = null;

    protected $fillable = [
        'customer_id',
        'purpose',
        'code_or_token',
        'contact_value',
        'expires_at',
        'consumed_at',
    ];

    protected function casts(): array
    {
        return [
            'purpose' => VerificationPurpose::class,
            'expires_at' => 'datetime',
            'consumed_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
