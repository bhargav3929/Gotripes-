<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Loantype;
use App\Models\Referee;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\Referrer;
use Illuminate\Support\Facades\Http;

class ReferrerController extends Controller 
{


  //  protected static $logID='ok';
public function ReferrerSetup() 
{
    $referrers = Referrer::all();
     $loantypes=Loantype::all();
     
    return view('inner-page', compact(['referrers','loantypes']));
}


 public function sendOTP(Request $request)
{
    $mobile = $request->referrerMobile;
    $authkey = 'f164ebff9b8582b5';
    $SID = '9855';
    $companyName = 'ThanQFin';
    
    // First, validate the mobile number input
    if (empty($mobile)) {
        return response()->json(['message' => 'Mobile number is required.', 'status' => 'error']);
    }

    // Send the OTP request
    $response = Http::get("https://api.authkey.io/request", [
        'authkey' => $authkey,
        'mobile' => $mobile,
        'country_code' => '91',
        'sid' => $SID,
        'Visitor' => $companyName
    ]);

    // Decode the response
   
    // Check if the HTTP request was successful
    if ($response->successful()) {

         $responseData = $response->json();

          // Extract the logID
           
         //-------self::$logID = $responseData['LogID']  ?? 'No logID found';
// Store logID in session
        $request->session()->put('logID', $responseData['LogID'] ?? 'No logID found');
       
        // Try to find an existing referrer
        $referrer = Referrer::where('referrerMobile', $mobile)->orderBy('created_at', 'desc')->first();
        
        if ($referrer) {
            // Update the existing referrer
            $referrer->update([
                'referrerMobile' => $mobile,
            ]);
        } else {
            // Create a new referrer entry
            Referrer::create([
                'referrerMobile' => $mobile,
            ]);
        }

        return response()->json(['message' => 'OTP sent successfully!' , 'status' => 'success']);
    } else {
        // Handle failure scenario
        return response()->json(['message' => 'Failed to send OTP.', 'status' => 'error']);
    }
}

public function verifyOTP(Request $request)
{
    // Retrieve logID from session
    $logID = $request->session()->get('logID', 'ok');  // Default to 'ok' if not set

    
    $otp = $request->otp;
    $authkey = 'f164ebff9b8582b5';  
    $channel='sms';
    $response = Http::get("https://authkey.io/api/2fa_verify.php", [
        'authkey' => $authkey,
        'channel'=> $channel,
        'otp' => $otp, // The OTP to be verified
        'logid'=> $logID 
       
        
    ]);

    if ($response->successful() && $response->json()['status'] == true) {
        // Assuming the API returns a 'status' in the JSON response to indicate success
        return response()->json([
            'message' => 'OTP verified successfully!' ,
            'status' => 'success'
        ]);
    } else {
        return response()->json([
            'message' => 'OTP verification failed. Please try again.',
            'status' => 'error'
        ]);
    }
}
public function updateReferrerDetails(Request $request)
{
    $request->validate([
        'referrerMobile' => 'required|digits:10',
        'referrerName' => 'required|string|max:255',
        'refereeMobile' => 'required|digits:10',
        'refereeName' => 'required|string|max:255',
        'tolID' => 'required|exists:loantypes,id' // Make sure the tolID exists in loantypes table
        
    ]);

    try {
        // Fetch the latest referrer by mobile and update
        $referrer = Referrer::where('referrerMobile', $request->referrerMobile)
                            ->orderBy('created_at', 'desc')->first();

        if (!$referrer) {
            return response()->json(['message' => 'Referrer not found.', 'status' => 'error']);
        }

        $referrer->update([
            'referrerName' => $request->referrerName,
            'referrerEmail' => $request->referrerEmail,
        ]);
$latestReferrer = Referrer::latest('updated_at')->first();


        // Create referee details
        Referee::create([
            'refereeMobile' => $request->refereeMobile,
            'refereeName'=>$request->refereeName,
            'refereeEmail' => $request->refereeEmail,
            'refereeAddress' => $request->refereeAddress,
            'tolID' => $request->tolID, 
            'account' => $request->account,
            'bankName' => $request->bankName, 
            'bankCode' => $request->bankCode,
            'referrerID' =>$latestReferrer->id,  
             
        ]);
        
   
        return response()->json(['message' => 'Referrer and Referee details saved successfully', 'status' => 'success']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to update details: ' . $e->getMessage(), 'status' => 'error']);
    }
}

}