<?php

namespace App\Services\TourPackages;

use App\Models\SupplierSubmission;
use App\Models\TourPackageAddon;
use App\Models\TourPackageDay;
use App\Models\TourPackageHotel;
use App\Models\TourPackageLandRate;
use App\Models\TourPackageRate;
use App\Models\TourPackageSeason;
use App\Models\TravelPackage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Puts a supplier's form into a tour package's Booking setup: hotels and
 * room prices, tour prices, extras, itinerary and peak seasons. Prices are
 * converted to AED at the rate staff confirm. Each section replaces that
 * section of the package, exactly like saving it in Booking setup.
 */
class SupplierRateImporter
{
    public const SECTIONS = [
        'hotels'  => 'Hotels and room prices',
        'tours'   => 'Tour prices and child prices',
        'extras'  => 'Extras',
        'days'    => 'Day-by-day plan',
        'seasons' => 'Peak seasons',
    ];

    /** Sections this submission has something for. */
    public static function available(SupplierSubmission $s): array
    {
        return array_filter([
            'hotels'  => (bool) $s->hotels(),
            'tours'   => (bool) $s->get('tours'),
            'extras'  => (bool) $s->get('extras'),
            'days'    => (bool) $s->get('days'),
            'seasons' => (bool) $s->get('seasons'),
        ]);
    }

    /** A new booking-wizard package built from the supplier's package details and photos. */
    public static function createPackage(SupplierSubmission $s, ?string $by): TravelPackage
    {
        $text = collect([
            $s->get('package.description'),
            filled($s->get('package.places')) ? 'Places: ' . $s->get('package.places') : null,
            filled($s->get('package.included')) ? "Included:\n" . $s->get('package.included') : null,
            filled($s->get('package.excluded')) ? "Not included:\n" . $s->get('package.excluded') : null,
        ])->filter()->join("\n\n");
        $days = collect($s->get('tours', []))->pluck('days')->filter()->sort()->values();
        $duration = $days->isEmpty() ? 'Flexible' : ($days->first() === $days->last() ? $days->first() . ' days' : $days->first() . '–' . $days->last() . ' days');

        $photos = array_values(array_filter((array) $s->photos, fn ($p) => is_file(storage_path('app/' . $p['path']))));
        $package = TravelPackage::create([
            'title' => $s->get('package.name'),
            'country' => $s->get('package.country'),
            'package_type' => 'wizard',
            'price' => 0,
            'description' => $text !== '' ? $text : $s->get('package.name'),
            'duration' => $duration,
            'margin_percent' => 15,
            'child_max_age' => 11,
            'infant_max_age' => 1,
            'image' => isset($photos[0]) ? self::publish($photos[0]['path']) : null,
            'isActive' => 1,
            'createdBy' => $by ?? 'manager',
            'createdDate' => now(),
        ]);
        foreach (array_slice($photos, 1, 7) as $i => $p) {
            $package->images()->create(['image_path' => self::publish($p['path']), 'sort_order' => $i + 1]);
        }
        return $package;
    }

