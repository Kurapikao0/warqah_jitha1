<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProductionStageHistory extends Model
{
    use HasFactory;

    // تم ربط الموديل باسم الجدول الصحيح (جمع) ليتوافق مع التهجيرات
    protected $table = 'order_production_stage_histories';

    const UPDATED_AT = null;

    protected $fillable = [
        'order_id',
        'stage_id',
        'changed_by',
        'note',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(OrderProductionStage::class, 'stage_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'changed_by');
    }
}
