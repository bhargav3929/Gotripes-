<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * The hotel behind a star tier, with the check-in/out times, late check-in
 * and check-out policy, and the meal plans it actually serves (many hotels
 * have no lunch or dinner menu, so not every tier offers every plan).
 */
class TourPackageHotel extends Model
{
    public const MEAL_PLANS = [
        'room_only'  => 'Room only',
        'breakfast'  => 'Breakfast',
        'half_board' => 'Half board (breakfast + dinner)',
        'full_board' => 'Full board (breakfast, lunch + dinner)',
    ];

    protected $fillable = [
        'package_id', 'hotel_stars', 'name', 'location', 'check_in_time', 'check_out_time',
        'late_check_in_policy', 'late_check_out_policy', 'meal_plans',
    ];

    protected $casts = ['hotel_stars' => 'integer', 'meal_plans' => 'array'];

    public function package()
    {
        return $this->belongsTo(TravelPackage::class, 'package_id');
    }

    /** Meal plan codes this hotel serves, in the standard order. */
    public function mealPlanCodes(): array
    {
        $plans = $this->meal_plans ?: ['breakfast'];
        return array_values(array_filter(array_keys(self::MEAL_PLANS), fn ($c) => in_array($c, $plans, true)));
    }
}
