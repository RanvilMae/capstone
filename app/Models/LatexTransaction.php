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
        'dry_sample_1',
        'dry_sample_2',
        'dry_sample_3',
        'dry_rubber_weight_kg',
        'price_per_kg',
        'total_amount',
        'user_id',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'volume_kg' => 'float',
        'dry_rubber_content' => 'float',
        'dry_rubber_weight_kg' => 'float',
        'price_per_kg' => 'float',
        'total_amount' => 'float',
    ];

    public function plot()
    {
        return $this->belongsTo(Plot::class, 'plot_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}