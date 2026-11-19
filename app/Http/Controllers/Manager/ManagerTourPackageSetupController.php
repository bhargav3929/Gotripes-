<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\TourPackageAddon;
use App\Models\TourPackageDay;
use App\Models\TourPackageHotel;
use App\Models\TourPackageLandRate;
use App\Models\TourPackageRate;
use App\Models\TourPackageSeason;
use App\Models\TravelPackage;
use App\Services\TourPackages\TourPackageQuote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Manager setup for a booking-wizard package: hotels with NET room prices
 * per night, tour/land prices per adult by group size, hotel details,
 * add-ons, itinerary and seasons. Each section saves on its own and replaces that section whole,
 * so what is on screen is exactly what customers are priced from.
 */
class ManagerTourPackageSetupController extends Controller
{
    private function package(int $id): TravelPackage
    {
        // TravelPackage is tenant-scoped: another tenant's id is a 404 here.
        $package = TravelPackage::where('isActive', 1)->where('package_type', 'wizard')->findOrFail($id);
        return $package->load(['rates', 'landRates', 'hotels', 'addons', 'itineraryDays', 'seasons']);
    }

    public function show(int $id): View
    {
        $package = $this->package($id);
        $priced = $package->hotels->filter(fn ($h) => $h->isPriced())->values();
        $legacy = !$package->usesRoomPricing() && $package->rates->isNotEmpty();

        return view('manager.packages.booking-setup', [
            'package'      => $package,
            'mealPlans'    => TourPackageHotel::MEAL_PLANS,
            'categories'   => TourPackageAddon::CATEGORIES,
            'bases'        => TourPackageAddon::BASES,
            'groups'       => TourPackageLandRate::GROUPS,
            'pricedHotels' => $priced,
            'priceCheck'   => $this->priceCheck($package, $priced),
            'tiers'        => $legacy ? $package->rates->pluck('hotel_stars')->unique()->sort()->values() : $package->hotels->pluck('hotel_stars')->sort()->values(),
            'durations'    => $package->landRates->isNotEmpty() ? $package->landRates->pluck('days')->unique()->sort()->values() : $package->rates->pluck('days')->unique()->sort()->values(),
        ]);
    }

    /** Hotels sold with this package and their NET room prices per night. */
    public function saveRooms(Request $request, int $id): RedirectResponse
    {
        $package = $this->package($id);
        $request->validate([
            'hotels'                 => 'array',
            'hotels.*.name'          => 'nullable|string|max:190',
            'hotels.*.location'      => 'nullable|string|max:190',
            'hotels.*.single_net'    => 'nullable|numeric|min:0|max:1000000',
            'hotels.*.double_net'    => 'nullable|numeric|min:0|max:1000000',
            'hotels.*.triple_net'    => 'nullable|numeric|min:0|max:1000000',
            'hotels.*.extra_bed_net' => 'nullable|numeric|min:0|max:1000000',
        ]);
        $missing = collect((array) $request->input('hotels', []))
            ->filter(fn ($h) => filled($h['name'] ?? null) && !((float) ($h['double_net'] ?? 0) > 0))
            ->keys();
        if ($missing->isNotEmpty()) {
            throw ValidationException::withMessages(['hotels' => 'Enter the double room price for the ' . $missing->map(fn ($s) => $s . '-star')->join(', ', ' and ') . ' hotel.']);
        }

        $count = 0;
        DB::transaction(function () use ($request, $package, &$count) {
            foreach ((array) $request->input('hotels', []) as $stars => $h) {
                $stars = (int) $stars;
                if ($stars < 1 || $stars > 5) {
                    continue;
                }
                if (!filled($h['name'] ?? null)) {
                    TourPackageHotel::where('package_id', $package->id)->where('hotel_stars', $stars)->delete();
                    continue;
                }
                $money = fn ($k) => filled($h[$k] ?? null) && (float) $h[$k] > 0 ? round((float) $h[$k], 2) : null;
                $hotel = TourPackageHotel::firstOrNew(['package_id' => $package->id, 'hotel_stars' => $stars]);
                $hotel->fill([
                    'name' => $h['name'], 'location' => $h['location'] ?? null,
                    'single_net' => $money('single_net'), 'double_net' => $money('double_net'),
                    'triple_net' => $money('triple_net'), 'extra_bed_net' => $money('extra_bed_net'),
                ]);
                if (!$hotel->exists) {
                    $hotel->fill(['check_in_time' => '14:00', 'check_out_time' => '12:00', 'meal_plans' => ['breakfast']]);
                }
                $hotel->save();
                $count++;
            }
        });
        $this->retireOldRates($package);
        return back()->with('success', $count . ' ' . ($count === 1 ? 'hotel' : 'hotels') . ' and room prices saved.');
    }

