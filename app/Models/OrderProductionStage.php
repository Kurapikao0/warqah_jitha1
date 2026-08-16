<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderProductionStage extends Model
{
    use HasFactory;

    /**
     * The `order_production_stages` table has no timestamp columns at all.
     */
    public $timestamps = false;

    protected $fillable = [
        'name',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'current_production_stage_id');
    }

    public function stageHistory(): HasMany
    {
        return $this->hasMany(OrderProductionStageHistory::class, 'stage_id');
    }

    public function next()
    {
        return self::where(
            'sort_order',
            '>',
            $this->sort_order
        )
            ->orderBy('sort_order')
            ->first();
    }
}
