<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmer extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address', 'farm_location', 'farm_size', 'notes'];

    public function plots()
    {
        return $this->hasMany(Plot::class);
    }
}