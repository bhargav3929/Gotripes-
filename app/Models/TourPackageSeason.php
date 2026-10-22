<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A peak period: surcharge_percent is added to the package rate for trips starting in it. */
class TourPackageSeason extends Model
{
    protected $fillable = ['package_id', 'name', 'starts_on', 'ends_on', 'surcharge_percent'];

    protected $casts = ['starts_on' => 'date', 'ends_on' => 'date', 'surcharge_percent' => 'float'];

    public function package()
    {
        return $this->belongsTo(TravelPackage::class, 'package_id');
    }
}
