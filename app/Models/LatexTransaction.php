<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LatexTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'plot_id',
        'location',
        'transaction_date',
        'volume_kg',
        'dry_rubber_content',
        'drc_sample_1',
        'drc_sample_2',
        'drc_sample_3',
        'dry_rubber_weight_kg',
        'price_per_kg',
        'total_amount',
        'user_id',
        'dry_sample_1',
        'dry_sample_2',
        'dry_sample_3',
    ];


    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    // Relationships
    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
