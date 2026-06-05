<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TravelPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class TravelPackageController extends Controller
{
    public function index()
    {
        $packages = TravelPackage::where('isActive', 1)
            ->orderBy('createdDate', 'desc')
            ->paginate(10);

        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(true));

        $imagePath = $this->storeImage($request->file('image'));

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
            'image'            => $imagePath,
            'isActive'         => 1,
            'createdBy'        => auth()->user()->name,
            'createdDate'      => now(),
        ]);

        $this->storeGallery($request, $package);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Travel Package created successfully!');
    }

    public function edit($id)
    {
        $package = TravelPackage::findOrFail($id);
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, $id)
    {
        $package = TravelPackage::findOrFail($id);

        $validated = $request->validate($this->rules(false));

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
            'modifiedBy'       => auth()->user()->name,
            'modifiedDate'     => now(),
        ]);

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

        return redirect()->route('admin.packages.index')
            ->with('success', 'Travel Package updated successfully!');
    }

    public function destroy($id)
    {
        $package = TravelPackage::findOrFail($id);

        $package->update([
            'isActive'     => 0,
            'modifiedBy'   => auth()->user()->name,
            'modifiedDate' => now(),
        ]);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Travel Package deleted successfully!');
    }

    /**
     * Shared validation rules. Cover image required only on create.
     */
    private function rules(bool $creating): array
    {
        return [
            'title'            => 'required|string|max:255',
            'country'          => 'required|string|max:100',
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

    private function storeImage($file): string
    {
        $destination = public_path('assets/packages');
        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true, true);
        }
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);
        return 'assets/packages/' . $filename;
    }
}
