<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\UmrahPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ManagerUmrahPackagesController extends Controller
{
    public function index(Request $request)
    {
        $query = UmrahPackage::orderBy('sortOrder', 'asc')
                             ->orderBy('createdDate', 'desc');

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        // Sub-category (tier) filter
        if ($request->filled('sub_category')) {
            $query->where('sub_category', $request->sub_category);
        }
        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'inactive') {
                $query->where('isActive', 0);
            } elseif ($request->status === 'active') {
                $query->where('isActive', 1);
            }
        } else {
            // Default: show all (active + inactive)
        }

        $packages = $query->paginate(20)->withQueryString();

        return view('manager.umrah-packages.index', compact('packages'));
    }

    public function create()
    {
        return view('manager.umrah-packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'required|string|in:bus,air',
            'sub_category'     => 'required|string|in:economy,standard,premium,vip',
            'price'            => 'required|numeric|min:0',
            'discount_price'   => 'nullable|numeric|min:0',
            'adult_price'      => 'nullable|numeric|min:0',
            'child_price'      => 'nullable|numeric|min:0',
            'infant_price'     => 'nullable|numeric|min:0',
            'currency'         => 'required|string|max:10',
            'description'      => 'required|string',
            'duration'         => 'required|string|max:255',
            'transport'        => 'nullable|string',
            'hotels'           => 'nullable|string',
            'inclusions'       => 'nullable|string',
            'exclusions'       => 'nullable|string',
            'itinerary'        => 'nullable|string',
            'tag'              => 'nullable|string|max:50',
            'features'         => 'nullable|string',
            'image'            => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'gallery_images'   => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'isFeatured'       => 'nullable|boolean',
            'sortOrder'        => 'nullable|integer|min:0',
        ]);

        $imagePath = $this->storeImage($request->file('image'));

        $galleryPaths = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $galleryPaths[] = $this->storeImage($file);
            }
        }

        UmrahPackage::create([
            'title'          => $validated['title'],
            'category'       => $validated['category'],
            'sub_category'   => $validated['sub_category'],
            'price'          => $validated['price'],
            'discount_price' => $validated['discount_price'] ?? null,
            'adult_price'    => $validated['adult_price']    ?? null,
            'child_price'    => $validated['child_price']    ?? null,
            'infant_price'   => $validated['infant_price']   ?? null,
            'currency'       => $validated['currency'],
            'description'    => $validated['description'],
            'duration'       => $validated['duration'],
            'transport'      => $validated['transport'],
            'hotels'         => $validated['hotels'],
            'inclusions'     => $this->parseLines($request->inclusions),
            'exclusions'     => $this->parseLines($request->exclusions),
            'itinerary'      => $this->parseLines($request->itinerary),
            'gallery_images' => $galleryPaths,
            'tag'            => $validated['tag'] ?? null,
            'features'       => $this->parseLines($request->features),
            'image'          => $imagePath,
            'isFeatured'     => $request->boolean('isFeatured'),
            'sortOrder'      => $validated['sortOrder'] ?? 0,
            'isActive'       => 1,
            'status'         => 'active',
            'createdBy'      => auth()->user()?->name ?? 'manager',
            'createdDate'    => now(),
        ]);

        return redirect()->route('manager.umrah-packages.index')
            ->with('success', 'Package created successfully.');
    }

    public function edit($id)
    {
        $package = UmrahPackage::findOrFail($id);
        return view('manager.umrah-packages.edit', compact('package'));
    }

    public function update(Request $request, $id)
    {
        $package = UmrahPackage::findOrFail($id);

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'required|string|in:bus,air',
            'sub_category'     => 'required|string|in:economy,standard,premium,vip',
            'price'            => 'required|numeric|min:0',
            'discount_price'   => 'nullable|numeric|min:0',
            'adult_price'      => 'nullable|numeric|min:0',
            'child_price'      => 'nullable|numeric|min:0',
            'infant_price'     => 'nullable|numeric|min:0',
            'currency'         => 'required|string|max:10',
            'description'      => 'required|string',
            'duration'         => 'required|string|max:255',
            'transport'        => 'nullable|string',
            'hotels'           => 'nullable|string',
            'inclusions'       => 'nullable|string',
            'exclusions'       => 'nullable|string',
            'itinerary'        => 'nullable|string',
            'tag'              => 'nullable|string|max:50',
            'features'         => 'nullable|string',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'gallery_images'   => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'isFeatured'       => 'nullable|boolean',
            'sortOrder'        => 'nullable|integer|min:0',
        ]);

        $imagePath = $package->image;
        if ($request->hasFile('image')) {
            if ($package->image && File::exists(public_path($package->image))) {
                File::delete(public_path($package->image));
            }
            $imagePath = $this->storeImage($request->file('image'));
        }

        $galleryPaths = $package->gallery_images ?? [];
        if ($request->hasFile('gallery_images')) {
            foreach ($package->gallery_images ?? [] as $old) {
                if (File::exists(public_path($old))) File::delete(public_path($old));
            }
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $file) {
                $galleryPaths[] = $this->storeImage($file);
            }
        }

        $package->update([
            'title'          => $validated['title'],
            'category'       => $validated['category'],
            'sub_category'   => $validated['sub_category'],
            'price'          => $validated['price'],
            'discount_price' => $validated['discount_price'] ?? null,
            'adult_price'    => $validated['adult_price']    ?? null,
            'child_price'    => $validated['child_price']    ?? null,
            'infant_price'   => $validated['infant_price']   ?? null,
            'currency'       => $validated['currency'],
            'description'    => $validated['description'],
            'duration'       => $validated['duration'],
            'transport'      => $validated['transport'],
            'hotels'         => $validated['hotels'],
            'inclusions'     => $this->parseLines($request->inclusions),
            'exclusions'     => $this->parseLines($request->exclusions),
            'itinerary'      => $this->parseLines($request->itinerary),
            'gallery_images' => $galleryPaths,
            'tag'            => $validated['tag'] ?? null,
            'features'       => $this->parseLines($request->features),
            'image'          => $imagePath,
            'isFeatured'     => $request->boolean('isFeatured'),
            'sortOrder'      => $validated['sortOrder'] ?? 0,
            'modifiedBy'     => auth()->user()?->name ?? 'manager',
            'modifiedDate'   => now(),
        ]);

        return redirect()->route('manager.umrah-packages.index')
            ->with('success', 'Package updated successfully.');
    }

    public function destroy($id)
    {
        $package = UmrahPackage::findOrFail($id);
        $package->update([
            'isActive'     => 0,
            'status'       => 'archived',
            'modifiedBy'   => auth()->user()?->name ?? 'manager',
            'modifiedDate' => now(),
        ]);
        return redirect()->route('manager.umrah-packages.index')
            ->with('success', 'Package archived.');
    }

    /** AJAX Toggle active/inactive */
    public function toggle($id)
    {
        $package = UmrahPackage::findOrFail($id);
        $newActive = !$package->isActive;
        $package->update([
            'isActive'     => $newActive,
            'status'       => $newActive ? 'active' : 'disabled',
            'modifiedBy'   => auth()->user()?->name ?? 'manager',
            'modifiedDate' => now(),
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success'  => true,
                'isActive' => $newActive,
                'label'    => $newActive ? 'Active' : 'Disabled',
            ]);
        }

        return back()->with('success', 'Package status updated.');
    }

    // ── helpers ──────────────────────────────────────────────────────
    private function storeImage($file): string
    {
        $companyId   = current_company()?->id ?? 0;
        $relativeDir = "assets/umrah-packages/{$companyId}";
        $destination = public_path($relativeDir);
        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true, true);
        }
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);
        return $relativeDir . '/' . $filename;
    }

    private function parseLines(?string $raw): array
    {
        if (empty($raw)) return [];
        return array_values(array_filter(
            array_map('trim', explode("\n", $raw)),
            fn($l) => $l !== ''
        ));
    }
}
