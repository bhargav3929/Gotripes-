<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\UAEActivity;
use App\Models\UAEActivityDetail;
use App\Models\Emirates;
use App\Support\CountryCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ManagerActivitiesController extends Controller
{
    /**
     * Display a listing of the manager's own activities, split into two tabs:
     *  1. Activities in the UAE      (country = UAE; legacy rows with no
     *                                 country are treated as UAE)
     *  2. Activities outside the UAE (any other country)
     *
     * Both lists are tenant-scoped (BelongsToCompany) and fully editable.
     */
    public function index()
    {
        $isoMap = collect(CountryCodes::all())->keyBy('name');

        // UAE activities (includes legacy rows with null/blank country).
        $uaeActivities = UAEActivity::with(['details', 'emirate'])
            ->where('isActive', 1)
            ->where(function ($q) {
                $q->whereNull('country')
                  ->orWhere('country', '')
                  ->orWhereRaw('LOWER(TRIM(country)) = ?', ['united arab emirates']);
            })
            ->orderBy('createdDate', 'desc')
            ->paginate(10, ['*'], 'uae_page');

        // Build per-country tabs from the company's allowed_countries setting,
        // excluding UAE (which always has its own tab).
        $allowedCountries = collect(current_company()?->getSetting('allowed_countries', []) ?? [])
            ->filter(fn ($c) => !$this->isUAE($c))
            ->values();

        $countryTabs = $allowedCountries->map(function ($countryName) use ($isoMap) {
            $pageParam  = 'pg_' . Str::slug($countryName);
            $activities = UAEActivity::with(['details'])
                ->where('isActive', 1)
                ->whereRaw('LOWER(TRIM(country)) = ?', [strtolower(trim($countryName))])
                ->orderBy('createdDate', 'desc')
                ->paginate(10, ['*'], $pageParam);

            $entry   = $isoMap[$countryName] ?? null;
            $iso     = strtolower($entry['iso'] ?? '');
            $flag    = $entry['flag'] ?? '🌍';

            return [
                'name'       => $countryName,
                'iso'        => $iso,
                'flag'       => $flag,
                'flagSrc'    => $iso ? "https://flagcdn.com/w320/{$iso}.png" : null,
                'activities' => $activities,
                'tabKey'     => 'country-' . Str::slug($countryName),
                'pageParam'  => $pageParam,
            ];
        });

        return view('manager.activities.index', compact('uaeActivities', 'countryTabs'));
    }

    /**
     * Show the form for creating a new activity.
     *
     * `?scope=outside` (used by the "Activities outside the UAE" tab) defaults
     * the country to a non-UAE one so the Emirate dropdown stays hidden.
     */
    public function create(Request $request)
    {
        $emirates = Emirates::where('isActive', 1)->orderBy('emiratesName')->get();
        $countries = $this->allowedCountriesConfig();

        // Pre-select a non-UAE country if a specific one is requested.
        $defaultCountry = 'United Arab Emirates';
        if ($request->filled('country') && !$this->isUAE($request->query('country'))) {
            $defaultCountry = $request->query('country');
        } elseif ($request->query('scope') === 'outside') {
            $nonUAE = collect(array_keys($countries))->first(fn ($c) => !$this->isUAE($c));
            $defaultCountry = $nonUAE ?? 'United Arab Emirates';
        }

        return view('manager.activities.create', compact('emirates', 'countries', 'defaultCountry'));
    }

    /**
     * Build the restricted country list for create/edit forms.
     * Always includes UAE; adds any extra countries the super admin granted.
     * Falls back to the full config list only if no restriction is set.
     */
    private function allowedCountriesConfig(): array
    {
        $all     = config('countries');
        $allowed = collect(current_company()?->getSetting('allowed_countries', []) ?? []);

        if ($allowed->isEmpty()) {
            // No restriction configured — show full list.
            return $all;
        }

        // Always include UAE, then the explicitly allowed countries in order.
        $keys = collect(['United Arab Emirates'])
            ->merge($allowed->filter(fn ($c) => !$this->isUAE($c)))
            ->unique();

        return $keys->filter(fn ($k) => isset($all[$k]))
                    ->mapWithKeys(fn ($k) => [$k => $all[$k]])
                    ->toArray();
    }

    private function resolveActivityCountry(Request $request): string
    {
        $chosen = trim($request->input('country', ''));
        return $chosen ?: 'United Arab Emirates';
    }

    private function isUAE(string $country): bool
    {
        return strtolower(trim($country)) === 'united arab emirates';
    }

    /**
     * Store a newly created activity.
     */
    public function store(Request $request)
    {
        $country = $this->resolveActivityCountry($request);
        $request->validate([
            'activityName'               => 'required|string|max:255',
            'activityLocation'           => 'required|string|max:255',
            'country'                    => 'required|string|max:100',
            'emiratesID'                 => $this->isUAE($country)
                                            ? 'required|exists:tbl_emirates,emiratesID'
                                            : 'nullable|exists:tbl_emirates,emiratesID',
            'activityPrice'              => 'required|numeric|min:0',
            'activityCurrency'           => 'nullable|string|max:3',
            'activityCategory'           => 'nullable|string|max:100',
            'activityChildPrice'         => 'nullable|numeric|min:0',
            'activityTransactionCharges' => 'nullable|numeric|min:0',
            'supplierName'               => 'nullable|string|max:255',
            'supplierEmail'              => 'nullable|email|max:255',
            'activityImageFiles'         => 'required|array|min:1',
            'activityImageFiles.*'       => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'detailsOverview'            => 'required|string',
            'detailsIminfo'              => 'required|array|min:1',
            'detailsIminfo.*'            => 'required|string',
            'detailsHighlights'          => 'required|array|min:1',
            'detailsHighlights.*'        => 'required|string',
        ]);

        $companyId = current_company()?->id ?? 0;
        $relativeDir = "assets/activities/{$companyId}";
        $destinationPath = public_path($relativeDir);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Handle multiple image uploads
        $imagePaths = [];
        foreach ($request->file('activityImageFiles') as $imageFile) {
            $filename = time() . '_' . Str::random(10) . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move($destinationPath, $filename);
            $imagePaths[] = $relativeDir . '/' . $filename;
        }

        $firstImage = count($imagePaths) > 0 ? $imagePaths[0] : '';

        $activity = UAEActivity::create([
            'activityName'               => $request->activityName,
            'activityLocation'           => $request->activityLocation,
            'emiratesID'                 => $this->isUAE($country) ? $request->emiratesID : null,
            'country'                    => $country,
            'activityCurrency'           => strtoupper($request->activityCurrency ?: ($this->isUAE($country) ? 'AED' : 'USD')),
            'activityPrice'              => $request->activityPrice,
            'activityCategory'           => $request->activityCategory,
            'activityChildPrice'         => $request->activityChildPrice ?? 0,
            'activityTransactionCharges' => $request->activityTransactionCharges ?? 0,
            'supplierName'               => $request->supplierName,
            'supplierEmail'              => $request->supplierEmail,
            'activityImage'              => $firstImage,
            'isActive'                   => 1,
            'createdBy'                  => auth()->user()?->name ?? 'manager',
            'createdDate'                => now(),
            'modifiedBy'                 => null,
            'modifiedDate'               => null,
        ]);

        // Create activity details record
        UAEActivityDetail::create([
            'activityID'        => $activity->activityID,
            'detailsOverview'   => $request->detailsOverview,
            'detailsIminfo'     => implode('#cseparator', $request->detailsIminfo),
            'detailsHighlights' => implode('#cseparator', $request->detailsHighlights),
            'activityImage'     => implode('#cseparator', $imagePaths),
            'isActive'          => 1,
            'createdBy'         => auth()->user()?->name ?? 'manager',
            'createdDate'       => now(),
            'modifiedBy'        => null,
            'modifiedDate'      => null,
        ]);

        return redirect()->route('manager.activities.index')
                         ->with('success', 'Activity created successfully!');
    }

    /**
     * Show the form for editing the specified activity.
     */
    public function edit($id)
    {
        $activity = UAEActivity::where('activityID', $id)
                               ->where('isActive', 1)
                               ->firstOrFail();

        $details = UAEActivityDetail::where('activityID', $activity->activityID)->first();

        $emirates = Emirates::where('isActive', 1)
                           ->orderBy('emiratesName')
                           ->get();

        // Parse #cseparator fields for editing
        $detailsIminfo     = [];
        $detailsHighlights = [];
        $existingImages    = [];

        if ($details) {
            if ($details->detailsIminfo) {
                $detailsIminfo = explode('#cseparator', $details->detailsIminfo);
            }
            if ($details->detailsHighlights) {
                $detailsHighlights = explode('#cseparator', $details->detailsHighlights);
            }
            if ($details->activityImage) {
                $existingImages = explode('#cseparator', $details->activityImage);
            }
        }

        $countries = $this->allowedCountriesConfig();

        return view('manager.activities.edit', compact(
            'activity',
            'details',
            'emirates',
            'detailsIminfo',
            'detailsHighlights',
            'existingImages',
            'countries'
        ));
    }

    /**
     * Update the specified activity.
     */
    public function update(Request $request, $id)
    {
        $activity = UAEActivity::where('activityID', $id)
                               ->where('isActive', 1)
                               ->firstOrFail();

        $country = $this->resolveActivityCountry($request);
        $request->validate([
            'activityName'               => 'required|string|max:255',
            'activityLocation'           => 'required|string|max:255',
            'country'                    => 'required|string|max:100',
            'emiratesID'                 => $this->isUAE($country)
                                            ? 'required|exists:tbl_emirates,emiratesID'
                                            : 'nullable|exists:tbl_emirates,emiratesID',
            'activityPrice'              => 'required|numeric|min:0',
            'activityCurrency'           => 'nullable|string|max:3',
            'activityCategory'           => 'nullable|string|max:100',
            'activityChildPrice'         => 'nullable|numeric|min:0',
            'activityTransactionCharges' => 'nullable|numeric|min:0',
            'supplierName'               => 'nullable|string|max:255',
            'supplierEmail'              => 'nullable|email|max:255',
            'activityImageFiles'         => 'nullable|array',
            'activityImageFiles.*'       => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'detailsOverview'            => 'required|string',
            'detailsIminfo'              => 'required|array|min:1',
            'detailsIminfo.*'            => 'required|string',
            'detailsHighlights'          => 'required|array|min:1',
            'detailsHighlights.*'        => 'required|string',
            'replace_images'             => 'nullable|boolean',
        ]);

        // Get existing details
        $details = UAEActivityDetail::firstOrNew(['activityID' => $activity->activityID]);
        $existingImages = [];

        if ($details && $details->activityImage) {
            $existingImages = explode('#cseparator', $details->activityImage);
        }

        $finalImagePaths = $existingImages;

        // Handle new image uploads if provided
        if ($request->hasFile('activityImageFiles')) {
            $companyId = current_company()?->id ?? 0;
            $relativeDir = "assets/activities/{$companyId}";
            $destinationPath = public_path($relativeDir);
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $newImages = [];
            foreach ($request->file('activityImageFiles') as $imageFile) {
                $filename = time() . '_' . Str::random(10) . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move($destinationPath, $filename);
                $newImages[] = $relativeDir . '/' . $filename;
            }

            if ($request->has('replace_images') && $request->replace_images) {
                // Delete old files
                foreach ($existingImages as $oldImage) {
                    $oldPath = public_path($oldImage);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }
                $finalImagePaths = $newImages;
            } else {
                $finalImagePaths = array_merge($existingImages, $newImages);
            }
        }

        $activity->update([
            'activityName'               => $request->activityName,
            'activityLocation'           => $request->activityLocation,
            'emiratesID'                 => $this->isUAE($country) ? $request->emiratesID : null,
            'country'                    => $country,
            'activityCurrency'           => strtoupper($request->activityCurrency ?: ($this->isUAE($country) ? 'AED' : 'USD')),
            'activityPrice'              => $request->activityPrice,
            'activityCategory'           => $request->activityCategory,
            'activityChildPrice'         => $request->activityChildPrice ?? 0,
            'activityTransactionCharges' => $request->activityTransactionCharges ?? 0,
            'supplierName'               => $request->supplierName,
            'supplierEmail'              => $request->supplierEmail,
            'activityImage'              => count($finalImagePaths) > 0 ? $finalImagePaths[0] : '',
            'modifiedBy'                 => auth()->user()?->name ?? 'manager',
            'modifiedDate'               => now(),
        ]);

        // Update or create details
        $detailsData = [
            'detailsOverview'   => $request->detailsOverview,
            'detailsIminfo'     => implode('#cseparator', $request->detailsIminfo),
            'detailsHighlights' => implode('#cseparator', $request->detailsHighlights),
            'activityImage'     => implode('#cseparator', $finalImagePaths),
            'isActive'          => 1,
            'modifiedBy'        => auth()->user()?->name ?? 'manager',
            'modifiedDate'      => now(),
        ];

        if (!$details->exists) {
            $detailsData['createdBy']   = auth()->user()?->name ?? 'manager';
            $detailsData['createdDate'] = now();
        }

        foreach ($detailsData as $key => $value) {
            $details->$key = $value;
        }
        $details->save();

        return redirect()->route('manager.activities.index')
                         ->with('success', 'Activity updated successfully!');
    }

    /**
     * Soft-delete the specified activity.
     */
    public function destroy($id)
    {
        $activity = UAEActivity::where('activityID', $id)
                               ->where('isActive', 1)
                               ->firstOrFail();

        // Soft delete main activity
        $activity->update([
            'isActive'     => 0,
            'modifiedBy'   => auth()->user()?->name ?? 'manager',
            'modifiedDate' => now(),
        ]);

        // Soft delete activity details
        UAEActivityDetail::where('activityID', $activity->activityID)
                         ->update([
                             'isActive'     => 0,
                             'modifiedBy'   => auth()->user()?->name ?? 'manager',
                             'modifiedDate' => now(),
                         ]);

        return redirect()->route('manager.activities.index')
                         ->with('success', 'Activity deleted successfully!');
    }
}
