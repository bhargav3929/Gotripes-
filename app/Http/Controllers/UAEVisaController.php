<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UAEVApplication;
use App\Models\UAEVisaMaster;
use App\Mail\UAEVVisaMail;
use Illuminate\Support\Facades\Mail;
// use Illuminate\Support\Facades\Log;

require_once app_path('Helpers/Crypto.php');

class UAEVisaController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'nationality'        => 'required|string|max:100',
            'residence'          => 'required|string|max:100',
            'first_name'         => 'required|string|max:100',
            'last_name'          => 'required|string|max:100',
            'passport_valid'     => 'nullable|boolean',
            'not_stay_long'      => 'nullable|boolean',
            'gender'             => 'required|in:Male,Female',
            'dob'                => 'required|date',
            'arrival_date'       => 'required|date',
            'departure_date'     => 'required|date|after_or_equal:arrival_date',
            'phone'              => 'required|string|max:20',
            'email'              => 'required|email',
            'profession'         => 'required|string|max:100',
            'marital_status'     => 'required|in:Single,Married',
            'passport_copy'      => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'passport_photo'     => 'required|image|max:4096',
            'visaDuration'       => 'required',
            'price'              => 'required',
        ]);

        // Cross-check price with master
        $master = UAEVisaMaster::where('UAEVisaDuration', $validated['visaDuration'])
            ->where('isActive', true)
            ->first();

        if (!$master || $validated['price'] != $master->UAEVPrice) {
            return redirect()->back()->with('error', 'Visa price mismatch. Please retry.');
        }

        $passportCopyPath = $request->hasFile('passport_copy')
            ? $request->file('passport_copy')->store('visas/passport_copies', 'public')
            : null;

        $passportPhotoPath = $request->hasFile('passport_photo')
            ? $request->file('passport_photo')->store('visas/passport_photos', 'public')
            : null;

        $createdBy = trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));

        $dbData = [
            'UAEV_nationality'     => $validated['nationality'] ?? null,
            'UAEV_residence'       => $validated['residence'] ?? null,
            'UAEV_first_name'      => $validated['first_name'],
            'UAEV_last_name'       => $validated['last_name'],
            'UAEV_passport_valid'  => $validated['passport_valid'] ?? null,
            'UAEV_not_stay_long'   => $validated['not_stay_long'] ?? null,
            'UAEV_gender'          => $validated['gender'] ?? null,
            'UAEV_dob'             => $validated['dob'] ?? null,
            'UAEV_arrival_date'    => $validated['arrival_date'] ?? null,
            'UAEV_departure_date'  => $validated['departure_date'] ?? null,
            'UAEV_phone'           => $validated['phone'] ?? null,
            'UAEV_email'           => $validated['email'],
            'UAEV_profession'      => $validated['profession'] ?? null,
            'UAEV_marital_status'  => $validated['marital_status'] ?? null,
            'UAEV_passport_copy'   => $passportCopyPath,
            'UAEV_passport_photo'  => $passportPhotoPath,
            'UAEV_visaDuration'    => $validated['visaDuration'] ?? null,
            'UAEV_price'           => $validated['price'] ?? null,
            'UAEV_Created_by'      => $createdBy,
            'UAEV_created_date'    => now(),
            'UAEV_isActive'        => 1,
            'UAEV_status'          => 1,
        ];

        // Insert to DB
        $uaev = UAEVApplication::create($dbData);

        // Prepare data for mail
        $data = $dbData;
        $data['id'] = $uaev->id;

        $recipients = [
            config('mail.from.address'),
            $data['UAEV_email']
        ];

        // Send mail, log success/fail
        $mailSent = false;
        try {
            Mail::to($recipients)->send(new UAEVVisaMail($data, $passportCopyPath, $passportPhotoPath));
            // Log::info('UAEV visa mail sent successfully', [
            //     'to' => $recipients,
            //     'from' => $data['UAEV_email'],
            //     'application_id' => $uaev->id,
            //     'timestamp' => now()->toDateTimeString(),
            //     'data' => $data,
            // ]);
            $mailSent = true;
        } catch (\Exception $e) {
            // Log::error('Failed to send UAEV visa mail', [
            //     'to' => $recipients,
            //     'from' => $data['UAEV_email'],
            //     'error' => $e->getMessage(),
            //     'application_id' => $uaev->id,
            //     'timestamp' => now()->toDateTimeString(),
            //     'data' => $data,
            // ]);
        }

        // --- CCAvenue payment integration below ---

        // Build order id with visa application id
        $orderId = 'ORDUAEV' . $uaev->id;

        $paymentData = [
            'merchant_id'   => config('services.ccavenue.merchant_id'),
            'order_id'      => $orderId,
            'currency'      => 'AED',
            'amount'        => number_format($validated['price'], 2, '.', ''),
            'redirect_url'  => config('services.ccavenue.redirect_url'),
            'cancel_url'    => config('services.ccavenue.cancel_url'),
            'language'      => 'EN',
        ];

        $paramString = http_build_query($paymentData);

        // Encrypt with CCAvenue's algorithm/working key
        $encryptedData = ccavenue_encrypt($paramString, config('services.ccavenue.working_key'));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mailSent
                    ? 'Your visa application has been submitted, saved, and emailed.'
                    : 'Your visa application has been submitted and saved, but email sending failed.',
                'price' => $validated['price'] ?? 0,
                'encryptedData' => $encryptedData,
                'accessCode' => config('services.ccavenue.access_code'),
                'orderId' => $orderId,
            ]);
        }

        return redirect()->back()->with([
            'success' => $mailSent
                ? 'Your visa application has been submitted, saved, and emailed.'
                : 'Your visa application has been submitted and saved, but email sending failed.',
            'price' => $validated['price'] ?? 0,
        ]);
    }
}
