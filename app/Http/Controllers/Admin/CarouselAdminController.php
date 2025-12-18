<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageAd;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CarouselAdminController extends Controller
{
    public function index()
    {
        $carouselImages = HomepageAd::where('isActive', 1)
                                   ->orderBy('createddate', 'desc')
                                   ->paginate(10);
        return view('admin.homepageads.index', compact('carouselImages'));
    }

    public function create()
    {
        return view('admin.homepageads.create');
    }

    public function store(Request $request)
    {
        // Validate with strict dimension requirements
        $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120', // Max 5MB
                Rule::dimensions()
                    ->minWidth(480)
                    ->maxWidth(482)
                    ->minHeight(160)
                    ->maxHeight(165),
            ],
        ], [
            'image.dimensions' => 'Image dimensions must be between 480x160 and 482x165 pixels.',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('assets/homepageads');

            // Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Move file to public/assets/homepageads/
            $file->move($destinationPath, $filename);
            $imagePath = 'assets/homepageads/' . $filename;

            HomepageAd::create([
                'imgPath' => $imagePath,
                'isActive' => 1,
                'createdby' => auth()->user()->name,
                'createddate' => now(),
                'modifiedby' => null,
                'modifieddate' => null
            ]);

            return redirect()->route('admin.homepageads.index')
                           ->with('success', 'Carousel image uploaded successfully!');
        }

        return redirect()->back()->withErrors('Please select an image.');
    }

    public function edit(HomepageAd $homepagead)
    {
        return view('admin.homepageads.edit', compact('homepagead'));
    }

    public function update(Request $request, HomepageAd $homepagead)
    {
        // Validate with strict dimension requirements (nullable for edit)
        $request->validate([
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120', // Max 5MB
                Rule::dimensions()
                    ->minWidth(480)
                    ->maxWidth(482)
                    ->minHeight(160)
                    ->maxHeight(165),
            ],
        ], [
            'image.dimensions' => 'Image dimensions must be between 480x160 and 482x165 pixels.',
        ]);

        $imagePath = $homepagead->imgPath;
        
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($homepagead->imgPath && file_exists(public_path($homepagead->imgPath))) {
                unlink(public_path($homepagead->imgPath));
            }
            
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('assets/homepageads');

            // Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Move new file
            $file->move($destinationPath, $filename);
            $imagePath = 'assets/homepageads/' . $filename;
        }

        $homepagead->update([
            'imgPath' => $imagePath,
            'modifiedby' => auth()->user()->name,
            'modifieddate' => now()
        ]);

        return redirect()->route('admin.homepageads.index')
                       ->with('success', 'Carousel image updated successfully!');
    }

    public function destroy(HomepageAd $homepagead)
    {
        // Soft delete - set isActive to 0
        $homepagead->update([
            'isActive' => 0,
            'modifiedby' => auth()->user()->name,
            'modifieddate' => now()
        ]);

        return redirect()->route('admin.homepageads.index')
                       ->with('success', 'Carousel image deleted successfully!');
    }
}