    /** Tours, transfers and guide: NET per adult for the whole trip, by trip length and group size. */
    public function saveLand(Request $request, int $id): RedirectResponse
    {
        $package = $this->package($id);
        $bands = array_keys(TourPackageLandRate::GROUPS);
        $rows = collect($request->input('land', []))
            ->filter(fn ($r) => filled($r['days'] ?? null) || collect($bands)->contains(fn ($g) => filled($r['g' . $g] ?? null)))
            ->values();
        $request->merge(['land' => $rows->all()]);
        $rules = [
            'land'                  => 'array|max:30',
            'land.*.days'           => 'required|integer|min:1|max:60',
            'child_land_percent'    => 'required|numeric|min:0|max:100',
            'child_sharing_max_age' => 'required|integer|min:0|max:17',
        ];
        foreach ($bands as $g) {
            $rules['land.*.g' . $g] = 'nullable|numeric|min:0|max:1000000';
        }
        $request->validate($rules, [], ['land.*.days' => 'trip length']);

        $seen = [];
        foreach ($rows as $r) {
            $d = (int) $r['days'];
            if (isset($seen[$d])) {
                throw ValidationException::withMessages(['land' => "There are two rows for {$d} days. Keep one."]);
            }
            $seen[$d] = true;
            if (!collect($bands)->contains(fn ($g) => filled($r['g' . $g] ?? null))) {
                throw ValidationException::withMessages(['land' => "Enter the price for the {$d}-day trip (0 if the hotel is all you sell)."]);
            }
        }

        DB::transaction(function () use ($package, $rows, $bands, $request) {
            TourPackageLandRate::where('package_id', $package->id)->delete();
            foreach ($rows as $r) {
                foreach ($bands as $g) {
                    if (filled($r['g' . $g] ?? null)) {
                        TourPackageLandRate::create(['package_id' => $package->id, 'days' => (int) $r['days'],
                            'min_group' => $g, 'adult_net' => round((float) $r['g' . $g], 2)]);
                    }
                }
            }
            $package->update([
                'child_land_percent' => (float) $request->input('child_land_percent'),
                'child_sharing_max_age' => (int) $request->input('child_sharing_max_age'),
            ]);
        });
        $this->retireOldRates($package);
        return back()->with('success', 'Tour prices saved for ' . $rows->count() . ' trip ' . ($rows->count() === 1 ? 'length' : 'lengths') . '.');
    }

    /** Once room and group pricing is complete, the old per-person rates are no longer used. */
    private function retireOldRates(TravelPackage $package): void
    {
        $fresh = TravelPackage::with(['hotels', 'landRates'])->find($package->id);
        if ($fresh && $fresh->usesRoomPricing()) {
            TourPackageRate::where('package_id', $package->id)->delete();
        }
    }

    /**
     * Totals for common bookings, for every priced hotel, so staff can check
     * their numbers. Uses the same TourPackageQuote customers are charged by.
     */
    private function priceCheck(TravelPackage $package, $hotels): array
    {
        if (!$package->usesRoomPricing()) {
            return ['rows' => []];
        }
        $days = (int) $package->landRates->min('days');
        // A normal-season date, so the check shows the base prices.
        $start = now()->addDays(30)->startOfDay();
        for ($i = 0; $i < 400 && $package->seasons->contains(fn ($s) => $start->between($s->starts_on->copy()->startOfDay(), $s->ends_on->copy()->endOfDay())); $i++) {
            $start->addDay();
        }
        $cases = [
            ['1 adult', 1, []],
            ['2 adults', 2, []],
            ['3 adults', 3, []],
            ['2 adults + child (8)', 2, [8]],
            ['2 adults + child (4)', 2, [4]],
            ['4 adults', 4, []],
            ['6 adults', 6, []],
        ];
        $rows = [];
        foreach ($cases as [$label, $adults, $ages]) {
            $cells = [];
            foreach ($hotels as $h) {
                $res = TourPackageQuote::make($package, [
                    'start_date' => $start->toDateString(), 'days' => $days, 'hotel_stars' => $h->hotel_stars,
                    'adults' => $adults, 'child_ages' => $ages,
                ]);
                $q = $res['quote'] ?? null;
                $paying = $q ? $q['adults'] + $q['children'] : 0;
                $cells[] = $q ? ['total' => $q['total'], 'rooms' => $q['rooms'], 'per_person' => $paying ? $q['total'] / $paying : $q['total']] : null;
            }
            $rows[] = ['label' => $label, 'cells' => $cells];
        }
        return ['rows' => $rows, 'hotels' => $hotels, 'days' => $days, 'date' => $start->format('j M Y')];
    }

