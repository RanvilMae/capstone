<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intervention extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'description',
        'start_date',
        'end_date',
    ];

    // Relationship: An intervention belongs to a farm
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
