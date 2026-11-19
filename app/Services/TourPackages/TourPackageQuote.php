<?php

namespace App\Services\TourPackages;

use App\Models\DestinationVisaRule;
use App\Models\TourPackageAddon;
use App\Models\TourPackageHotel;
use App\Models\TravelPackage;
use Carbon\Carbon;

/**
 * Prices a wizard package booking. This is the only place a tour package
 * price is worked out: the wizard shows what this returns, and checkout
 * charges exactly its total. Nothing priced in the browser is trusted.
 *
 * Supplier rates are NET. Every line is net × (1 + margin%), rounded per
 * line, so the lines the customer sees always add up to the total charged.
 *
 * Who pays what:
 *   - adults pay the adult rate; children (infant_max_age < age <= child_max_age)
 *     the child rate; infants (age <= infant_max_age) the infant rate;
 *     anyone older than child_max_age is priced as an adult
 *   - an odd number of adults leaves one in a single room: single supplement
 *   - a peak season the trip starts in adds its surcharge to the package rate
 *   - per-person add-ons count adults and children; infants travel free on add-ons
 */
class TourPackageQuote
{
    public const TRAVELER_TYPES = ['solo', 'couple', 'family', 'group'];
    public const MAX_TRAVELLERS = 50;

    /**
     * @return array{ok: bool, errors: array<string,string>, quote: ?array}
     */
    public static function make(TravelPackage $package, array $input, bool $checkTravellerType = true): array
    {
        $package->loadMissing(['rates', 'landRates', 'hotels', 'addons', 'seasons']);
        $rooms = $package->usesRoomPricing();
        $errors = [];
        $margin = max(0.0, (float) $package->margin_percent) / 100;
        $sell = fn (float $net) => round($net * (1 + $margin), 2);

        // ---- dates and duration ----
        $days = (int) ($input['days'] ?? 0);
        $stars = (int) ($input['hotel_stars'] ?? 0);
        $rate = null;
        if ($rooms) {
            if (!$package->landRates->contains('days', $days)) {
                $errors['days'] = 'Choose one of the available trip lengths.';
            } elseif (!$package->hotels->first(fn ($h) => $h->hotel_stars === $stars && $h->isPriced())) {
                $errors['hotel_stars'] = 'Choose one of the listed hotels.';
            }
        } else {
            $rate = $package->rates->first(fn ($r) => $r->days === $days && $r->hotel_stars === $stars);
            if (!$package->rates->contains('days', $days)) {
                $errors['days'] = 'Choose one of the available trip lengths.';
            } elseif (!$rate) {
                $errors['hotel_stars'] = 'That hotel category is not available for a ' . $days . '-day trip.';
            }
        }

        $start = null;
        try {
            $start = Carbon::createFromFormat('Y-m-d', (string) ($input['start_date'] ?? ''))->startOfDay();
        } catch (\Throwable $e) {
        }
        if (!$start) {
            $errors['start_date'] = 'Choose your travel start date.';
        } elseif ($start->lt(now()->startOfDay()->addDay())) {
            $errors['start_date'] = 'The trip must start tomorrow or later.';
        } elseif ($start->gt(now()->addYears(2))) {
            $errors['start_date'] = 'Trips can be booked up to two years ahead.';
        }

        // ---- travellers ----
        $type = (string) ($input['traveler_type'] ?? '');
        $adults = (int) ($input['adults'] ?? 0);
        $ages = array_values(array_map('intval', array_filter((array) ($input['child_ages'] ?? []), fn ($a) => $a !== '' && $a !== null)));
        $children = $infants = $sharing = 0;
        foreach ($ages as $age) {
            if ($age < 0 || $age > 17) {
                $errors['child_ages'] = 'Children\'s ages must be between 0 and 17.';
                break;
            }
            if ($age <= $package->infant_max_age) {
                $infants++;
            } elseif ($age <= $package->child_max_age) {
                $children++;
                if ($age <= (int) $package->child_sharing_max_age) {
                    $sharing++; // sleeps in the parents' bed: no hotel cost
                }
            } else {
                $adults++; // older children pay the adult rate
            }
        }
        $kids = count($ages);
        $everyone = (int) ($input['adults'] ?? 0) + $kids;

        if ($checkTravellerType && !in_array($type, self::TRAVELER_TYPES, true)) {
            $errors['traveler_type'] = 'Choose who is travelling.';
        } elseif ((int) ($input['adults'] ?? 0) < 1) {
            $errors['adults'] = 'At least one adult must travel.';
        } elseif (!$checkTravellerType) {
            // Staff price check: any mix of travellers.
        } elseif ($type === 'solo' && ($input['adults'] != 1 || $kids)) {
            $errors['traveler_type'] = 'A solo trip is for one adult.';
        } elseif ($type === 'couple' && ($input['adults'] != 2 || $kids)) {
            $errors['traveler_type'] = 'A couple trip is for two adults.';
        } elseif ($type === 'family' && $kids < 1) {
            $errors['traveler_type'] = 'Add the children\'s ages for a family trip.';
        } elseif ($type === 'group' && $everyone < 5) {
            $errors['traveler_type'] = 'A group is five people or more.';
        } elseif ($everyone > self::MAX_TRAVELLERS) {
            $errors['adults'] = 'For more than ' . self::MAX_TRAVELLERS . ' travellers, please contact us.';
        }
        $paying = $adults + $children;

        // ---- hotel, meal plan, add-ons ----
        $hotel = $package->hotels->firstWhere('hotel_stars', $stars);
        $mealCodes = $hotel ? $hotel->mealPlanCodes() : array_values(array_unique(array_merge(['breakfast'],
            $package->addons->where('category', 'meal_plan')->pluck('code')->all())));
        $meal = (string) ($input['meal_plan'] ?? ($mealCodes[0] ?? 'breakfast'));
        if (!in_array($meal, $mealCodes, true)) {
            $errors['meal_plan'] = 'That meal plan is not offered by the ' . $stars . '-star hotel.';
        }

        $pick = function (string $category, ?string $code) use ($package, &$errors) {
            $options = $package->addons->where('category', $category)->values();
            if ($options->isEmpty()) {
                return null;
            }
            $chosen = $code === null || $code === ''
                ? ($options->firstWhere('net_price', 0.0) ?? $options->first())
                : $options->firstWhere('code', $code);
            if (!$chosen) {
                $errors[$category] = 'Choose one of the listed options.';
            }
            return $chosen;
        };
        $guide = $pick('guide_language', $input['guide_language'] ?? null);
        $transport = $pick('transport', $input['transport'] ?? null);

        $wantsTransfer = filter_var($input['airport_transfer'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $transfer = $wantsTransfer ? $package->addons->firstWhere('category', 'airport_transfer') : null;
        if ($wantsTransfer && !$transfer) {
            $errors['airport_transfer'] = 'Airport transfer is not offered for this package.';
        }

        $insuranceAddon = $package->addons->firstWhere('category', 'insurance');
        $wantsInsurance = $package->insurance_required || filter_var($input['insurance'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if ($wantsInsurance && !$insuranceAddon) {
            $errors['insurance'] = 'Travel insurance is not set up for this package yet. Please contact us.';
        }

        if ($errors) {
            return ['ok' => false, 'errors' => $errors, 'quote' => null];
        }

        // ---- price it ----
        $lines = [];
        $line = function (string $key, string $label, string $detail, float $net) use (&$lines, $sell) {
            if ($net <= 0) {
                return;
            }
            $lines[] = ['key' => $key, 'label' => $label, 'detail' => $detail, 'net' => round($net, 2), 'amount' => $sell($net)];
        };

        $roomPlan = null;
        if ($rooms) {
            $hotelRow = $package->hotels->first(fn ($h) => $h->hotel_stars === $stars);
            $nights = max(1, $days - 1);
            $roomPlan = self::allocateRooms($adults + $children - $sharing, $hotelRow);
            $hotelNet = $nights * $roomPlan['per_night'];
            $line('hotel', "Hotel, {$nights} " . ($nights === 1 ? 'night' : 'nights') . ' · ' . $stars . '★ ' . $hotelRow->name, $roomPlan['label'], $hotelNet);

            $group = $adults + $children;
            $land = self::landPrice($package, $days, $group);
            $childLand = round($land * (float) $package->child_land_percent / 100, 2);
            $line('land_adults', "Tours, transfers and guide, {$days} days", self::count($adults, 'adult') . ' × AED ' . number_format($land * (1 + $margin), 2) . ' (group of ' . $group . ')', $adults * $land);
            $line('land_children', 'Tours, transfers and guide: children', self::count($children, 'child', 'children') . ' at ' . rtrim(rtrim(number_format($package->child_land_percent, 2), '0'), '.') . '% of the adult price', $children * $childLand);
            $rateNet = $hotelNet + $adults * $land + $children * $childLand;
        } else {
            $tier = $stars . '-star hotel';
            $line('adults', "Package, {$days} days, {$tier}", self::count($adults, 'adult'), $adults * $rate->adult_net);
            $line('children', 'Children (ages ' . ($package->infant_max_age + 1) . '–' . $package->child_max_age . ')', self::count($children, 'child', 'children'), $children * $rate->child_net);
            $line('infants', 'Infants (under ' . ($package->infant_max_age + 1) . ')', self::count($infants, 'infant'), $infants * $rate->infant_net);
            if ($adults % 2 === 1) {
                $line('single_supplement', 'Single room supplement', 'one adult in a room of their own', $rate->single_supplement_net);
            }

            $rateNet = $adults * $rate->adult_net + $children * $rate->child_net + $infants * $rate->infant_net
                + ($adults % 2 === 1 ? $rate->single_supplement_net : 0);
        }
        $season = $package->seasons
            ->filter(fn ($s) => $start->between($s->starts_on->copy()->startOfDay(), $s->ends_on->copy()->endOfDay()))
            ->sortByDesc('surcharge_percent')->first();
        if ($season) {
            $line('season', 'Peak season: ' . $season->name, '+' . rtrim(rtrim(number_format($season->surcharge_percent, 2), '0'), '.') . '% on the package', $rateNet * $season->surcharge_percent / 100);
        }

        $units = fn (TourPackageAddon $a) => match ($a->charge_basis) {
            'per_person' => $paying,
            'per_person_per_day' => $paying * $days,
            'per_day' => $days,
            default => 1,
        };
        $addonLine = function (string $key, string $label, ?TourPackageAddon $a) use ($line, $units) {
            if ($a && $a->net_price > 0) {
                $line($key, $label . ': ' . $a->label, TourPackageAddon::BASES[$a->charge_basis] ?? '', $a->net_price * $units($a));
            }
        };
        $addonLine('guide_language', 'Guide', $guide);
        $addonLine('transport', 'Transport', $transport);
        $addonLine('meal_plan', 'Meals', $package->addons->first(fn ($a) => $a->category === 'meal_plan' && $a->code === $meal));
        $addonLine('airport_transfer', 'Airport transfer', $transfer);
        $addonLine('insurance', 'Insurance', $wantsInsurance ? $insuranceAddon : null);

        $visa = null;
        $nationality = trim((string) ($input['nationality'] ?? ''));
        if ($nationality !== '' && $package->country) {
            $visa = VisaEligibility::check((string) $package->country, $nationality);
        }

        return ['ok' => true, 'errors' => [], 'quote' => [
            'currency'   => 'AED',
            'start_date' => $start->toDateString(),
            'end_date'   => $start->copy()->addDays($days - 1)->toDateString(),
            'days'       => $days,
            'nights'     => $days - 1,
            'hotel_stars' => $stars,
            'hotel'      => $hotel ? [
                'name' => $hotel->name, 'location' => $hotel->location,
                'check_in' => $hotel->check_in_time, 'check_out' => $hotel->check_out_time,
                'late_check_in' => $hotel->late_check_in_policy, 'late_check_out' => $hotel->late_check_out_policy,
            ] : null,
            'rooms'      => $roomPlan['label'] ?? null,
            'traveler_type' => $type,
            'adults'     => $adults,
            'children'   => $children,
            'infants'    => $infants,
            'child_ages' => $ages,
            'selections' => [
                'meal_plan'        => $meal,
                'meal_plan_label'  => TourPackageHotel::MEAL_PLANS[$meal] ?? $meal,
                'guide_language'   => $guide?->code,
                'guide_label'      => $guide?->label,
                'transport'        => $transport?->code,
                'transport_label'  => $transport?->label,
                'airport_transfer' => (bool) $transfer,
                'insurance'        => (bool) ($wantsInsurance && $insuranceAddon),
            ],
            'season'     => $season?->name,
            'lines'      => array_map(fn ($l) => array_diff_key($l, ['net' => 1]), $lines),
            'net_lines'  => $lines,
            'net_total'  => round(array_sum(array_column($lines, 'net')), 2),
            'total'      => round(array_sum(array_column($lines, 'amount')), 2),
            'visa'       => $visa,
        ]];
    }

    /**
     * Rooms for the guests who need a bed (adults and children too old to share
     * the parents' bed). Two to a double; an odd guest out makes one room a
     * triple, or gets an extra bed, or a single room, whichever the hotel has.
     *
     * @return array{rooms: array<int,string>, extra_beds: int, per_night: float, label: string}
     */
    public static function allocateRooms(int $guests, \App\Models\TourPackageHotel $hotel): array
    {
        $guests = max(1, $guests);
        $single = (float) $hotel->single_net > 0 ? (float) $hotel->single_net : null;
        $double = (float) $hotel->double_net;
        $triple = (float) $hotel->triple_net > 0 ? (float) $hotel->triple_net : null;
        $extra = (float) $hotel->extra_bed_net > 0 ? (float) $hotel->extra_bed_net : null;

        $rooms = [];
        $extraBeds = 0;
        if ($guests === 1) {
            $rooms[] = $single ? 'single' : 'double';
        } else {
            $doubles = intdiv($guests, 2);
            $odd = $guests % 2 === 1;
            $rooms = array_fill(0, $doubles, 'double');
            if ($odd) {
                if ($triple) {
                    $rooms[0] = 'triple';
                } elseif ($extra) {
                    $extraBeds = 1;
                } else {
                    $rooms[] = $single ? 'single' : 'double';
                }
            }
        }

        $price = ['single' => $single ?? $double, 'double' => $double, 'triple' => $triple ?? $double];
        $perNight = array_sum(array_map(fn ($r) => $price[$r], $rooms)) + $extraBeds * ($extra ?? 0);

        $counts = array_count_values($rooms);
        $names = ['single' => 'Single', 'double' => 'Double', 'triple' => 'Triple'];
        $parts = [];
        foreach (['single', 'double', 'triple'] as $t) {
            if (!empty($counts[$t])) {
                $parts[] = $counts[$t] . ' ' . $names[$t] . ' room' . ($counts[$t] > 1 ? 's' : '');
            }
        }
        if ($extraBeds) {
            $parts[] = $extraBeds . ' extra bed';
        }

        return ['rooms' => $rooms, 'extra_beds' => $extraBeds, 'per_night' => round($perNight, 2), 'label' => implode(' + ', $parts)];
    }

    /**
     * Land price per adult (NET, whole trip) for a group of this size: the band
     * with the largest starting size that fits, or the smallest band set.
     */
    public static function landPrice(\App\Models\TravelPackage $package, int $days, int $group): float
    {
        $rows = $package->landRates->where('days', $days)->sortBy('min_group');
        $fit = $rows->filter(fn ($r) => $r->min_group <= max(1, $group))->last() ?? $rows->first();
        return (float) ($fit?->adult_net ?? 0);
    }

    /** Selling price per adult, two adults sharing a double, shortest trip (for "From AED x"). */
    public static function fromPerAdult(\App\Models\TravelPackage $package, int $stars): ?float
    {
        $hotel = $package->hotels->first(fn ($h) => $h->hotel_stars === $stars && $h->isPriced());
        $days = $package->landRates->pluck('days')->unique()->sort()->values();
        if (!$hotel || $days->isEmpty()) {
            return null;
        }
        $margin = 1 + max(0.0, (float) $package->margin_percent) / 100;
        return $days->map(fn ($d) => round(((max(1, $d - 1) * (float) $hotel->double_net) / 2 + self::landPrice($package, $d, 2)) * $margin, 2))->min();
    }

    public static function visaPayload(?DestinationVisaRule $rule): array
    {
        if (!$rule) {
            return ['known' => false, 'tone' => 'unknown', 'label' => 'Our visa team will check this passport for you', 'requirement' => null,
                'notes' => 'You can carry on and choose your package. We confirm the visa with you before you travel.'];
        }
        return [
            'known'       => true,
            'tone'        => $rule->tone(),
            'requirement' => $rule->requirement,
            'label'       => $rule->label(),
            'max_stay_days'   => $rule->max_stay_days,
            'processing_days' => $rule->processing_days,
            'fee'         => $rule->fee,
            'documents'   => $rule->documents,
            'notes'       => $rule->notes,
        ];
    }

    private static function count(int $n, string $one, ?string $many = null): string
    {
        return $n . ' ' . ($n === 1 ? $one : ($many ?? $one . 's'));
    }
}
