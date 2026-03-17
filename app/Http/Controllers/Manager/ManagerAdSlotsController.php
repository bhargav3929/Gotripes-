<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\HomepageAd;
use Illuminate\Http\Request;

class ManagerAdSlotsController extends Controller
{
    public function index()
    {
        $adSlots = HomepageAd::where('isActive', 1)
            ->orderBy('slotOrder', 'asc')
            ->orderBy('displayOrder', 'asc')
            ->get()
            ->groupBy('slotOrder');

        $totalMedia = HomepageAd::where('isActive', 1)->count();
        $usedSlots = $adSlots->keys()->count();

        return view('manager.adslots.index', compact('adSlots', 'totalMedia', 'usedSlots'));
    }

    public function create()
    {
        $usedSlots = HomepageAd::where('isActive', 1)
            ->select('slotOrder')
            ->distinct()
            ->pluck('slotOrder')
            ->toArray();

        return view('manager.adslots.create', compact('usedSlots'));
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
                'createdby' => session('manager_name', 'manager'),
                'createddate' => now(),
                'modifiedby' => null,
                'modifieddate' => null,
            ]);

            return redirect()->route('manager.adslots.index')
                           ->with('success', 'Media added to TV ' . $request->input('slotOrder') . ' successfully!');
        }

        return redirect()->back()->withErrors('Please select a file.');
    }

    public function edit(HomepageAd $adslot)
    {
        return view('manager.adslots.edit', ['homepagead' => $adslot]);
    }

    public function update(Request $request, HomepageAd $adslot)
    {
        $mediaType = $request->input('mediaType', $adslot->mediaType ?? 'image');

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

        $mediaPath = $adslot->imgPath;

        if ($request->hasFile('media')) {
            if ($adslot->imgPath && file_exists(public_path($adslot->imgPath))) {
                unlink(public_path($adslot->imgPath));
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

        $adslot->update([
            'imgPath' => $mediaPath,
            'mediaType' => $mediaType,
            'slotOrder' => $request->input('slotOrder', $adslot->slotOrder),
            'duration' => $request->input('duration', $adslot->duration ?? 5),
            'modifiedby' => session('manager_name', 'manager'),
            'modifieddate' => now(),
        ]);

        return redirect()->route('manager.adslots.index')
                       ->with('success', 'Media updated successfully!');
    }

    public function destroy(HomepageAd $adslot)
    {
        $adslot->update([
            'isActive' => 0,
            'modifiedby' => session('manager_name', 'manager'),
            'modifieddate' => now(),
        ]);

        return redirect()->route('manager.adslots.index')
                       ->with('success', 'Media removed successfully!');
    }
}
