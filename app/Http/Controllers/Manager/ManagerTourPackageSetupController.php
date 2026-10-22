<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\TourPackageAddon;
use App\Models\TourPackageDay;
use App\Models\TourPackageHotel;
use App\Models\TourPackageRate;
use App\Models\TourPackageSeason;
use App\Models\TravelPackage;
use App\Services\TourPackages\RateSheetImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Manager setup for a booking-wizard package: net rates (typed or imported
 * from the supplier's sheet), hotels per tier, add-ons, itinerary and
 * seasons. Each section saves on its own and replaces that section whole,
 * so what is on screen is exactly what customers are priced from.
 */
class ManagerTourPackageSetupController extends Controller
{
    private function package(int $id): TravelPackage
    {
        // TravelPackage is tenant-scoped: another tenant's id is a 404 here.
        $package = TravelPackage::where('isActive', 1)->where('package_type', 'wizard')->findOrFail($id);
        return $package->load(['rates', 'hotels', 'addons', 'itineraryDays', 'seasons']);
    }

    public function show(int $id): View
    {
        $package = $this->package($id);
        return view('manager.packages.booking-setup', [
            'package'    => $package,
            'mealPlans'  => TourPackageHotel::MEAL_PLANS,
            'categories' => TourPackageAddon::CATEGORIES,
            'bases'      => TourPackageAddon::BASES,
            'tiers'      => $package->rates->pluck('hotel_stars')->unique()->sort()->values(),
            'durations'  => $package->rates->pluck('days')->unique()->sort()->values(),
        ]);
    }

    public function saveRates(Request $request, int $id): RedirectResponse
    {
        $package = $this->package($id);
        $rows = collect($request->input('rates', []))->filter(fn ($r) => filled($r['days'] ?? null) || filled($r['adult_net'] ?? null))->values();
        $request->merge(['rates' => $rows->all()]);
        $request->validate([
            'rates'                         => 'array|max:200',
            'rates.*.days'                  => 'required|integer|min:1|max:60',
            'rates.*.hotel_stars'           => 'required|integer|min:1|max:5',
            'rates.*.adult_net'             => 'required|numeric|min:0|max:1000000',
            'rates.*.child_net'             => 'nullable|numeric|min:0|max:1000000',
            'rates.*.infant_net'            => 'nullable|numeric|min:0|max:1000000',
            'rates.*.single_supplement_net' => 'nullable|numeric|min:0|max:1000000',
        ], [], ['rates.*.days' => 'days', 'rates.*.hotel_stars' => 'hotel stars', 'rates.*.adult_net' => 'adult net price']);

        $this->replaceRates($package, $rows->all());
        return back()->with('success', $rows->count() . ' rate rows saved.');
    }

    public function importRates(Request $request, int $id): RedirectResponse
    {
        $package = $this->package($id);
        $request->validate(['rate_sheet' => 'required|file|mimes:csv,txt|max:2048'], [
            'rate_sheet.mimes' => 'Upload the rate sheet as a .csv file (in Excel: File → Save As → CSV).',
        ]);

        $result = RateSheetImport::parse((string) file_get_contents($request->file('rate_sheet')->getRealPath()));
        if ($result['errors']) {
            throw ValidationException::withMessages(['rate_sheet' => $result['errors']]);
        }
        $this->replaceRates($package, $result['rows']);
        return back()->with('success', count($result['rows']) . ' rate rows imported from the sheet. They replace the previous rates.');
    }

    private function replaceRates(TravelPackage $package, array $rows): void
    {
        $seen = [];
        foreach ($rows as $r) {
            $key = (int) $r['days'] . '/' . (int) $r['hotel_stars'];
            if (isset($seen[$key])) {
                throw ValidationException::withMessages(['rates' => "There are two rows for {$r['days']} days at {$r['hotel_stars']} stars. Keep one."]);
            }
            $seen[$key] = true;
        }
        DB::transaction(function () use ($package, $rows) {
            TourPackageRate::where('package_id', $package->id)->delete();
            foreach ($rows as $r) {
                TourPackageRate::create([
                    'package_id' => $package->id,
                    'days' => (int) $r['days'], 'hotel_stars' => (int) $r['hotel_stars'],
                    'adult_net' => (float) $r['adult_net'], 'child_net' => (float) ($r['child_net'] ?? 0),
                    'infant_net' => (float) ($r['infant_net'] ?? 0), 'single_supplement_net' => (float) ($r['single_supplement_net'] ?? 0),
                ]);
            }
        });
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
                $stars = (int) $stars;
                if ($stars < 1 || $stars > 5) {
                    continue;
                }
                if (!filled($h['name'] ?? null)) {
                    TourPackageHotel::where('package_id', $package->id)->where('hotel_stars', $stars)->delete();
                    continue;
                }
                TourPackageHotel::updateOrCreate(['package_id' => $package->id, 'hotel_stars' => $stars], [
                    'name' => $h['name'], 'location' => $h['location'] ?? null,
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
                : (Str::slug(($a['code'] ?? '') ?: $a['label'], '_') ?: 'option');
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
