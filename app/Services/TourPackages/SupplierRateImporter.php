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
        'details' => 'Description, included and not included',
        'photos'  => 'Photos (their cover photo first)',
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
            'details' => filled($s->get('package.description')) || filled($s->get('package.included')) || filled($s->get('package.excluded')),
            'photos'  => (bool) self::photos($s),
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
        $days = collect($s->get('tours', []))->pluck('days')->filter()->sort()->values();
        $duration = $days->isEmpty() ? 'Flexible' : ($days->first() === $days->last() ? $days->first() . ' days' : $days->first() . '–' . $days->last() . ' days');

        $package = TravelPackage::create(self::details($s) + [
            'title' => $s->get('package.name'),
            'country' => $s->get('package.country'),
            'package_type' => 'wizard',
            'price' => 0,
            'duration' => $duration,
            'margin_percent' => 15,
            'child_max_age' => 11,
            'infant_max_age' => 1,
            'isActive' => 1,
            'createdBy' => $by ?? 'manager',
            'createdDate' => now(),
        ]);
        self::applyPhotos($s, $package);
        return $package;
    }

    /** Description (as HTML, with the places), included and not included. */
    private static function details(SupplierSubmission $s): array
    {
        $text = collect([
            $s->get('package.description'),
            filled($s->get('package.places')) ? 'Places: ' . $s->get('package.places') : null,
        ])->filter()->join("\n\n");
        return [
            'description' => TravelPackage::textToHtml($text !== '' ? $text : (string) $s->get('package.name')),
            'included' => $s->get('package.included') ?: null,
            'excluded' => $s->get('package.excluded') ?: null,
        ];
    }

    /** The supplier's photos still on disk; the one they chose as cover is first. */
    private static function photos(SupplierSubmission $s): array
    {
        return array_values(array_filter((array) $s->photos, fn ($p) => is_file(storage_path('app/' . $p['path']))));
    }

    /** Cover = the supplier's chosen photo, gallery = the next 7. Replaces what the package had. */
    private static function applyPhotos(SupplierSubmission $s, TravelPackage $package): void
    {
        $photos = self::photos($s);
        if (!$photos) {
            return;
        }
        $old = array_merge([$package->image], $package->images()->pluck('image_path')->all());
        $package->images()->delete();
        $package->update(['image' => self::publish($photos[0]['path'])]);
        foreach (array_slice($photos, 1, 7) as $i => $p) {
            $package->images()->create(['image_path' => self::publish($p['path']), 'sort_order' => $i + 1]);
        }
        foreach (array_filter($old) as $path) {
            if (str_starts_with($path, 'assets/packages/') && File::exists(public_path($path))) {
                File::delete(public_path($path));
            }
        }
    }

    /** @return array<int, string> what was filled in, for the success message */
    public static function apply(SupplierSubmission $s, TravelPackage $package, float $toAed, array $sections): array
    {
        $aed = fn ($v) => $v === null || $v === '' ? null : round((float) $v * $toAed, 2);
        $done = [];

        DB::transaction(function () use ($s, $package, $sections, $aed, &$done) {
            if (in_array('details', $sections, true) && isset(self::available($s)['details'])) {
                $package->update(self::details($s));
                $done[] = 'description, included and not included';
            }

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
                // Free up to child_free_age (sharing the parents' bed), the child % up to
                // the age before child_adult_age, the adult price from child_adult_age.
                $free = $s->get('child_free_age');
                $adultFrom = $s->get('child_adult_age');
                $package->update(array_filter([
                    'child_land_percent' => $s->get('child_percent'),
                    'child_sharing_max_age' => $free,
                    'child_max_age' => $adultFrom !== null ? max(1, (int) $adultFrom - 1) : null,
                    'infant_max_age' => $free !== null ? min((int) $free, 5, max(0, (int) ($adultFrom ?? 12) - 2)) : null,
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

        if (in_array('photos', $sections, true) && self::photos($s)) {
            self::applyPhotos($s, $package);
            $done[] = 'photos';
        }

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
