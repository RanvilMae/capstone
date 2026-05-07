<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'owner_name',
        'area_hectare',
        'rubber_age',
    ];

    // Relationship: A farm can have many interventions
    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }
}
