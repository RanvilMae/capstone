<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plot extends Model
{
    protected $table = 'plots';

    protected $fillable = [
        'code',
        'farmer_id',
        'user_id',
        'plot_size_rai',
        'plot_location',
        'notes',
    ];

    protected static function booted()
    {
        static::creating(function ($plot) {
            // Keep user_id linked to the logged-in user who created the plot
            if (!$plot->user_id && auth()->check()) {
                $plot->user_id = auth()->id();
            }
        });
    }

    // Fix: Point to the actual Farmer model
    public function farmer()
    {
        return $this->belongsTo(Farmer::class, 'farmer_id');
    }

    // Optional: Keep a relationship for the User account who registered/manages the plot
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
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