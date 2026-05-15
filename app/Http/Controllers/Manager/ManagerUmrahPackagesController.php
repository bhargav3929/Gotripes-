<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\UmrahPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ManagerUmrahPackagesController extends Controller
{
    public function index()
    {
        $packages = UmrahPackage::where('isActive', 1)
            ->orderBy('sortOrder', 'asc')
            ->orderBy('createdDate', 'desc')
            ->paginate(15);

        return view('manager.umrah-packages.index', compact('packages'));
    }

    public function create()
    {
        return view('manager.umrah-packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'currency'    => 'required|string|max:10',
            'description' => 'required|string',
            'duration'    => 'required|string|max:255',
            'tag'         => 'nullable|string|max:50',
            'features'    => 'nullable|string',
            'image'       => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'isFeatured'  => 'nullable|boolean',
            'sortOrder'   => 'nullable|integer|min:0',
        ]);

        $imagePath = $this->storeImage($request->file('image'));

        UmrahPackage::create([
            'title'        => $validated['title'],
            'price'        => $validated['price'],
            'currency'     => $validated['currency'],
            'description'  => $validated['description'],
            'duration'     => $validated['duration'],
            'tag'          => $validated['tag'] ?? null,
            'features'     => $this->parseFeatures($request->features),
            'image'        => $imagePath,
            'isFeatured'   => $request->boolean('isFeatured'),
            'sortOrder'    => $validated['sortOrder'] ?? 0,
            'isActive'     => 1,
            'createdBy'    => auth()->user()?->name ?? 'manager',
            'createdDate'  => now(),
        ]);

        return redirect()->route('manager.umrah-packages.index')
            ->with('success', 'Package created successfully.');
    }

    public function edit($id)
    {
        $package = UmrahPackage::where('id', $id)->where('isActive', 1)->firstOrFail();
        return view('manager.umrah-packages.edit', compact('package'));
    }

    public function update(Request $request, $id)
    {
        $package = UmrahPackage::where('id', $id)->where('isActive', 1)->firstOrFail();

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'currency'    => 'required|string|max:10',
            'description' => 'required|string',
            'duration'    => 'required|string|max:255',
            'tag'         => 'nullable|string|max:50',
            'features'    => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'isFeatured'  => 'nullable|boolean',
            'sortOrder'   => 'nullable|integer|min:0',
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
            'price'        => $validated['price'],
            'currency'     => $validated['currency'],
            'description'  => $validated['description'],
            'duration'     => $validated['duration'],
            'tag'          => $validated['tag'] ?? null,
            'features'     => $this->parseFeatures($request->features),
            'image'        => $imagePath,
            'isFeatured'   => $request->boolean('isFeatured'),
            'sortOrder'    => $validated['sortOrder'] ?? 0,
            'modifiedBy'   => auth()->user()?->name ?? 'manager',
            'modifiedDate' => now(),
        ]);

        return redirect()->route('manager.umrah-packages.index')
            ->with('success', 'Package updated successfully.');
    }

    public function destroy($id)
    {
        $package = UmrahPackage::where('id', $id)->where('isActive', 1)->firstOrFail();

        $package->update([
            'isActive'     => 0,
            'modifiedBy'   => auth()->user()?->name ?? 'manager',
            'modifiedDate' => now(),
        ]);

        return redirect()->route('manager.umrah-packages.index')
            ->with('success', 'Package removed.');
    }

    private function storeImage($file): string
    {
        $companyId = current_company()?->id ?? 0;
        $relativeDir = "assets/umrah-packages/{$companyId}";
        $destination = public_path($relativeDir);

        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true, true);
        }

        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);

        return $relativeDir . '/' . $filename;
    }

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
