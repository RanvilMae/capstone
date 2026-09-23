<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YieldForecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'plot_id',
        'target_harvest_date',
        'scheduled_tapping_days',
        'target_revenue_goal',
        'estimated_drc_percent',
        'target_market_price',
        'projected_raw_latex_kg',
        'projected_dry_rubber_kg',
        'projected_revenue',
        'goal_completion_rate',
        'advisory_insight',
    ];

    protected $casts = [
        'target_harvest_date' => 'date',
        'scheduled_tapping_days' => 'integer',
        'target_revenue_goal' => 'float',
        'estimated_drc_percent' => 'float',
        'target_market_price' => 'float',
        'projected_raw_latex_kg' => 'float',
        'projected_dry_rubber_kg' => 'float',
        'projected_revenue' => 'float',
        'goal_completion_rate' => 'float',
    ];

    public function plot(): BelongsTo
    {
        return $this->belongsTo(Plot::class);
    }
}