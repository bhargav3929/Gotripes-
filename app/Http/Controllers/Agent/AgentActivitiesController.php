<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\UAEActivity;
use App\Models\UAEActivityDetail;
use App\Models\Emirates;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

/**
 * Agent-portal activities. Same rules and storage layout as the manager
 * version (ManagerActivitiesController), but scoped to the logged-in
 * agent's own listings; no read-only platform tab.
 */
class AgentActivitiesController extends Controller
{
    /** Base query: active activities owned by this agent (tenant scope auto-applied). */
    private function ownActivities()
    {
        return UAEActivity::where('agent_id', auth()->id())->where('isActive', 1);
    }

    public function index()
    {
        $activities = $this->ownActivities()
            ->with(['details', 'emirate'])
            ->orderBy('createdDate', 'desc')
            ->paginate(10);

        return view('agent.activities.index', compact('activities'));
    }

    public function create()
    {
        $emirates  = Emirates::where('isActive', 1)->orderBy('emiratesName')->get();
        $countries = config('countries');

        return view('agent.activities.create', compact('emirates', 'countries'));
    }

    private function resolveActivityCountry(Request $request): string
    {
        return trim($request->input('country', '')) ?: 'United Arab Emirates';
    }

    private function isUAE(string $country): bool
    {
        return strtolower(trim($country)) === 'united arab emirates';
    }

