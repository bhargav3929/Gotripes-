<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UAEActivity;
use App\Models\Emirates;

class UAEDetailsController extends Controller
{
    public function show(Request $request)
    {
        $id = $request->input('id'); // get ?id=... from query string
        $emirateId = $request->input('emirateId'); // get ?emirateId=... from query string

        $activity = null;
        $detail = null;
        $activityImages = [];
        $emirate = null;

        if ($id) {
            $activity = UAEActivity::find($id);
            $detail = $activity ? $activity->details : null;
            
            if ($detail && !empty($detail->activityImage)) {
                // Split using the #cseparator
                $activityImages = array_filter(
                    explode('#cseparator', $detail->activityImage),
                    fn($img) => trim($img) !== ''
                );
            }
        }

        // Get emirate details if emirateId is provided
        if ($emirateId) {
            $emirate = Emirates::where('emiratesID', $emirateId)
                              ->where('isActive', 1)
                              ->first();
        }

        // Now $activityImages is an array of image paths ready to use in your carousel
        return view('dubai-global-village', compact('activity', 'detail', 'activityImages', 'emirate'));
    }
}
