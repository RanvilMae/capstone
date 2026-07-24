<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plot extends Model
{
    protected $table = 'plots';

    protected $fillable = [
        'code',
        'farmer_id',
        'user_id', // 1. Add user_id here
        'plot_size_rai',
        'plot_location',
        'notes',
    ];

    // 2. Automatically sync user_id with farmer_id when creating a plot
    protected static function booted()
    {
        static::creating(function ($plot) {
            if (!$plot->user_id && $plot->farmer_id) {
                $plot->user_id = $plot->farmer_id;
            }
        });
    }

    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function latexTransactions()
    {
        return $this->hasMany(LatexTransaction::class, 'plot_id', 'id');
    }

    public function productionSummaries()
    {
        return $this->hasMany(ProductionSummary::class);
    }
}