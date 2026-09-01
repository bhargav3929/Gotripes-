<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    /**
     * Display a listing of the users (for admin dashboard)
     */
    public function index()
    {
        try {
            $users = User::orderBy('created_at', 'desc')->get();
            return view('admin.users.index', compact('users'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to load users');
        }
    }

    /**
     * Get all active emirates for registration form
     */
    public function getEmirates(): JsonResponse
    {
        try {
            $emirates = DB::table('tbl_emirates')
                ->where('isActive', 1)
                ->select('emiratesID as id', 'emiratesName as name', 'emiratesDescription as description')
                ->orderBy('emiratesName')
                ->get();

            return response()->json([
                'success' => true,
                'emirates' => $emirates,
                'count' => $emirates->count()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load emirates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle partner registration with emirates selection, status tracking, and multiple file upload
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|min:2',
                'phone' => 'required|string|max:20|min:10|unique:users,phone',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|max:255|confirmed',
                'company_name' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'trade_license_number' => 'required|string|max:100',
                'trade_license_expiry_date' => 'required|date|after:today',
                'registering_from_uae' => 'required|boolean',
                'emirate_id' => 'required_if:registering_from_uae,1|nullable|integer',
                'country' => 'required_if:registering_from_uae,0|nullable|string|max:100',
                'services' => 'required|array|min:1',
                'services.*' => ['string', \Illuminate\Validation\Rule::in(array_keys(User::AGENT_SERVICES))],
                'partner_documents' => 'required|array',
                'partner_documents.*' => 'file|mimes:pdf,doc,docx,png,jpg,jpeg|max:5120',
            ], [
                'name.required' => 'Full name is required',
                'name.min' => 'Name must be at least 2 characters',
                'phone.required' => 'Phone number is required',
                'phone.unique' => 'This phone number is already registered',
                'phone.min' => 'Phone number must be at least 10 digits',
                'email.required' => 'Email address is required',
                'email.email' => 'Please enter a valid email address',
                'email.unique' => 'This email is already registered',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 8 characters',
                'password.confirmed' => 'Password and confirm password do not match',
                'company_name.required' => 'Business/company name is required',
                'address.required' => 'Address is required',
                'trade_license_number.required' => 'Trade license number is required',
                'trade_license_expiry_date.required' => 'Trade license expiry date is required',
                'trade_license_expiry_date.after' => 'Trade license expiry date must be in the future',
                'emirate_id.required_if' => 'Please select which Emirate you are registering from',
                'country.required_if' => 'Please select which country you are registering from',
                'services.required' => 'Select at least one product/service you want to sell',
                'partner_documents.required' => 'At least one document must be uploaded',
                'partner_documents.*.mimes' => 'Only pdf, doc, docx, png, jpg, jpeg files are allowed',
                'partner_documents.*.max' => 'Uploaded file may not be greater than 5MB',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $isUae = $request->boolean('registering_from_uae');
            $emirateName = null;
            $emiratesIdsCsv = '';

            if ($isUae) {
                $emirate = DB::table('tbl_emirates')
                    ->where('emiratesID', $request->emirate_id)
                    ->where('isActive', 1)
                    ->first();

                if (!$emirate) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid emirate selection. Please refresh the page and try again.'
                    ], 422);
                }

                $emirateName = $emirate->emiratesName;
                // Kept in the same numeric-id-csv shape the admin approval
                // screen already parses from email_verified_at, so a single
                // chosen emirate still displays there exactly like before.
                $emiratesIdsCsv = (string) $emirate->emiratesID;
            }

            $emailVerifiedAtValue = $emiratesIdsCsv . 'rseparator0rseparator';

            $userData = [
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => $emailVerifiedAtValue,
                'company_name' => $request->company_name,
                'address' => $request->address,
                'trade_license_number' => $request->trade_license_number,
                'trade_license_expiry_date' => $request->trade_license_expiry_date,
                'emirate' => $emirateName,
                'country' => $isUae ? null : trim($request->country),
                'agent_services' => array_values($request->services),
            ];

            // MULTIPLE FILE UPLOAD
            $uploadedFilePaths = [];
            if ($request->hasFile('partner_documents')) {
                foreach ($request->file('partner_documents') as $file) {
                    $filename = 'partner_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                    $destinationPath = public_path('uploads/partners');
                    if (!File::exists($destinationPath)) {
                        File::makeDirectory($destinationPath, 0755, true);
                    }
                    $file->move($destinationPath, $filename);
                    $uploadedFilePaths[] = 'uploads/partners/' . $filename;
                }
            }

            // Save as JSON array (or string, but JSON preferred)
            $userData['partner_document_path'] = json_encode($uploadedFilePaths);

            $user = User::create($userData);

            return response()->json([
                'success' => true,
                'message' => 'Partner registration successful! Your account is pending admin approval.',
                'user_id' => $user->id,
                'location' => $isUae ? $emirateName : $userData['country'],
                'status' => 'Pending Approval',
                'note' => 'You will receive an email notification once your account is approved by the admin.'
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'debug_error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Admin method to approve/reject partner registration with admin comments
     */
    public function updatePartnerStatus(Request $request, $userId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:1,2',
                'admin_notes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $currentValue = $user->email_verified_at;
            $parts = $this->parseEmailVerifiedAt($currentValue);

            if (!$parts) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid partner data format'
                ], 400);
            }

            $newStatus = $request->status;
            $adminNotes = $request->admin_notes ? trim($request->admin_notes) : '';
            $newValue = $parts['emirates'] . 'rseparator' . $newStatus . 'rseparator' . $adminNotes;

            $updateData = [
                'email_verified_at' => $newValue,
                'updated_at' => now()
            ];

            // Approval connects this Partner Registration to the same
            // Agent/Manager system built for AgentApplication (rather than a
            // second, parallel one): granting the company_agent role and a
            // company_id is what makes ManagerAgentsController::index() list
            // it and User::hasService() honor the services chosen at
            // registration. Registrations happen with no tenant context (the
            // main gotrips.ai domain), so they land under the main "gotrips"
            // company — the same fallback IdentifyTenant itself uses for
            // that domain. Left alone on reject / re-processing.
            if ($newStatus == 1) {
                $mainCompany = Company::where('slug', 'gotrips')->first();
                if ($mainCompany) {
                    $updateData['role'] = 'company_agent';
                    $updateData['company_id'] = $mainCompany->id;
                    $updateData['is_active'] = true;
                }
            }

            User::where('id', $userId)->update($updateData);

            $statusText = $newStatus == 1 ? 'Approved' : 'Rejected';
            $emiratesNames = $this->getEmiratesFromValue($currentValue);

            return response()->json([
                'success' => true,
                'message' => "Partner account has been {$statusText}",
                'user_id' => $userId,
                'status' => $statusText,
                'emirates' => $emiratesNames,
                'admin_notes' => $adminNotes
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status',
                'debug_error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get pending partner approvals (for admin dashboard)
     */
    public function getPendingPartners(): JsonResponse
    {
        try {
            $pendingPartners = User::where('email_verified_at', 'like', '%rseparator0rseparator%')
                ->select('id', 'name', 'email', 'phone', 'email_verified_at', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($user) {
                    $details = $this->parseUserPartnerDetails($user->email_verified_at);
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'emirates' => $details['emirates_names'],
                        'emirates_count' => count($details['emirates_ids']),
                        'registered_at' => $user->created_at->format('M d, Y H:i'),
                        'status' => 'Pending'
                    ];
                });

            return response()->json([
                'success' => true,
                'pending_partners' => $pendingPartners,
                'count' => $pendingPartners->count()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load pending partners'
            ], 500);
        }
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        // Add your store logic here
        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        // Add your update logic here
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $userName = $user->name;
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', "User '{$userName}' has been deleted successfully");
        } catch (Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Failed to delete user');
        }
    }

    /**
     * Parse email_verified_at value into components
     * Format: emirates_ids + rseparator + status + rseparator + admin_comments
     */
    private function parseEmailVerifiedAt($emailVerifiedAt)
    {
        if (!$emailVerifiedAt || !str_contains($emailVerifiedAt, 'rseparator')) {
            return null;
        }
        $parts = explode('rseparator', $emailVerifiedAt, 3);
        return [
            'emirates' => $parts[0] ?? '',
            'status' => isset($parts[1]) ? (int)$parts[1] : 0,
            'admin_comments' => $parts[2] ?? ''
        ];
    }

    /**
     * Parse user partner details with emirates names
     */
    private function parseUserPartnerDetails($emailVerifiedAt)
    {
        $parts = $this->parseEmailVerifiedAt($emailVerifiedAt);
        if (!$parts) {
            return [
                'emirates_ids' => [],
                'emirates_names' => [],
                'status' => 0,
                'status_text' => 'Unknown',
                'admin_comments' => ''
            ];
        }
        $emiratesIds = array_filter(array_map('trim', explode(',', $parts['emirates'])));
        $emiratesNames = $this->getEmiratesNamesByIds($emiratesIds);
        $statusText = match($parts['status']) {
            1 => 'Approved',
            2 => 'Rejected',
            default => 'Pending'
        };
        return [
            'emirates_ids' => $emiratesIds,
            'emirates_names' => $emiratesNames,
            'status' => $parts['status'],
            'status_text' => $statusText,
            'admin_comments' => $parts['admin_comments']
        ];
    }

    /**
     * Helper method to get emirates names from email_verified_at value
     */
    private function getEmiratesFromValue($emailVerifiedAt)
    {
        $parts = $this->parseEmailVerifiedAt($emailVerifiedAt);
        if (!$parts) {
            return [];
        }
        $emiratesIds = array_filter(array_map('trim', explode(',', $parts['emirates'])));
        return $this->getEmiratesNamesByIds($emiratesIds);
    }

    /**
     * Get emirates names by IDs
     */
    private function getEmiratesNamesByIds($emiratesIds)
    {
        if (empty($emiratesIds)) {
            return [];
        }
        return DB::table('tbl_emirates')
            ->whereIn('emiratesID', $emiratesIds)
            ->where('isActive', 1)
            ->pluck('emiratesName')
            ->toArray();
    }

    /**
     * Helper method for backwards compatibility (renamed to avoid duplicate)
     */
    private function getUserEmirates($emailVerifiedAt)
    {
        return $this->getEmiratesFromValue($emailVerifiedAt);
    }
}
