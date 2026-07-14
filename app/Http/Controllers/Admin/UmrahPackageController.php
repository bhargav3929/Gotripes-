<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UmrahPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class UmrahPackageController extends Controller
{
    public function index()
    {
        $packages = UmrahPackage::where('isActive', 1)
            ->orderBy('sortOrder', 'asc')
            ->orderBy('createdDate', 'desc')
            ->paginate(10);

        return view('admin.umrah-packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.umrah-packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'category'        => 'required|string|in:economy,standard,premium,vip',
            'price'           => 'required|numeric|min:0',
            'currency'        => 'required|string|max:10',
            'description'     => 'required|string',
            'duration'        => 'required|string|max:255',
            'transport'       => 'nullable|string',
            'hotels'          => 'nullable|string',
            'inclusions'      => 'nullable|string',
            'exclusions'      => 'nullable|string',
            'itinerary'       => 'nullable|string',
            'tag'             => 'nullable|string|max:50',
            'features'        => 'nullable|string',
            'image'           => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'gallery_images'  => 'nullable|array',
            'gallery_images.*'=> 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'isFeatured'      => 'nullable|boolean',
            'sortOrder'       => 'nullable|integer|min:0',
        ]);

        $imagePath = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('assets/umrah-packages');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $file->move($destinationPath, $filename);
            $imagePath = 'assets/umrah-packages/' . $filename;
        }

        $galleryPaths = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('assets/umrah-packages');

                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }

                $file->move($destinationPath, $filename);
                $galleryPaths[] = 'assets/umrah-packages/' . $filename;
            }
        }

        // Parse features, inclusions, exclusions, and itinerary from textarea (one per line)
        $features = $this->parseFeatures($request->features);
        $inclusions = $this->parseFeatures($request->inclusions);
        $exclusions = $this->parseFeatures($request->exclusions);
        $itinerary = $this->parseFeatures($request->itinerary);

        UmrahPackage::create([
            'title'          => $request->title,
            'category'       => $request->category,
            'price'          => $request->price,
            'currency'       => $request->currency,
            'description'    => $request->description,
            'duration'       => $request->duration,
            'transport'      => $request->transport,
            'hotels'         => $request->hotels,
            'inclusions'     => $inclusions,
            'exclusions'     => $exclusions,
            'itinerary'      => $itinerary,
            'gallery_images' => $galleryPaths,
            'tag'            => $request->tag,
            'features'       => $features,
            'image'          => $imagePath,
            'isFeatured'     => $request->boolean('isFeatured'),
            'sortOrder'      => $request->sortOrder ?? 0,
            'isActive'       => 1,
            'createdBy'      => auth()->user()->name,
            'createdDate'    => now(),
        ]);

        return redirect()->route('admin.umrah-packages.index')
            ->with('success', 'Umrah Package created successfully!');
    }

    public function edit($id)
    {
        $package = UmrahPackage::findOrFail($id);
        return view('admin.umrah-packages.edit', compact('package'));
    }

    public function update(Request $request, $id)
    {
        $package = UmrahPackage::findOrFail($id);

        $request->validate([
            'title'           => 'required|string|max:255',
            'category'        => 'required|string|in:economy,standard,premium,vip',
            'price'           => 'required|numeric|min:0',
            'currency'        => 'required|string|max:10',
            'description'     => 'required|string',
            'duration'        => 'required|string|max:255',
            'transport'       => 'nullable|string',
            'hotels'          => 'nullable|string',
            'inclusions'      => 'nullable|string',
            'exclusions'      => 'nullable|string',
            'itinerary'       => 'nullable|string',
            'tag'             => 'nullable|string|max:50',
            'features'        => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'gallery_images'  => 'nullable|array',
            'gallery_images.*'=> 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'isFeatured'      => 'nullable|boolean',
            'sortOrder'       => 'nullable|integer|min:0',
        ]);

        $imagePath = $package->image;

        if ($request->hasFile('image')) {
            if ($package->image && File::exists(public_path($package->image))) {
                File::delete(public_path($package->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('assets/umrah-packages');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $file->move($destinationPath, $filename);
            $imagePath = 'assets/umrah-packages/' . $filename;
        }

        $galleryPaths = $package->gallery_images ?? [];
        if ($request->hasFile('gallery_images')) {
            if (!empty($package->gallery_images)) {
                foreach ($package->gallery_images as $oldImg) {
                    if (File::exists(public_path($oldImg))) {
                        File::delete(public_path($oldImg));
                    }
                }
            }
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $file) {
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('assets/umrah-packages');

                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }

                $file->move($destinationPath, $filename);
                $galleryPaths[] = 'assets/umrah-packages/' . $filename;
            }
        }

        $features = $this->parseFeatures($request->features);
        $inclusions = $this->parseFeatures($request->inclusions);
        $exclusions = $this->parseFeatures($request->exclusions);
        $itinerary = $this->parseFeatures($request->itinerary);

        $package->update([
            'title'          => $request->title,
            'category'       => $request->category,
            'price'          => $request->price,
            'currency'       => $request->currency,
            'description'    => $request->description,
            'duration'       => $request->duration,
            'transport'      => $request->transport,
            'hotels'         => $request->hotels,
            'inclusions'     => $inclusions,
            'exclusions'     => $exclusions,
            'itinerary'      => $itinerary,
            'gallery_images' => $galleryPaths,
            'tag'            => $request->tag,
            'features'       => $features,
            'image'          => $imagePath,
            'isFeatured'     => $request->boolean('isFeatured'),
            'sortOrder'      => $request->sortOrder ?? 0,
            'modifiedBy'     => auth()->user()->name,
            'modifiedDate'   => now(),
        ]);

        return redirect()->route('admin.umrah-packages.index')
            ->with('success', 'Umrah Package updated successfully!');
    }

    public function destroy($id)
    {
        $package = UmrahPackage::findOrFail($id);

        $package->update([
            'isActive'     => 0,
            'modifiedBy'   => auth()->user()->name,
            'modifiedDate' => now(),
        ]);

        return redirect()->route('admin.umrah-packages.index')
            ->with('success', 'Umrah Package deleted successfully!');
    }

    /**
     * Parse features from a newline-separated string into an array.
     */
    private function parseFeatures(?string $raw): array
    {
        if (empty($raw)) {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode("\n", $raw)),
            fn($line) => $line !== ''
        ));
    }
}
