<?php

namespace Tests\Feature\TourPackages;

use App\Models\Company;
use App\Models\DestinationVisaRule;
use App\Models\TourPackageAddon;
use App\Models\TourPackageHotel;
use App\Models\TourPackageRate;
use App\Models\TourPackageSeason;
use App\Models\TravelPackage;
use Illuminate\Support\Str;

/**
 * A realistic wizard package for tests: Georgia, 4/5/6 days, three hotel
 * tiers (the 3-star serves breakfast only), 10% margin.
 */
trait TourPackageFixture
{
    protected function tenant(): Company
    {
        $slug = 'tpk' . Str::lower(Str::random(8));
        return Company::create([
            'name' => 'Tour Test ' . $slug, 'slug' => $slug, 'subdomain' => $slug,
            'is_active' => true, 'plan' => 'pro', 'subscription_ends_at' => now()->addYear(),
            'features' => ['tours'], 'commission_type' => 'percentage', 'commission_value' => 10,
        ]);
    }

    protected function georgia(Company $company, array $attrs = []): TravelPackage
    {
        // Visa answers come from the staff rules below, never the live eVisa API.
        // VisaEligibilityTest turns Fluxir back on against a faked API.
        config(['fluxir.client_id' => null]);

        $p = TravelPackage::withoutCompanyScope()->create(array_merge([
            'company_id' => $company->id, 'title' => 'Georgia Highlights', 'country' => 'Georgia',
            'package_type' => 'wizard', 'price' => 0, 'description' => 'Tbilisi, Kazbegi and wine country.',
            'duration' => '4–6 days', 'isActive' => true, 'margin_percent' => 10,
        ], $attrs));

        foreach ([4 => 1.0, 5 => 1.2, 6 => 1.4] as $days => $f) {
            foreach ([3 => 1000, 4 => 1400, 5 => 2000] as $stars => $adult) {
                TourPackageRate::create([
                    'package_id' => $p->id, 'days' => $days, 'hotel_stars' => $stars,
                    'adult_net' => $adult * $f, 'child_net' => $adult * $f / 2, 'infant_net' => 50,
                    'single_supplement_net' => 300 * $f,
                ]);
            }
        }
        TourPackageHotel::create(['package_id' => $p->id, 'hotel_stars' => 3, 'name' => 'Tbilisi Inn',
            'check_in_time' => '14:00', 'check_out_time' => '12:00',
            'late_check_in_policy' => 'Reception is open 24 hours.', 'late_check_out_policy' => 'Until 16:00 for 50% of a night.',
            'meal_plans' => ['breakfast']]);
        TourPackageHotel::create(['package_id' => $p->id, 'hotel_stars' => 4, 'name' => 'Rooms Tbilisi',
            'meal_plans' => ['breakfast', 'half_board']]);
        TourPackageHotel::create(['package_id' => $p->id, 'hotel_stars' => 5, 'name' => 'Stamba',
            'meal_plans' => ['room_only', 'breakfast', 'half_board', 'full_board']]);

        $addons = [
            ['guide_language', 'en', 'English', 0, 'per_booking'],
            ['guide_language', 'ar', 'Arabic', 40, 'per_day'],
            ['transport', 'standard', 'Standard bus', 0, 'per_booking'],
            ['transport', 'luxury', 'Luxury bus', 15, 'per_person_per_day'],
            ['meal_plan', 'half_board', 'Half board', 25, 'per_person_per_day'],
            ['meal_plan', 'full_board', 'Full board', 45, 'per_person_per_day'],
            ['airport_transfer', 'transfer', 'Airport to hotel pickup', 120, 'per_booking'],
            ['insurance', 'basic', 'Travel insurance', 30, 'per_person'],
        ];
        foreach ($addons as $i => [$cat, $code, $label, $net, $basis]) {
            TourPackageAddon::create(['package_id' => $p->id, 'category' => $cat, 'code' => $code,
                'label' => $label, 'net_price' => $net, 'charge_basis' => $basis, 'sort_order' => $i]);
        }
        TourPackageSeason::create(['package_id' => $p->id, 'name' => 'New Year', 'starts_on' => '2027-12-24',
            'ends_on' => '2028-01-03', 'surcharge_percent' => 20]);

        DestinationVisaRule::withoutCompanyScope()->firstOrCreate(['company_id' => $company->id, 'destination' => 'Georgia',
            'nationality' => 'India'], ['requirement' => 'evisa', 'processing_days' => 5, 'fee' => 80,
            'documents' => 'Passport valid 6 months, bank statement']);
        DestinationVisaRule::withoutCompanyScope()->firstOrCreate(['company_id' => $company->id, 'destination' => 'Georgia',
            'nationality' => '*'], ['requirement' => 'visa_free', 'max_stay_days' => 365]);

        return $p->fresh();
    }

    /**
     * The Georgia package priced the new way: room prices per night and tour
     * prices per adult by group size (6 days only), no per-person rates.
     *   3★ Tbilisi Inn: single 250, double 300, triple 390, extra bed 60
     *   4★ Rooms Tbilisi: double 500 only
     *   land, 6 days: 1 adult 1800, 2 adults 1100, 3-4 adults 900, 5+ adults 750
     */
    protected function georgiaRooms(Company $company, array $attrs = []): TravelPackage
    {
        $p = $this->georgia($company, $attrs);
        \App\Models\TourPackageRate::where('package_id', $p->id)->delete();
        TourPackageHotel::where('package_id', $p->id)->where('hotel_stars', 3)->update(['single_net' => 250, 'double_net' => 300, 'triple_net' => 390, 'extra_bed_net' => 60]);
        TourPackageHotel::where('package_id', $p->id)->where('hotel_stars', 4)->update(['double_net' => 500]);
        foreach ([1 => 1800, 2 => 1100, 3 => 900, 5 => 750] as $g => $net) {
            \App\Models\TourPackageLandRate::create(['package_id' => $p->id, 'days' => 6, 'min_group' => $g, 'adult_net' => $net]);
        }
        $p->forceFill(['child_land_percent' => 50, 'child_sharing_max_age' => 5])->save();
        return $p->fresh();
    }

    protected function input(array $over = []): array
    {
        return array_merge([
            'start_date' => now()->addMonth()->toDateString(), 'days' => 5, 'hotel_stars' => 4,
            'traveler_type' => 'couple', 'adults' => 2, 'child_ages' => [], 'meal_plan' => 'breakfast',
            'guide_language' => 'en', 'transport' => 'standard', 'airport_transfer' => false,
            'insurance' => false, 'nationality' => 'United Kingdom',
        ], $over);
    }
}