    public function saveHotels(Request $request, int $id): RedirectResponse
    {
        $package = $this->package($id);
        $request->validate([
            'hotels'                           => 'array',
            'hotels.*.name'                    => 'nullable|string|max:190',
            'hotels.*.location'                => 'nullable|string|max:190',
            'hotels.*.check_in_time'           => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'hotels.*.check_out_time'          => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'hotels.*.late_check_in_policy'    => 'nullable|string|max:1000',
            'hotels.*.late_check_out_policy'   => 'nullable|string|max:1000',
            'hotels.*.meal_plans'              => 'nullable|array',
            'hotels.*.meal_plans.*'            => 'in:' . implode(',', array_keys(TourPackageHotel::MEAL_PLANS)),
        ], ['hotels.*.check_in_time.regex' => 'Use 24-hour time like 14:00.', 'hotels.*.check_out_time.regex' => 'Use 24-hour time like 12:00.']);

        DB::transaction(function () use ($request, $package) {
            foreach ((array) $request->input('hotels', []) as $stars => $h) {
                // Names and room prices are set in part 1; this saves the details of hotels already there.
                $hotel = TourPackageHotel::where('package_id', $package->id)->where('hotel_stars', (int) $stars)->first();
                if (!$hotel) {
                    continue;
                }
                $hotel->update([
                    'check_in_time' => ($h['check_in_time'] ?? null) ?: '14:00', 'check_out_time' => ($h['check_out_time'] ?? null) ?: '12:00',
                    'late_check_in_policy' => $h['late_check_in_policy'] ?? null,
                    'late_check_out_policy' => $h['late_check_out_policy'] ?? null,
                    'meal_plans' => array_values($h['meal_plans'] ?? ['breakfast']) ?: ['breakfast'],
                ]);
            }
        });
        return back()->with('success', 'Hotels saved.');
    }

    public function saveAddons(Request $request, int $id): RedirectResponse
    {
        $package = $this->package($id);
        $rows = collect($request->input('addons', []))->filter(fn ($a) => filled($a['label'] ?? null))->values();
        $request->merge(['addons' => $rows->all()]);
        $request->validate([
            'addons'                => 'array|max:100',
            'addons.*.category'     => 'required|in:' . implode(',', array_keys(TourPackageAddon::CATEGORIES)),
            'addons.*.code'         => 'nullable|string|max:40',
            'addons.*.label'        => 'required|string|max:120',
            'addons.*.description'  => 'nullable|string|max:190',
            'addons.*.net_price'    => 'required|numeric|min:0|max:1000000',
            'addons.*.charge_basis' => 'required|in:' . implode(',', array_keys(TourPackageAddon::BASES)),
        ]);

        $codes = [];
        foreach ($rows as $i => $a) {
            // Meal plan supplements must use the plan code so they match the hotel's plans.
            $code = $a['category'] === 'meal_plan'
                ? (string) ($a['code'] ?? '')
                : (trim(substr(Str::slug(($a['code'] ?? '') ?: $a['label'], '_'), 0, 40), '_') ?: 'option'); // column is 40 chars
            if ($a['category'] === 'meal_plan' && !array_key_exists($code, TourPackageHotel::MEAL_PLANS)) {
                throw ValidationException::withMessages(["addons.$i.code" => 'Pick which meal plan this supplement is for.']);
            }
            $k = $a['category'] . '/' . $code;
            if (isset($codes[$k])) {
                throw ValidationException::withMessages(["addons.$i.label" => '"' . $a['label'] . '" appears twice in ' . TourPackageAddon::CATEGORIES[$a['category']] . '.']);
            }
            $codes[$k] = ['code' => $code] + $a + ['sort_order' => $i];
        }

        DB::transaction(function () use ($package, $codes) {
            TourPackageAddon::where('package_id', $package->id)->delete();
            foreach ($codes as $a) {
                TourPackageAddon::create([
                    'package_id' => $package->id, 'category' => $a['category'], 'code' => $a['code'],
                    'label' => $a['label'], 'description' => $a['description'] ?? null,
                    'net_price' => (float) $a['net_price'], 'charge_basis' => $a['charge_basis'], 'sort_order' => $a['sort_order'],
                ]);
            }
        });
        return back()->with('success', count($codes) . ' options saved.');
    }

