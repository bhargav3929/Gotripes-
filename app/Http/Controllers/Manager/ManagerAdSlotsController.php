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
                             ->paginate(10);
        return view('manager.adslots.index', compact('adSlots'));
    }

    public function create()
    {
        $activeCount = HomepageAd::where('isActive', 1)->count();
        return view('manager.adslots.create', compact('activeCount'));
    }

    public function store(Request $request)
    {
        $activeCount = HomepageAd::where('isActive', 1)->count();
        if ($activeCount >= 6) {
            return redirect()->back()->withErrors('Maximum 6 active ad slots allowed. Please remove one first.');
        }

        $mediaType = $request->input('mediaType', 'image');

        if ($mediaType === 'video') {
            $request->validate([
                'media' => 'required|file|mimes:mp4|max:51200',
                'slotOrder' => 'required|integer|min:1|max:6',
            ]);
        } else {
            $request->validate([
                'media' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'slotOrder' => 'required|integer|min:1|max:6',
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

            HomepageAd::create([
                'imgPath' => $mediaPath,
                'mediaType' => $mediaType,
                'slotOrder' => $request->input('slotOrder', 0),
                'isActive' => 1,
                'createdby' => session('manager_name', 'manager'),
                'createddate' => now(),
                'modifiedby' => null,
                'modifieddate' => null,
            ]);

            return redirect()->route('manager.adslots.index')
                           ->with('success', 'Ad slot uploaded successfully!');
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
                    'slotOrder' => 'required|integer|min:1|max:6',
                ]);
            } else {
                $request->validate([
                    'media' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                    'slotOrder' => 'required|integer|min:1|max:6',
                ]);
            }
        } else {
            $request->validate([
                'slotOrder' => 'required|integer|min:1|max:6',
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
            'modifiedby' => session('manager_name', 'manager'),
            'modifieddate' => now(),
        ]);

        return redirect()->route('manager.adslots.index')
                       ->with('success', 'Ad slot updated successfully!');
    }

    public function destroy(HomepageAd $adslot)
    {
        $adslot->update([
            'isActive' => 0,
            'modifiedby' => session('manager_name', 'manager'),
            'modifieddate' => now(),
        ]);

        return redirect()->route('manager.adslots.index')
                       ->with('success', 'Ad slot removed successfully!');
    }
}
