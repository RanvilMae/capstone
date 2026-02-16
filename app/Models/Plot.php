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


    public function productionSummaries()
    {
        return $this->hasMany(ProductionSummary::class);
    }

    public function latexTransactions()
    {
        return $this->hasMany(LatexTransaction::class, 'plot_id', 'id');
    }

    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function production_summaries()
    {
        return $this->hasMany(ProductionSummary::class);
    }
}
