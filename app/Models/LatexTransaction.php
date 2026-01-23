<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LatexTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'plot_id',
        'transaction_date',
        'volume_kg',
        'dry_rubber_content',
        'price_per_kg',
        'total_amount',
        'user_id'
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
