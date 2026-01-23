<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionYear extends Model
{
    protected $fillable = ['year_label', 'start_date', 'end_date'];

    public function summaries()
    {
        return $this->hasMany(ProductionSummary::class);
    }
}
