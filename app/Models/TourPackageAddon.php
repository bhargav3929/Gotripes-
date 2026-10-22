<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A priced option on a wizard package. A net_price of 0 means "included".
 * Meal plan addons are the supplement for that plan on top of the room rate.
 */
class TourPackageAddon extends Model
{
    public const CATEGORIES = [
        'guide_language'   => 'Tour guide language',
        'transport'        => 'Daily transport',
        'meal_plan'        => 'Meal plan supplement',
        'airport_transfer' => 'Airport transfer',
        'insurance'        => 'Travel insurance',
    ];

    public const BASES = [
        'per_person'         => 'per person',
        'per_person_per_day' => 'per person per day',
        'per_booking'        => 'per booking',
        'per_day'            => 'per day',
    ];

    protected $fillable = ['package_id', 'category', 'code', 'label', 'description', 'net_price', 'charge_basis', 'sort_order'];

    protected $casts = ['net_price' => 'float', 'sort_order' => 'integer'];

    public function package()
    {
        return $this->belongsTo(TravelPackage::class, 'package_id');
    }
}