    public function saveDays(Request $request, int $id): RedirectResponse
    {
        $package = $this->package($id);
        $rows = collect($request->input('days', []))->filter(fn ($d) => filled($d['title'] ?? null))->values();
        $request->validate([
            'days'                 => 'array|max:120',
            'days.*.duration_days' => 'nullable|integer|min:1|max:60',
            'days.*.day_number'    => 'required_with:days.*.title|nullable|integer|min:1|max:60',
            'days.*.title'         => 'nullable|string|max:190',
            'days.*.description'   => 'nullable|string|max:2000',
            'days.*.image'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        DB::transaction(function () use ($request, $package) {
            $keepIds = [];
            foreach ((array) $request->input('days', []) as $i => $d) {
                if (!filled($d['title'] ?? null)) {
                    continue;
                }
                $day = !empty($d['id'])
                    ? TourPackageDay::where('package_id', $package->id)->find($d['id'])
                    : null;
                $day ??= new TourPackageDay(['package_id' => $package->id]);
                $day->fill([
                    'duration_days' => filled($d['duration_days'] ?? null) ? (int) $d['duration_days'] : null,
                    'day_number' => (int) $d['day_number'], 'title' => $d['title'], 'description' => $d['description'] ?? null,
                ]);
                if ($file = $request->file("days.$i.image")) {
                    $this->deleteImage($day->image_path);
                    $day->image_path = $this->storeImage($file);
                } elseif (!empty($d['remove_image'])) {
                    $this->deleteImage($day->image_path);
                    $day->image_path = null;
                }
                $day->save();
                $keepIds[] = $day->id;
            }
            TourPackageDay::where('package_id', $package->id)->whereNotIn('id', $keepIds)->get()
                ->each(function ($d) {
                    $this->deleteImage($d->image_path);
                    $d->delete();
                });
        });
        return back()->with('success', 'Itinerary saved.');
    }

    public function saveSeasons(Request $request, int $id): RedirectResponse
    {
        $package = $this->package($id);
        $rows = collect($request->input('seasons', []))->filter(fn ($s) => filled($s['name'] ?? null))->values();
        $request->merge(['seasons' => $rows->all()]);
        $request->validate([
            'seasons'                     => 'array|max:50',
            'seasons.*.name'              => 'required|string|max:100',
            'seasons.*.starts_on'         => 'required|date',
            'seasons.*.ends_on'           => 'required|date|after_or_equal:seasons.*.starts_on',
            'seasons.*.surcharge_percent' => 'required|numeric|min:0|max:300',
        ]);
        DB::transaction(function () use ($package, $rows) {
            TourPackageSeason::where('package_id', $package->id)->delete();
            foreach ($rows as $s) {
                TourPackageSeason::create(['package_id' => $package->id] + $s);
            }
        });
        return back()->with('success', $rows->count() . ' peak periods saved.');
    }

    private function storeImage($file): string
    {
        $relativeDir = 'assets/packages/' . (current_company()?->id ?? 0) . '/itinerary';
        File::ensureDirectoryExists(public_path($relativeDir), 0755);
        $filename = time() . '_' . Str::random(10) . '.' . strtolower($file->getClientOriginalExtension());
        $file->move(public_path($relativeDir), $filename);
        return $relativeDir . '/' . $filename;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && str_starts_with($path, 'assets/packages/') && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
