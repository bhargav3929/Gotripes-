<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\UAEActivity;
use App\Models\UAEActivityDetail;
use App\Models\Emirates;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ManagerActivitiesController extends Controller
{
    /**
     * Display a listing of activities.
     */
    public function index()
    {
        $activities = UAEActivity::with(['details', 'emirate'])
                                 ->where('isActive', 1)
                                 ->orderBy('createdDate', 'desc')
                                 ->paginate(10);

        return view('manager.activities.index', compact('activities'));
    }

    /**
     * Show the form for creating a new activity.
     */
    public function create()
    {
        $emirates = Emirates::where('isActive', 1)
                           ->orderBy('emiratesName')
                           ->get();

        return view('manager.activities.create', compact('emirates'));
    }

    /**
     * Store a newly created activity.
     */
    public function store(Request $request)
    {
        $request->validate([
            'activityName'               => 'required|string|max:255',
            'activityLocation'           => 'required|string|max:255',
            'emiratesID'                 => 'required|exists:tbl_emirates,emiratesID',
            'activityPrice'              => 'required|numeric|min:0',
            'activityCategory'           => 'nullable|string|max:100',
            'activityChildPrice'         => 'nullable|numeric|min:0',
            'activityTransactionCharges' => 'nullable|numeric|min:0',
            'supplierName'               => 'nullable|string|max:255',
            'supplierEmail'              => 'nullable|email|max:255',
            'activityImageFiles'         => 'required|array|min:1',
            'activityImageFiles.*'       => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'detailsOverview'            => 'required|string',
            'detailsIminfo'              => 'required|array|min:1',
            'detailsIminfo.*'            => 'required|string',
            'detailsHighlights'          => 'required|array|min:1',
            'detailsHighlights.*'        => 'required|string',
        ]);

        // Ensure upload directory exists
        $destinationPath = public_path('assets/activities');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Handle multiple image uploads
        $imagePaths = [];
        foreach ($request->file('activityImageFiles') as $imageFile) {
            $filename = time() . '_' . Str::random(10) . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move($destinationPath, $filename);
            $imagePaths[] = 'assets/activities/' . $filename;
        }

        $firstImage = count($imagePaths) > 0 ? $imagePaths[0] : '';

        // Create main activity record
        $activity = UAEActivity::create([
            'activityName'               => $request->activityName,
            'activityLocation'           => $request->activityLocation,
            'emiratesID'                 => $request->emiratesID,
            'activityPrice'              => $request->activityPrice,
            'activityCategory'           => $request->activityCategory,
            'activityChildPrice'         => $request->activityChildPrice ?? 0,
            'activityTransactionCharges' => $request->activityTransactionCharges ?? 0,
            'supplierName'               => $request->supplierName,
            'supplierEmail'              => $request->supplierEmail,
            'activityImage'              => $firstImage,
            'isActive'                   => 1,
            'createdBy'                  => session('manager_name', 'manager'),
            'createdDate'                => now(),
            'modifiedBy'                 => null,
            'modifiedDate'               => null,
        ]);

        // Create activity details record
        UAEActivityDetail::create([
            'activityID'        => $activity->activityID,
            'detailsOverview'   => $request->detailsOverview,
            'detailsIminfo'     => implode('#cseparator', $request->detailsIminfo),
            'detailsHighlights' => implode('#cseparator', $request->detailsHighlights),
            'activityImage'     => implode('#cseparator', $imagePaths),
            'isActive'          => 1,
            'createdBy'         => session('manager_name', 'manager'),
            'createdDate'       => now(),
            'modifiedBy'        => null,
            'modifiedDate'      => null,
        ]);

        return redirect()->route('manager.activities.index')
                         ->with('success', 'Activity created successfully!');
    }

    /**
     * Show the form for editing the specified activity.
     */
    public function edit($id)
    {
        $activity = UAEActivity::where('activityID', $id)
                               ->where('isActive', 1)
                               ->firstOrFail();

        $details = UAEActivityDetail::where('activityID', $activity->activityID)->first();

        $emirates = Emirates::where('isActive', 1)
                           ->orderBy('emiratesName')
                           ->get();

        // Parse #cseparator fields for editing
        $detailsIminfo     = [];
        $detailsHighlights = [];
        $existingImages    = [];

        if ($details) {
            if ($details->detailsIminfo) {
                $detailsIminfo = explode('#cseparator', $details->detailsIminfo);
            }
            if ($details->detailsHighlights) {
                $detailsHighlights = explode('#cseparator', $details->detailsHighlights);
            }
            if ($details->activityImage) {
                $existingImages = explode('#cseparator', $details->activityImage);
            }
        }

        return view('manager.activities.edit', compact(
            'activity',
            'details',
            'emirates',
            'detailsIminfo',
            'detailsHighlights',
            'existingImages'
        ));
    }

    /**
     * Update the specified activity.
     */
    public function update(Request $request, $id)
    {
        $activity = UAEActivity::where('activityID', $id)
                               ->where('isActive', 1)
                               ->firstOrFail();

        $request->validate([
            'activityName'               => 'required|string|max:255',
            'activityLocation'           => 'required|string|max:255',
            'emiratesID'                 => 'required|exists:tbl_emirates,emiratesID',
            'activityPrice'              => 'required|numeric|min:0',
            'activityCategory'           => 'nullable|string|max:100',
            'activityChildPrice'         => 'nullable|numeric|min:0',
            'activityTransactionCharges' => 'nullable|numeric|min:0',
            'supplierName'               => 'nullable|string|max:255',
            'supplierEmail'              => 'nullable|email|max:255',
            'activityImageFiles'         => 'nullable|array',
            'activityImageFiles.*'       => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'detailsOverview'            => 'required|string',
            'detailsIminfo'              => 'required|array|min:1',
            'detailsIminfo.*'            => 'required|string',
            'detailsHighlights'          => 'required|array|min:1',
            'detailsHighlights.*'        => 'required|string',
            'replace_images'             => 'nullable|boolean',
        ]);

        // Get existing details
        $details = UAEActivityDetail::firstOrNew(['activityID' => $activity->activityID]);
        $existingImages = [];

        if ($details && $details->activityImage) {
            $existingImages = explode('#cseparator', $details->activityImage);
        }

        $finalImagePaths = $existingImages;

        // Handle new image uploads if provided
        if ($request->hasFile('activityImageFiles')) {
            $destinationPath = public_path('assets/activities');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $newImages = [];
            foreach ($request->file('activityImageFiles') as $imageFile) {
                $filename = time() . '_' . Str::random(10) . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move($destinationPath, $filename);
                $newImages[] = 'assets/activities/' . $filename;
            }

            if ($request->has('replace_images') && $request->replace_images) {
                // Delete old files
                foreach ($existingImages as $oldImage) {
                    $oldPath = public_path($oldImage);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }
                $finalImagePaths = $newImages;
            } else {
                $finalImagePaths = array_merge($existingImages, $newImages);
            }
        }

        // Update main activity record
        $activity->update([
            'activityName'               => $request->activityName,
            'activityLocation'           => $request->activityLocation,
            'emiratesID'                 => $request->emiratesID,
            'activityPrice'              => $request->activityPrice,
            'activityCategory'           => $request->activityCategory,
            'activityChildPrice'         => $request->activityChildPrice ?? 0,
            'activityTransactionCharges' => $request->activityTransactionCharges ?? 0,
            'supplierName'               => $request->supplierName,
            'supplierEmail'              => $request->supplierEmail,
            'activityImage'              => count($finalImagePaths) > 0 ? $finalImagePaths[0] : '',
            'modifiedBy'                 => session('manager_name', 'manager'),
            'modifiedDate'               => now(),
        ]);

        // Update or create details
        $detailsData = [
            'detailsOverview'   => $request->detailsOverview,
            'detailsIminfo'     => implode('#cseparator', $request->detailsIminfo),
            'detailsHighlights' => implode('#cseparator', $request->detailsHighlights),
            'activityImage'     => implode('#cseparator', $finalImagePaths),
            'isActive'          => 1,
            'modifiedBy'        => session('manager_name', 'manager'),
            'modifiedDate'      => now(),
        ];

        if (!$details->exists) {
            $detailsData['createdBy']   = session('manager_name', 'manager');
            $detailsData['createdDate'] = now();
        }

        foreach ($detailsData as $key => $value) {
            $details->$key = $value;
        }
        $details->save();

        return redirect()->route('manager.activities.index')
                         ->with('success', 'Activity updated successfully!');
    }

    /**
     * Soft-delete the specified activity.
     */
    public function destroy($id)
    {
        $activity = UAEActivity::where('activityID', $id)
                               ->where('isActive', 1)
                               ->firstOrFail();

        // Soft delete main activity
        $activity->update([
            'isActive'     => 0,
            'modifiedBy'   => session('manager_name', 'manager'),
            'modifiedDate' => now(),
        ]);

        // Soft delete activity details
        UAEActivityDetail::where('activityID', $activity->activityID)
                         ->update([
                             'isActive'     => 0,
                             'modifiedBy'   => session('manager_name', 'manager'),
                             'modifiedDate' => now(),
                         ]);

        return redirect()->route('manager.activities.index')
                         ->with('success', 'Activity deleted successfully!');
    }
}
