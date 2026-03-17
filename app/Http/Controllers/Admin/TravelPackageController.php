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
        $request->validate([
            'title'       => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'description' => 'required|string',
            'duration'    => 'required|string|max:255',
            'image'       => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $imagePath = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('assets/packages');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $file->move($destinationPath, $filename);
            $imagePath = 'assets/packages/' . $filename;
        }

        TravelPackage::create([
            'title'        => $request->title,
            'price'        => $request->price,
            'description'  => $request->description,
            'duration'     => $request->duration,
            'image'        => $imagePath,
            'isActive'     => 1,
            'createdBy'    => auth()->user()->name,
            'createdDate'  => now(),
        ]);

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

        $request->validate([
            'title'       => 'required|string|max:255',
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

            $file = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('assets/packages');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $file->move($destinationPath, $filename);
            $imagePath = 'assets/packages/' . $filename;
        }

        $package->update([
            'title'        => $request->title,
            'price'        => $request->price,
            'description'  => $request->description,
            'duration'     => $request->duration,
            'image'        => $imagePath,
            'modifiedBy'   => auth()->user()->name,
            'modifiedDate' => now(),
        ]);

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
}
