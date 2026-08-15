<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'store_logo',
        'contact_email',
        'contact_phone',
        'tax_rate',
        'default_currency',
        'maintenance_mode',
        'maintenance_message',
    ];

    protected function casts(): array
    {
        return [
            'tax_rate' => 'float',
            'maintenance_mode' => 'boolean',
        ];
    }
}
