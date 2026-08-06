<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', // يجب أن يكون مسموحاً بالتعبئة الجماعية Mass Assignment
        'code',
        'expires_at',
        'is_used',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}