    public function store(Request $request)
    {
        $country = $this->resolveActivityCountry($request);
        $request->validate([
            'activityName'               => 'required|string|max:255',
            'activityLocation'           => 'required|string|max:255',
            'country'                    => 'required|string|max:100',
            'emiratesID'                 => $this->isUAE($country)
                                            ? 'required|exists:tbl_emirates,emiratesID'
                                            : 'nullable|exists:tbl_emirates,emiratesID',
            'activityPrice'              => 'required|numeric|min:0',
            'activityCurrency'           => 'nullable|string|max:3',
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

        $companyId = current_company()?->id ?? 0;
        $relativeDir = "assets/activities/{$companyId}";
        $destinationPath = public_path($relativeDir);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $imagePaths = [];
        foreach ($request->file('activityImageFiles') as $imageFile) {
            $filename = time() . '_' . Str::random(10) . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move($destinationPath, $filename);
            $imagePaths[] = $relativeDir . '/' . $filename;
        }

        $firstImage = count($imagePaths) > 0 ? $imagePaths[0] : '';

        $activity = UAEActivity::create([
            'agent_id'                   => auth()->id(),
            'activityName'               => $request->activityName,
            'activityLocation'           => $request->activityLocation,
            'emiratesID'                 => $this->isUAE($country) ? $request->emiratesID : null,
            'country'                    => $country,
            'activityCurrency'           => strtoupper($request->activityCurrency ?: ($this->isUAE($country) ? 'AED' : 'USD')),
            'activityPrice'              => $request->activityPrice,
            'activityCategory'           => $request->activityCategory,
            'activityChildPrice'         => $request->activityChildPrice ?? 0,
            'activityTransactionCharges' => $request->activityTransactionCharges ?? 0,
            'supplierName'               => $request->supplierName,
            'supplierEmail'              => $request->supplierEmail,
            'activityImage'              => $firstImage,
            'isActive'                   => 1,
            'createdBy'                  => auth()->user()?->name ?? 'agent',
            'createdDate'                => now(),
            'modifiedBy'                 => null,
            'modifiedDate'               => null,
        ]);

        UAEActivityDetail::create([
            'activityID'        => $activity->activityID,
            'detailsOverview'   => $request->detailsOverview,
            'detailsIminfo'     => implode('#cseparator', $request->detailsIminfo),
            'detailsHighlights' => implode('#cseparator', $request->detailsHighlights),
            'activityImage'     => implode('#cseparator', $imagePaths),
            'isActive'          => 1,
            'createdBy'         => auth()->user()?->name ?? 'agent',
            'createdDate'       => now(),
            'modifiedBy'        => null,
            'modifiedDate'      => null,
        ]);

        return redirect()->route('agent.activities.index')
                         ->with('success', 'Activity created successfully!');
    }

    public function edit($id)
    {
        $activity = $this->ownActivities()
                         ->where('activityID', $id)
                         ->firstOrFail();

        $details = UAEActivityDetail::where('activityID', $activity->activityID)->first();

        $emirates = Emirates::where('isActive', 1)
                           ->orderBy('emiratesName')
                           ->get();

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

        $countries = config('countries');

        return view('agent.activities.edit', compact(
            'activity',
            'details',
            'emirates',
            'detailsIminfo',
            'detailsHighlights',
            'existingImages',
            'countries'
        ));
    }

    public function update(Request $request, $id)
    {
        $activity = $this->ownActivities()
                         ->where('activityID', $id)
                         ->firstOrFail();

        $country = $this->resolveActivityCountry($request);
        $request->validate([
            'activityName'               => 'required|string|max:255',
            'activityLocation'           => 'required|string|max:255',
            'country'                    => 'required|string|max:100',
            'emiratesID'                 => $this->isUAE($country)
                                            ? 'required|exists:tbl_emirates,emiratesID'
                                            : 'nullable|exists:tbl_emirates,emiratesID',
            'activityPrice'              => 'required|numeric|min:0',
            'activityCurrency'           => 'nullable|string|max:3',
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

        $details = UAEActivityDetail::firstOrNew(['activityID' => $activity->activityID]);
        $existingImages = [];

        if ($details && $details->activityImage) {
            $existingImages = explode('#cseparator', $details->activityImage);
        }

        $finalImagePaths = $existingImages;

        if ($request->hasFile('activityImageFiles')) {
            $companyId = current_company()?->id ?? 0;
            $relativeDir = "assets/activities/{$companyId}";
            $destinationPath = public_path($relativeDir);
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $newImages = [];
            foreach ($request->file('activityImageFiles') as $imageFile) {
                $filename = time() . '_' . Str::random(10) . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move($destinationPath, $filename);
                $newImages[] = $relativeDir . '/' . $filename;
            }

            if ($request->has('replace_images') && $request->replace_images) {
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

        $activity->update([
            'activityName'               => $request->activityName,
            'activityLocation'           => $request->activityLocation,
            'emiratesID'                 => $this->isUAE($country) ? $request->emiratesID : null,
            'country'                    => $country,
            'activityCurrency'           => strtoupper($request->activityCurrency ?: ($this->isUAE($country) ? 'AED' : 'USD')),
            'activityPrice'              => $request->activityPrice,
            'activityCategory'           => $request->activityCategory,
            'activityChildPrice'         => $request->activityChildPrice ?? 0,
            'activityTransactionCharges' => $request->activityTransactionCharges ?? 0,
            'supplierName'               => $request->supplierName,
            'supplierEmail'              => $request->supplierEmail,
            'activityImage'              => count($finalImagePaths) > 0 ? $finalImagePaths[0] : '',
            'modifiedBy'                 => auth()->user()?->name ?? 'agent',
            'modifiedDate'               => now(),
        ]);

        $detailsData = [
            'detailsOverview'   => $request->detailsOverview,
            'detailsIminfo'     => implode('#cseparator', $request->detailsIminfo),
            'detailsHighlights' => implode('#cseparator', $request->detailsHighlights),
            'activityImage'     => implode('#cseparator', $finalImagePaths),
            'isActive'          => 1,
            'modifiedBy'        => auth()->user()?->name ?? 'agent',
            'modifiedDate'      => now(),
        ];

        if (!$details->exists) {
            $detailsData['createdBy']   = auth()->user()?->name ?? 'agent';
            $detailsData['createdDate'] = now();
        }

        foreach ($detailsData as $key => $value) {
            $details->$key = $value;
        }
        $details->save();

        return redirect()->route('agent.activities.index')
                         ->with('success', 'Activity updated successfully!');
    }

    public function destroy($id)
    {
        $activity = $this->ownActivities()
                         ->where('activityID', $id)
                         ->firstOrFail();

        $activity->update([
            'isActive'     => 0,
            'modifiedBy'   => auth()->user()?->name ?? 'agent',
            'modifiedDate' => now(),
        ]);

        UAEActivityDetail::where('activityID', $activity->activityID)
                         ->update([
                             'isActive'     => 0,
                             'modifiedBy'   => auth()->user()?->name ?? 'agent',
                             'modifiedDate' => now(),
                         ]);

        return redirect()->route('agent.activities.index')
                         ->with('success', 'Activity deleted successfully!');
    }
}
