<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\UAEActivity;
use App\Models\Emirates;

class UAEDetailsController extends Controller
{
    /**
     * Show activity detail using clean URL slugs
     * e.g. /activities/abu-dhabi/natural-history-museum-abu-dhabi
     */
    public function showBySlug($emirateSlug, $activitySlug)
    {
        $emirateName = str_replace('-', ' ', $emirateSlug);
        $emirate = Emirates::where('isActive', 1)
                          ->whereRaw('LOWER(emiratesName) = ?', [strtolower($emirateName)])
                          ->firstOrFail();

        // Find activity by matching slug against activityName
        $activities = UAEActivity::where('emiratesID', $emirate->emiratesID)
                                ->listed()
                                ->get();

        $activity = $activities->first(function ($a) use ($activitySlug) {
            return Str::slug($a->activityName) === $activitySlug;
        });

        abort_unless($activity, 404);

        $detail = $activity->details;
        $activityImages = [];

        if ($detail && !empty($detail->activityImage)) {
            $activityImages = array_filter(
                explode('#cseparator', $detail->activityImage),
                fn($img) => trim($img) !== ''
            );
        }

        return view('dubai-global-village', compact('activity', 'detail', 'activityImages', 'emirate'));
    }

    public function show(Request $request)
    {
        $id = $request->input('id');
        $emirateId = $request->input('emirateId');

        // Redirect old ?id=&emirateId= URLs to clean slugs
        if ($id && $emirateId) {
            // Hidden activities do not get redirected to a pretty URL either —
            // they fall through to the 404 below.
            $activity = UAEActivity::listed()->find($id);
            $emirate = Emirates::where('emiratesID', $emirateId)->where('isActive', 1)->first();

            if ($activity && $emirate) {
                return redirect()->route('activities.detail.slug', [
                    'emirateSlug' => Str::slug($emirate->emiratesName),
                    'activitySlug' => Str::slug($activity->activityName),
                ], 301);
            }
        }

        $activity = null;
        $detail = null;
        $activityImages = [];
        $emirate = null;
        $activityAdultPrice = 0;

        if ($id) {
            // listed(), not find(): this legacy ?id= page would otherwise still
            // show and sell an activity a manager has hidden, because it looks
            // it up straight by primary key.
            $activity = UAEActivity::listed()->find($id);
            abort_unless($activity, 404);

            $detail = $activity->details;
            $activityAdultPrice = ($activity->activityPrice !== null) ? (float) $activity->activityPrice : 0;

            if ($detail && !empty($detail->activityImage)) {
                $activityImages = array_filter(
                    explode('#cseparator', $detail->activityImage),
                    fn($img) => trim($img) !== ''
                );
            }
        }

        if ($emirateId) {
            $emirate = Emirates::where('emiratesID', $emirateId)
                              ->where('isActive', 1)
                              ->first();
        }

        // Now $activityImages is an array of image paths ready to use in your carousel
        return view('dubai-global-village', compact('activity', 'detail', 'activityImages', 'emirate', 'activityAdultPrice'));
    }
}