    /** @return array<int, string> what was filled in, for the success message */
    public static function apply(SupplierSubmission $s, TravelPackage $package, float $toAed, array $sections): array
    {
        $aed = fn ($v) => $v === null || $v === '' ? null : round((float) $v * $toAed, 2);
        $done = [];

        DB::transaction(function () use ($s, $package, $sections, $aed, &$done) {
            if (in_array('hotels', $sections, true) && $s->hotels()) {
                $keep = [];
                foreach ($s->hotels() as $stars => $h) {
                    $hotel = TourPackageHotel::firstOrNew(['package_id' => $package->id, 'hotel_stars' => (int) $stars]);
                    $positive = fn ($v) => ($x = $aed($v)) !== null && $x > 0 ? $x : null;
                    $hotel->fill([
                        'name' => $h['name'], 'location' => $h['location'] ?? null,
                        'single_net' => $positive($h['single'] ?? null), 'double_net' => $positive($h['double'] ?? null),
                        'triple_net' => $positive($h['triple'] ?? null), 'extra_bed_net' => $positive($h['extra_bed'] ?? null),
                        'check_in_time' => ($h['check_in'] ?? null) ?: '14:00', 'check_out_time' => ($h['check_out'] ?? null) ?: '12:00',
                        'meal_plans' => ($h['meals'] ?? []) ?: ['breakfast'],
                        'late_check_in_policy' => $h['late_policy'] ?? null,
                    ]);
                    $hotel->save();
                    $keep[] = (int) $stars;
                }
                TourPackageHotel::where('package_id', $package->id)->whereNotIn('hotel_stars', $keep)->delete();
                $done[] = count($keep) . ' ' . (count($keep) === 1 ? 'hotel' : 'hotels');
            }

            if (in_array('tours', $sections, true) && $s->get('tours')) {
                TourPackageLandRate::where('package_id', $package->id)->delete();
                foreach ($s->get('tours') as $t) {
                    foreach (array_keys(TourPackageLandRate::GROUPS) as $g) {
                        if (isset($t["g$g"]) && $t["g$g"] !== null) {
                            TourPackageLandRate::create(['package_id' => $package->id, 'days' => (int) $t['days'], 'min_group' => $g, 'adult_net' => $aed($t["g$g"])]);
                        }
                    }
                }
                $package->update(array_filter([
                    'child_land_percent' => $s->get('child_percent'),
                    'child_sharing_max_age' => $s->get('child_free_age'),
                ], fn ($v) => $v !== null));
                $done[] = 'tour prices for ' . count($s->get('tours')) . ' trip ' . (count($s->get('tours')) === 1 ? 'length' : 'lengths');
            }

            if (in_array('extras', $sections, true) && $s->get('extras')) {
                // Insurance is ours, not the supplier's: keep it.
                TourPackageAddon::where('package_id', $package->id)->where('category', '!=', 'insurance')->delete();
                $used = [];
                foreach ($s->get('extras') as $i => $e) {
                    $code = $e['category'] === 'meal_plan'
                        ? $e['meal_code']
                        : (trim(substr(Str::slug($e['name'], '_'), 0, 36), '_') ?: 'option');
                    $key = $e['category'] . '/' . $code;
                    if (isset($used[$key])) {
                        if ($e['category'] === 'meal_plan') {
                            continue; // one supplement per meal plan
                        }
                        $code = substr($code, 0, 36) . '_' . ($i + 1);
                        $key = $e['category'] . '/' . $code;
                    }
                    $used[$key] = true;
                    TourPackageAddon::create([
                        'package_id' => $package->id, 'category' => $e['category'], 'code' => $code,
                        'label' => $e['name'], 'net_price' => $aed($e['price']) ?? 0,
                        'charge_basis' => $e['basis'], 'sort_order' => $i,
                    ]);
                }
                // Guide and transport are "pick one" in the booking: when the supplier only
                // listed paid upgrades, the one in the tour price must be there as the default,
                // or every customer would be charged the upgrade.
                $included = ['guide_language' => 'Guide included in the tour', 'transport' => 'Car included in the tour'];
                foreach ($included as $cat => $label) {
                    $mine = collect($s->get('extras'))->where('category', $cat);
                    if ($mine->isNotEmpty() && !$mine->contains(fn ($e) => (float) $e['price'] === 0.0)) {
                        TourPackageAddon::create([
                            'package_id' => $package->id, 'category' => $cat, 'code' => isset($used[$cat . '/included']) ? 'included_in_tour' : 'included',
                            'label' => $label, 'net_price' => 0, 'charge_basis' => 'per_booking', 'sort_order' => -1,
                        ]);
                    }
                }
                $done[] = count($used) . ' extras';
            }

            if (in_array('days', $sections, true) && $s->get('days')) {
                TourPackageDay::where('package_id', $package->id)->get()->each(function ($d) {
                    if ($d->image_path && str_starts_with($d->image_path, 'assets/packages/') && File::exists(public_path($d->image_path))) {
                        File::delete(public_path($d->image_path));
                    }
                    $d->delete();
                });
                foreach ($s->get('days') as $d) {
                    TourPackageDay::create(['package_id' => $package->id, 'day_number' => $d['day'], 'title' => $d['title'], 'description' => $d['description']]);
                }
                $done[] = count($s->get('days')) . '-day plan';
            }

            if (in_array('seasons', $sections, true) && $s->get('seasons')) {
                TourPackageSeason::where('package_id', $package->id)->delete();
                foreach ($s->get('seasons') as $x) {
                    TourPackageSeason::create(['package_id' => $package->id, 'name' => $x['name'], 'starts_on' => $x['from'], 'ends_on' => $x['to'], 'surcharge_percent' => $x['percent']]);
                }
                $done[] = count($s->get('seasons')) . ' peak periods';
            }
        });

        // Same as Booking setup: once room and tour prices exist, the old per-person rates go.
        $fresh = TravelPackage::with(['hotels', 'landRates'])->find($package->id);
        if ($fresh && $fresh->usesRoomPricing()) {
            TourPackageRate::where('package_id', $package->id)->delete();
        }
        return $done;
    }

    /** Copies a supplier photo into the public package images folder. */
    private static function publish(string $path): string
    {
        $dir = 'assets/packages/' . (current_company()?->id ?? 0);
        File::ensureDirectoryExists(public_path($dir), 0755);
        $name = time() . '_' . Str::random(10) . '.' . strtolower(pathinfo($path, PATHINFO_EXTENSION));
        File::copy(storage_path('app/' . $path), public_path($dir . '/' . $name));
        return $dir . '/' . $name;
    }
}
