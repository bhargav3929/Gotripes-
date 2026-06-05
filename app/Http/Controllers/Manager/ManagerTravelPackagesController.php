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
        $validated = $request->validate($this->rules($request, true));

        $coverPath = $this->storeImage($request->file('image'));

        $package = TravelPackage::create([
            'title'            => $validated['title'],
            'country'          => $validated['country'],
            'package_type'     => $validated['package_type'],
            'partner_email'    => $validated['partner_email'] ?? null,
            'partner_whatsapp' => $validated['partner_whatsapp'] ?? null,
            'price'            => $validated['price'],
            'price_adult'      => $validated['price_adult'] ?? null,
            'price_child'      => $validated['price_child'] ?? null,
            'price_infant'     => $validated['price_infant'] ?? null,
            'description'      => $validated['description'],
            'duration'         => $validated['duration'],
            'image'            => $coverPath,
            'isActive'         => 1,
            'createdBy'        => auth()->user()?->name ?? 'manager',
            'createdDate'      => now(),
        ]);

        $this->storeGallery($request, $package);

        return redirect()->route('manager.packages.index')
            ->with('success', 'Tour package created successfully.');
    }

    /**
     * Shared validation rules. The cover image is required only on create.
     */
    private function rules(Request $request, bool $creating): array
    {
        $countryRule = 'required|string|max:100';
        $allowed = $this->allowedCountries();
        if ($allowed) {
            $countryRule .= '|in:' . implode(',', $allowed);
        }

        return [
            'title'            => 'required|string|max:255',
            'country'          => $countryRule,
            'package_type'     => 'required|in:enquire,purchase',
            'partner_email'    => 'nullable|email|max:255',
            'partner_whatsapp' => 'nullable|string|max:30',
            'price'            => 'required|numeric|min:0',
            'price_adult'      => 'nullable|numeric|min:0',
            'price_child'      => 'nullable|numeric|min:0',
            'price_infant'     => 'nullable|numeric|min:0',
            'description'      => 'required|string',
            'duration'         => 'required|string|max:255',
            'image'            => ($creating ? 'required' : 'nullable') . '|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'gallery'          => 'nullable|array|max:7',
            'gallery.*'        => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    /**
     * Store any additional gallery images uploaded with the request.
     */
    private function storeGallery(Request $request, TravelPackage $package): void
    {
        if (!$request->hasFile('gallery')) {
            return;
        }

        $order = (int) ($package->images()->max('sort_order') ?? 0);
        foreach ($request->file('gallery') as $file) {
            $package->images()->create([
                'image_path' => $this->storeImage($file),
                'sort_order' => ++$order,
            ]);
        }
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

        $validated = $request->validate($this->rules($request, false));

        $imagePath = $package->image;
        if ($request->hasFile('image')) {
            if ($package->image && File::exists(public_path($package->image))) {
                File::delete(public_path($package->image));
            }
            $imagePath = $this->storeImage($request->file('image'));
        }

        $package->update([
            'title'            => $validated['title'],
            'country'          => $validated['country'],
            'package_type'     => $validated['package_type'],
            'partner_email'    => $validated['partner_email'] ?? null,
            'partner_whatsapp' => $validated['partner_whatsapp'] ?? null,
            'price'            => $validated['price'],
            'price_adult'      => $validated['price_adult'] ?? null,
            'price_child'      => $validated['price_child'] ?? null,
            'price_infant'     => $validated['price_infant'] ?? null,
            'description'      => $validated['description'],
            'duration'         => $validated['duration'],
            'image'            => $imagePath,
            'modifiedBy'       => auth()->user()?->name ?? 'manager',
            'modifiedDate'     => now(),
        ]);

        // Remove any gallery images the manager unchecked.
        foreach ((array) $request->input('remove_images', []) as $imageId) {
            $img = $package->images()->find($imageId);
            if ($img) {
                if ($img->image_path && File::exists(public_path($img->image_path))) {
                    File::delete(public_path($img->image_path));
                }
                $img->delete();
            }
        }

        $this->storeGallery($request, $package);

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
