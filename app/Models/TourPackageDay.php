<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One day of a package itinerary. duration_days null = shown for any duration. */
class TourPackageDay extends Model
{
    protected $fillable = ['package_id', 'duration_days', 'day_number', 'title', 'description', 'image_path'];

    protected $casts = ['duration_days' => 'integer', 'day_number' => 'integer'];

    public function package()
    {
        return $this->belongsTo(TravelPackage::class, 'package_id');
    }
}
