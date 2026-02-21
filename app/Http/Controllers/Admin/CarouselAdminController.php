<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageAd;
use Illuminate\Http\Request;

class CarouselAdminController extends Controller
{
    public function index()
    {
        // Group all active media by TV slot
        $adSlots = HomepageAd::where('isActive', 1)
            ->orderBy('slotOrder', 'asc')
            ->orderBy('displayOrder', 'asc')
            ->get()
            ->groupBy('slotOrder');

        $totalMedia = HomepageAd::where('isActive', 1)->count();
        $usedSlots = $adSlots->keys()->count();

        return view('admin.homepageads.index', compact('adSlots', 'totalMedia', 'usedSlots'));
    }

    public function create()
    {
        $usedSlots = HomepageAd::where('isActive', 1)
            ->select('slotOrder')
            ->distinct()
            ->pluck('slotOrder')
            ->toArray();

        return view('admin.homepageads.create', compact('usedSlots'));
    }

    public function store(Request $request)
    {
        $mediaType = $request->input('mediaType', 'image');

        if ($mediaType === 'video') {
            $request->validate([
                'media' => 'required|file|mimes:mp4|max:51200',
                'slotOrder' => 'required|integer|min:1|max:5',
                'duration' => 'nullable|integer|min:1|max:60',
            ]);
        } else {
            $request->validate([
                'media' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'slotOrder' => 'required|integer|min:1|max:5',
                'duration' => 'nullable|integer|min:1|max:60',
            ]);
        }

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('assets/homepageads');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $mediaPath = 'assets/homepageads/' . $filename;

            // Auto-increment displayOrder within the slot
            $maxOrder = HomepageAd::where('isActive', 1)
                ->where('slotOrder', $request->input('slotOrder'))
                ->max('displayOrder') ?? 0;

            HomepageAd::create([
                'imgPath' => $mediaPath,
                'mediaType' => $mediaType,
                'slotOrder' => $request->input('slotOrder'),
                'displayOrder' => $maxOrder + 1,
                'duration' => $request->input('duration', 5),
                'isActive' => 1,
                'createdby' => auth()->user()->name ?? 'admin',
                'createddate' => now(),
                'modifiedby' => null,
                'modifieddate' => null
            ]);

            return redirect()->route('admin.homepageads.index')
                           ->with('success', 'Media added to TV ' . $request->input('slotOrder') . ' successfully!');
        }

        return redirect()->back()->withErrors('Please select a file.');
    }

    public function edit(HomepageAd $homepagead)
    {
        return view('admin.homepageads.edit', compact('homepagead'));
    }

    public function update(Request $request, HomepageAd $homepagead)
    {
        $mediaType = $request->input('mediaType', $homepagead->mediaType ?? 'image');

        if ($request->hasFile('media')) {
            if ($mediaType === 'video') {
                $request->validate([
                    'media' => 'required|file|mimes:mp4|max:51200',
                    'slotOrder' => 'required|integer|min:1|max:5',
                    'duration' => 'nullable|integer|min:1|max:60',
                ]);
            } else {
                $request->validate([
                    'media' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                    'slotOrder' => 'required|integer|min:1|max:5',
                    'duration' => 'nullable|integer|min:1|max:60',
                ]);
            }
        } else {
            $request->validate([
                'slotOrder' => 'required|integer|min:1|max:5',
                'duration' => 'nullable|integer|min:1|max:60',
            ]);
        }

        $mediaPath = $homepagead->imgPath;

        if ($request->hasFile('media')) {
            if ($homepagead->imgPath && file_exists(public_path($homepagead->imgPath))) {
                unlink(public_path($homepagead->imgPath));
            }

            $file = $request->file('media');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('assets/homepageads');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $mediaPath = 'assets/homepageads/' . $filename;
        }

        $homepagead->update([
            'imgPath' => $mediaPath,
            'mediaType' => $mediaType,
            'slotOrder' => $request->input('slotOrder', $homepagead->slotOrder),
            'duration' => $request->input('duration', $homepagead->duration ?? 5),
            'modifiedby' => auth()->user()->name ?? 'admin',
            'modifieddate' => now()
        ]);

        return redirect()->route('admin.homepageads.index')
                       ->with('success', 'Media updated successfully!');
    }

    public function destroy(HomepageAd $homepagead)
    {
        $homepagead->update([
            'isActive' => 0,
            'modifiedby' => auth()->user()->name ?? 'admin',
            'modifieddate' => now()
        ]);

        return redirect()->route('admin.homepageads.index')
                       ->with('success', 'Media removed successfully!');
    }
}
