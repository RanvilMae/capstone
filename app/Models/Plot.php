<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plot extends Model
{
    protected $table = 'plots'; // just to be explicit

    protected $fillable = [
        'farmer_id',
        'plot_size_rai',
        'plot_location',
        'notes',
    ];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function productionSummaries()
    {
        return $this->hasMany(ProductionSummary::class);
    }

    public function latexTransactions()
    {
        return $this->hasMany(LatexTransaction::class, 'plot_id', 'id');
    }
}
