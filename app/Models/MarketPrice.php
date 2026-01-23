<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketPrice extends Model
{
    protected $fillable = [
        'price_per_kg',
        'date',
        'source'
    ];

    protected $dates = ['date'];
}
