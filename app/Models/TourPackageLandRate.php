<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Land part of a tour package (tours, transfers, guide, vehicle): the NET
 * price per adult for the whole trip, for one trip length and one group
 * size band. Bigger groups share the vehicle and guide, so they pay less.
 */
class TourPackageLandRate extends Model
{
    /** Group size bands: smallest group size => label shown to staff. */
    public const GROUPS = [1 => '1', 2 => '2', 3 => '3–4', 5 => '5–6', 7 => '7–9', 10 => '10+'];

    protected $fillable = ['package_id', 'days', 'min_group', 'adult_net'];

    protected $casts = ['days' => 'integer', 'min_group' => 'integer', 'adult_net' => 'float'];

    public function package()
    {
        return $this->belongsTo(TravelPackage::class, 'package_id');
    }
}
