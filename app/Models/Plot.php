<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plot extends Model
{
    protected $fillable = ['farmer_id', 'plot_size_rai', 'plot_location', 'notes'];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function productionSummaries()
    {
        return $this->hasMany(ProductionSummary::class);
    }
}
