<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AdminUser; 
class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'order_status_histories';
    const UPDATED_AT = null;

    protected $fillable = [
        'order_id',
        'status',
        'note',
        'changed_by',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'changed_by');
    }
}