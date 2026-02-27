<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UAEActivity;
use App\Models\UAEActivityDetail;
use App\Models\Emirates; // Add this import
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class UAEActivityAdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userType = session('user_type', 'unknown');
        $isPartnerRestricted = session('is_partner_restricted', false);
        $isApprovedPartner = ($userType === 'approved_partner' && $isPartnerRestricted === true);
        
        // Log::info('🔍 Loading activities for user', [
        //     'user_id' => $user->id,
        //     'user_name' => strtolower($user->name),
        //     'user_type' => $userType,
        //     'is_partner_restricted' => $isPartnerRestricted
        // ]);

        // Build the query with eager loading - Updated to include emirates relationship
        $query = UAEActivity::with(['details', 'emirate'])
                             ->where('isActive', 1);

        // Apply filtering based on user type
        if ($isApprovedPartner) {
            // For approved partners, filter by createdBy using case-insensitive comparison
            $query->whereRaw('LOWER(createdBy) = ?', [strtolower($user->name)]);
            
            // Log::info('🤝 Applied partner filter', [
            //     'partner_name' => strtolower($user->name),
            //     'filter_applied' => 'LOWER(createdBy) = LOWER(?)'
            // ]);
        } else {
            // Log::info('👑 Admin access - showing all activities');
        }

        // Get paginated results with original ordering
        $activities = $query->orderBy('createdDate', 'desc')->paginate(10);
        
        // Log::info('📊 Activities loaded', [
        //     'total_found' => $activities->total(),
        //     'current_page_count' => $activities->count(),
        //     'user_can_see' => $isApprovedPartner ? 'only_own_activities' : 'all_activities'
        // ]);

        // Debug: Log first few activities for troubleshooting
        if ($activities->count() > 0) {
            foreach ($activities->take(3) as $activity) {
                // Log::info('🔍 Activity debug', [
                //     'activity_id' => $activity->activityID ?? 'N/A',
                //     'activity_name' => $activity->activityName ?? 'N/A',
                //     'createdBy' => $activity->createdBy ?? 'N/A',
                //     'createdBy_lower' => strtolower($activity->createdBy ?? ''),
                //     'user_name_lower' => strtolower($user->name),
                //     'matches' => strtolower($activity->createdBy ?? '') === strtolower($user->name) ? 'YES' : 'NO'
                // ]);
            }
        }

        // Pass additional variables to view for session-based logic
        return view('admin.uaeactivities.index', compact(
            'activities', 
            'user', 
            'userType', 
            'isPartnerRestricted', 
            'isApprovedPartner'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        $userType = session('user_type', 'unknown');
        $isPartnerRestricted = session('is_partner_restricted', false);
        $isApprovedPartner = ($userType === 'approved_partner' && $isPartnerRestricted === true);
        
        // Get emirates based on user type
        if ($isApprovedPartner && $user->email_verified_at) {
            // For approved partners, parse their email_verified_at to get allowed emirates
            $parts = explode('rseparator', $user->email_verified_at, 3);
            $allowedEmiratesIds = isset($parts[0]) && !empty($parts[0]) ? explode(',', $parts[0]) : [];
            
            // Get emirates that match partner's allowed emirates
            $emirates = Emirates::where('isActive', 1)
                        ->whereIn('emiratesID', $allowedEmiratesIds)
                        ->orderBy('emiratesName')
                        ->get();
                        
            // Log::info('🤝 Partner create - filtered emirates', [
            //     'partner_id' => $user->id,
            //     'allowed_emirates' => $allowedEmiratesIds,
            //     'available_emirates_count' => $emirates->count()
            // ]);
        } else {
            // For admin users, show all active emirates
            $emirates = Emirates::where('isActive', 1)
                               ->orderBy('emiratesName')
                               ->get();
                               
            // Log::info('👑 Admin create - all emirates available', [
            //     'emirates_count' => $emirates->count()
            // ]);
        }
        
        return view('admin.uaeactivities.create', compact(
            'emirates', 
            'user', 
            'userType', 
            'isPartnerRestricted', 
            'isApprovedPartner'
        ));
    }

    public function store(Request $request)
{
    $user = Auth::user();
    
    // Enable logging to debug detailsOverview submission
    Log::info('=== UAE Activity Store Request Started ===');
    Log::info('User:', ['name' => $user->name, 'id' => $user->id]);
    Log::info('detailsOverview received:', [
        'value' => $request->input('detailsOverview'),
        'length' => strlen($request->input('detailsOverview') ?? ''),
        'type' => gettype($request->input('detailsOverview'))
    ]);

    $request->validate([
        'activityName' => 'required|string|max:255',
        'activityLocation' => 'required|string|max:255',
        'emiratesID' => 'required|exists:tbl_emirates,emiratesID',
        'activityPrice' => 'required|numeric|min:0',
        'activityChildPrice' => 'nullable|numeric|min:0',
        'activityTransactionCharges' => 'nullable|numeric|min:0',
        'dubaiPrice' => 'nullable|numeric|min:0',
        'abuDhabiPrice' => 'nullable|numeric|min:0',
        'fromAbuDhabiToDubai' => 'nullable|numeric|min:0',
        'emirates' => 'nullable|numeric|min:0',
        'supplierName' => 'nullable|string|max:255',
        'supplierEmail' => 'nullable|email|max:255',
        'activityCategory' => 'nullable|string|max:255',
        'activityImageFiles' => 'required|array',
        'activityImageFiles.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        'detailsOverview' => 'required|string',
        'detailsIminfo' => 'required|array',
        'detailsIminfo.*' => 'required|string',
        'detailsHighlights' => 'required|array',
        'detailsHighlights.*' => 'required|string'
    ]);

    Log::info('✅ Validation passed');

    // Create assets/activities directory if it doesn't exist
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

    // First image for main activity table
    $firstImage = count($imagePaths) > 0 ? $imagePaths[0] : '';

    // Generate clean URL route from activity name
    $activityRoute = Str::slug($request->activityName);

    // Create main activity record
    $activity = UAEActivity::create([
        'activityName' => $request->activityName,
        'activityLocation' => $request->activityLocation,
        'emiratesID' => $request->emiratesID,
        'activityPrice' => $request->activityPrice,
        'activityChildPrice' => $request->activityChildPrice ?? 0,
        'activityTransactionCharges' => $request->activityTransactionCharges ?? 0,
        'dubaiPrice' => $request->dubaiPrice ?? 0,
        'abuDhabiPrice' => $request->abuDhabiPrice ?? 0,
        'fromAbuDhabiToDubai' => $request->fromAbuDhabiToDubai ?? 0,
        'emirates' => $request->emirates ?? 0,
        'activityImage' => $firstImage,
        'activityCurrency' => 'AED',
        'activityRoute' => $activityRoute,
        'supplierName' => $request->supplierName ?? '',
        'supplierEmail' => $request->supplierEmail ?? '',
        'activityCategory' => $request->activityCategory ?? '',
        'createdBy' => $user->name,
        'isActive' => 1,
        'createdDate' => now(),
        'modifiedDate' => null
    ]);

    Log::info('✅ Activity created:', ['activityID' => $activity->activityID]);

    // Create activity details record - store HTML from Quill directly
    UAEActivityDetail::create([
        'activityID' => $activity->activityID,
        'detailsOverview' => $request->detailsOverview, // Store HTML string directly
        'detailsIminfo' => implode('#cseparator', $request->detailsIminfo),
        'detailsHighlights' => implode('#cseparator', $request->detailsHighlights),
        'activityImage' => count($imagePaths) > 0 ? implode('#cseparator', $imagePaths) : '',
    ]);

    Log::info('✅ Activity details created successfully');
    Log::info('=== UAE Activity Store Request Completed ===');

    return redirect()->route('admin.uaeactivities.index')
                     ->with('success', 'UAE Activity created successfully!');
}


    public function edit($id)
    {
        $activity = UAEActivity::where('activityID', $id)->firstOrFail();

        $user = Auth::user();
        $userType = session('user_type', 'unknown');
        $isPartnerRestricted = session('is_partner_restricted', false);
        $isApprovedPartner = ($userType === 'approved_partner' && $isPartnerRestricted === true);
        $details = UAEActivityDetail::where('activityID', $activity->activityID)->first();
        
        // Log::info('📝 Editing activity', [
        //     'activity_id' => $activity->activityID,
        //     'user_id' => $user->id,
        //     'user_name' => $user->name,
        //     'activity_created_by' => $activity->createdBy,
        //     'is_partner' => $isApprovedPartner
        // ]);

        // Check if approved partner can edit this activity
        if ($isApprovedPartner) {
            // Check if partner created this activity using case-insensitive comparison
            if (strtolower($activity->createdBy ?? '') !== strtolower($user->name)) {
                // Log::warning('❌ Partner attempted to edit activity they did not create', [
                //     'user_name' => strtolower($user->name),
                //     'activity_created_by' => strtolower($activity->createdBy ?? ''),
                //     'activity_id' => $activity->activityID
                // ]);
                
                return redirect()->route('admin.uaeactivities.index')
                               ->withErrors(['access' => 'You can only edit activities that you created.']);
            }
        }
        
        // Get emirates based on user type for dropdown
        if ($isApprovedPartner && $user->email_verified_at) {
            // For approved partners, parse their email_verified_at to get allowed emirates
            $parts = explode('rseparator', $user->email_verified_at, 3);
            $allowedEmiratesIds = isset($parts[0]) && !empty($parts[0]) ? explode(',', $parts[0]) : [];
            
            // Get emirates that match partner's allowed emirates
            $emirates = Emirates::where('isActive', 1)
                        ->whereIn('emiratesID', $allowedEmiratesIds)
                        ->orderBy('emiratesName')
                        ->get();
        } else {
            // For admin users, show all active emirates
            $emirates = Emirates::where('isActive', 1)
                               ->orderBy('emiratesName')
                               ->get();
        }
        
        // Parse #cseparator fields for editing
        $detailsIminfo = [];
        $detailsHighlights = [];
        $existingImages = [];
        
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
        
        return view('admin.uaeactivities.edit', compact(
            'activity', 
            'details', 
            'detailsIminfo', 
            'detailsHighlights', 
            'existingImages', 
            'emirates',
            'user', 
            'userType', 
            'isPartnerRestricted', 
            'isApprovedPartner'
        ));
    }

    public function update(Request $request, $id)
    {
        try {
        $activity = UAEActivity::where('activityID', $id)->firstOrFail();

        $user = Auth::user();
        $userType = session('user_type', 'unknown');
        $isPartnerRestricted = session('is_partner_restricted', false);
        $isApprovedPartner = ($userType === 'approved_partner' && $isPartnerRestricted === true);
        
        // Log::info('🔄 Updating activity', [
        //     'activity_id' => $activity->activityID,
        //     'user_id' => $user->id,
        //     'user_name' => $user->name,
        //     'activity_created_by' => $activity->createdBy,
        //     'is_partner' => $isApprovedPartner
        // ]);

        // Check if approved partner can update this activity
        if ($isApprovedPartner) {
            // Check if partner created this activity using case-insensitive comparison
            if (strtolower($activity->createdBy ?? '') !== strtolower($user->name)) {
                // Log::warning('❌ Partner attempted to update activity they did not create', [
                //     'user_name' => strtolower($user->name),
                //     'activity_created_by' => strtolower($activity->createdBy ?? ''),
                //     'activity_id' => $activity->activityID
                // ]);
                
                return redirect()->route('admin.uaeactivities.index')
                               ->withErrors(['access' => 'You can only update activities that you created.']);
            }
        }
        
        $request->validate([
            'activityName' => 'required|string|max:255',
            'activityLocation' => 'required|string|max:255',
            'emiratesID' => 'required|exists:tbl_emirates,emiratesID', // Add emirates validation
            'activityPrice' => 'required|numeric|min:0',
            'activityChildPrice' => 'nullable|numeric|min:0',
            'activityTransactionCharges' => 'nullable|numeric|min:0',
            'dubaiPrice' => 'nullable|numeric|min:0',
            'abuDhabiPrice' => 'nullable|numeric|min:0',
            // NEW: Add validation for the new transport pricing fields
            'fromAbuDhabiToDubai' => 'nullable|numeric|min:0',
            'emirates' => 'nullable|numeric|min:0',
            'supplierName' => 'nullable|string|max:255',
            'supplierEmail' => 'nullable|email|max:255',
            'activityImageFiles' => 'nullable|array',
            'activityImageFiles.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'detailsOverview' => 'nullable|string',
            'detailsIminfo' => 'nullable|array',
            'detailsIminfo.*' => 'nullable|string',
            'detailsHighlights' => 'nullable|array',
            'detailsHighlights.*' => 'nullable|string',
            'replace_images' => 'nullable|boolean'
        ]);

        // Get existing details — use firstOrNew so it works even if no row exists yet
        $details = UAEActivityDetail::where('activityID', $activity->activityID)->first();
        $isNewDetails = false;
        if (!$details) {
            $details = new UAEActivityDetail();
            $details->activityID = $activity->activityID;
            $isNewDetails = true;
        }
        $existingImages = [];

        if ($details->activityImage) {
            $existingImages = explode('#cseparator', $details->activityImage);
        }

        $imagePaths = [];
        $finalImagePaths = $existingImages; // Default to existing images

        // Handle new image uploads if provided
        if ($request->hasFile('activityImageFiles')) {
            $destinationPath = public_path('assets/activities');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Upload new images
            foreach ($request->file('activityImageFiles') as $imageFile) {
                $filename = time() . '_' . Str::random(10) . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move($destinationPath, $filename);
                $imagePaths[] = 'assets/activities/' . $filename;
            }

            // Determine if we should replace existing images or add to them
            $imageAction = $request->input('image_action', 'add');
            if ($imageAction === 'replace' || ($request->has('replace_images') && $request->replace_images)) {
                // Replace existing images - delete old local files first
                foreach ($existingImages as $oldImage) {
                    if (!str_starts_with($oldImage, 'http')) {
                        $oldImagePath = public_path($oldImage);
                        if (File::exists($oldImagePath)) {
                            File::delete($oldImagePath);
                        }
                    }
                }
                $finalImagePaths = $imagePaths; // Use only new images
            } else {
                // Add new images FIRST so the card thumbnail updates to the new image
                $finalImagePaths = array_merge($imagePaths, $existingImages);
            }
        }

        // UPDATED: Prepare data for update - Include new fields AND emiratesID
        $activityUpdateData = [];
        $fieldsToCheck = [
            'activityName', 'activityLocation', 'emiratesID',
            'activityPrice',
            'activityChildPrice', 'activityTransactionCharges',
            'dubaiPrice', 'abuDhabiPrice',
            'fromAbuDhabiToDubai', 'emirates',
            'supplierName', 'supplierEmail', 'activityCategory',
        ];

        foreach ($fieldsToCheck as $field) {
            if ($request->has($field)) {
                $activityUpdateData[$field] = $request->input($field);
            }
        }

        // Update first image only if images were modified
        if (!empty($imagePaths) || $request->input('image_action') === 'replace') {
            $activityUpdateData['activityImage'] = count($finalImagePaths) > 0 ? $finalImagePaths[0] : '';
        }

        // Update route slug if name changed
        if (isset($activityUpdateData['activityName'])) {
            $activityUpdateData['activityRoute'] = Str::slug($activityUpdateData['activityName']);
        }

        // Always update modification info
        $activityUpdateData['modifiedBy'] = $user->name;
        $activityUpdateData['modifiedDate'] = now();

        // Update main activity record only with changed fields
        $activity->update($activityUpdateData);

        // Prepare details update data
        $detailsUpdateData = [];

        if ($request->has('detailsOverview')) {
            $detailsUpdateData['detailsOverview'] = $request->detailsOverview;
        }

        if ($request->has('detailsIminfo')) {
            $detailsUpdateData['detailsIminfo'] = implode('#cseparator', $request->detailsIminfo);
        }

        if ($request->has('detailsHighlights')) {
            $detailsUpdateData['detailsHighlights'] = implode('#cseparator', $request->detailsHighlights);
        }

        // Update images in details if they were modified
        if (!empty($imagePaths) || $request->input('image_action') === 'replace') {
            $detailsUpdateData['activityImage'] = count($finalImagePaths) > 0 ? implode('#cseparator', $finalImagePaths) : '';
        }

        // Set creation fields if this is a new record
        if ($isNewDetails) {
            // Ensure activityImage has a value for new records
            if (!isset($detailsUpdateData['activityImage'])) {
                $detailsUpdateData['activityImage'] = '';
            }
        }

        // Update details with only changed fields
        foreach ($detailsUpdateData as $key => $value) {
            $details->$key = $value;
        }

        $details->save();

        return redirect()->route('admin.uaeactivities.index')
                       ->with('success', 'UAE Activity updated successfully!');

        } catch (\Exception $e) {
            Log::error('UPDATE FAILED: ' . $e->getMessage(), [
                'id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('admin.uaeactivities.index')
                           ->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $activity = UAEActivity::where('activityID', $id)->firstOrFail();

        $user = Auth::user();
        $userType = session('user_type', 'unknown');
        $isPartnerRestricted = session('is_partner_restricted', false);
        $isApprovedPartner = ($userType === 'approved_partner' && $isPartnerRestricted === true);
        
        // Log::info('🗑️ Deleting activity', [
        //     'activity_id' => $activity->activityID,
        //     'user_id' => $user->id,
        //     'user_name' => $user->name,
        //     'activity_created_by' => $activity->createdBy,
        //     'is_partner' => $isApprovedPartner
        // ]);

        // Check if approved partner can delete this activity
        if ($isApprovedPartner) {
            // Check if partner created this activity using case-insensitive comparison
            if (strtolower($activity->createdBy ?? '') !== strtolower($user->name)) {
                // Log::warning('❌ Partner attempted to delete activity they did not create', [
                //     'user_name' => strtolower($user->name),
                //     'activity_created_by' => strtolower($activity->createdBy ?? ''),
                //     'activity_id' => $activity->activityID
                // ]);
                
                return redirect()->route('admin.uaeactivities.index')
                               ->withErrors(['access' => 'You can only delete activities that you created.']);
            }
        }
        
        // Soft delete main activity
        $activity->update([
            'isActive' => 0,
            'modifiedDate' => now()
        ]);

        // Delete activity details
        UAEActivityDetail::where('activityID', $activity->activityID)->delete();

        // Log::info('✅ Activity deleted successfully', [
        //     'activity_id' => $activity->activityID,
        //     'deleted_by' => $user->name
        // ]);

        return redirect()->route('admin.uaeactivities.index')
                       ->with('success', 'UAE Activity deleted successfully!');
    }

    /**
     * Show method to display single activity
     */
    public function show($id)
    {
        Log::info('SHOW method hit', ['id' => $id]);
        return redirect()->route('admin.uaeactivities.edit', $id);
    }

    /**
     * Helper method to delete physical image files
     */
    private function deleteImageFiles($imagePaths)
    {
        if (is_string($imagePaths)) {
            $imagePaths = explode('#cseparator', $imagePaths);
        }

        foreach ($imagePaths as $imagePath) {
            $fullPath = public_path($imagePath);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }
    }
}
