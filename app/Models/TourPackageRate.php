<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Net price per person for the whole stay, for one duration and hotel tier. */
class TourPackageRate extends Model
{
    protected $fillable = ['package_id', 'days', 'hotel_stars', 'adult_net', 'child_net', 'infant_net', 'single_supplement_net'];

    protected $casts = [
        'days' => 'integer', 'hotel_stars' => 'integer',
        'adult_net' => 'float', 'child_net' => 'float', 'infant_net' => 'float', 'single_supplement_net' => 'float',
    ];

    public function package()
    {
        return $this->belongsTo(TravelPackage::class, 'package_id');
    }
}
