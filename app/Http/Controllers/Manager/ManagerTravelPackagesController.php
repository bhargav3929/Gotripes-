<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\TravelPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ManagerTravelPackagesController extends Controller
{
    /**
     * Get the countries this tenant is allowed to use for tour packages.
     * Returns null when unrestricted (backward-compatible).
     */
    private function allowedCountries(): ?array
    {
        $company = current_company();
        if (!$company) {
            return null;
        }

        $allowed = $company->getSetting('allowed_countries', []);
        return is_array($allowed) && count($allowed) > 0 ? $allowed : null;
    }

    public function index(Request $request)
    {
        $query = TravelPackage::where('isActive', 1);

        // Filter by country if requested
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        $packages = $query->orderBy('country')
            ->orderBy('createdDate', 'desc')
            ->paginate(15)
            ->withQueryString();

        $allowedCountries = $this->allowedCountries();

        // Get distinct countries already used, for the filter dropdown
        $usedCountries = TravelPackage::where('isActive', 1)
            ->whereNotNull('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        return view('manager.packages.index', compact('packages', 'allowedCountries', 'usedCountries'));
    }

    public function create()
    {
        $allowedCountries = $this->allowedCountries();
        return view('manager.packages.create', compact('allowedCountries'));
    }

    public function store(Request $request)
    {
        $countryRule = 'required|string|max:100';
        $allowed = $this->allowedCountries();
        if ($allowed) {
            $countryRule .= '|in:' . implode(',', $allowed);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'country'     => $countryRule,
            'price'       => 'required|numeric|min:0',
            'description' => 'required|string',
            'duration'    => 'required|string|max:255',
            'image'       => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $imagePath = $this->storeImage($request->file('image'));

        TravelPackage::create([
            'title'        => $validated['title'],
            'country'      => $validated['country'],
            'price'        => $validated['price'],
            'description'  => $validated['description'],
            'duration'     => $validated['duration'],
            'image'        => $imagePath,
            'isActive'     => 1,
            'createdBy'    => auth()->user()?->name ?? 'manager',
            'createdDate'  => now(),
        ]);

        return redirect()->route('manager.packages.index')
            ->with('success', 'Tour package created successfully.');
    }

    public function edit($id)
    {
        $package = TravelPackage::where('id', $id)->where('isActive', 1)->firstOrFail();
        $allowedCountries = $this->allowedCountries();
        return view('manager.packages.edit', compact('package', 'allowedCountries'));
    }

    public function update(Request $request, $id)
    {
        $package = TravelPackage::where('id', $id)->where('isActive', 1)->firstOrFail();

        $countryRule = 'required|string|max:100';
        $allowed = $this->allowedCountries();
        if ($allowed) {
            $countryRule .= '|in:' . implode(',', $allowed);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'country'     => $countryRule,
            'price'       => 'required|numeric|min:0',
            'description' => 'required|string',
            'duration'    => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $imagePath = $package->image;
        if ($request->hasFile('image')) {
            if ($package->image && File::exists(public_path($package->image))) {
                File::delete(public_path($package->image));
            }
            $imagePath = $this->storeImage($request->file('image'));
        }

        $package->update([
            'title'        => $validated['title'],
            'country'      => $validated['country'],
            'price'        => $validated['price'],
            'description'  => $validated['description'],
            'duration'     => $validated['duration'],
            'image'        => $imagePath,
            'modifiedBy'   => auth()->user()?->name ?? 'manager',
            'modifiedDate' => now(),
        ]);

        return redirect()->route('manager.packages.index')
            ->with('success', 'Tour package updated successfully.');
    }

    public function destroy($id)
    {
        $package = TravelPackage::where('id', $id)->where('isActive', 1)->firstOrFail();

        $package->update([
            'isActive'     => 0,
            'modifiedBy'   => auth()->user()?->name ?? 'manager',
            'modifiedDate' => now(),
        ]);

        return redirect()->route('manager.packages.index')
            ->with('success', 'Tour package removed.');
    }

    /**
     * Store an uploaded image under a per-tenant subdirectory so two tenants
     * can never collide on filenames. Returns the asset-relative path.
     */
    private function storeImage($file): string
    {
        $companyId = current_company()?->id ?? 0;
        $relativeDir = "assets/packages/{$companyId}";
        $destination = public_path($relativeDir);

        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true, true);
        }

        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);

        return $relativeDir . '/' . $filename;
    }
}
