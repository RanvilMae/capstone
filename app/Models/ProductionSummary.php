<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionSummary extends Model
{
    protected $fillable = ['plot_id', 'production_year_id', 'dry_rubber_weight_kg', 'total_amount_baht'];

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    public function productionYear()
    {
        return $this->belongsTo(ProductionYear::class);
    }
}