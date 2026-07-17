<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UAEVApplication;
use App\Models\UAEVisaMaster;
use App\Models\NomodTransaction;
use App\Mail\UAEVVisaMail;
use App\Services\NomodService;
use App\Models\UAEVisaPackage;
use App\Models\UAEVisaPrice;
use Illuminate\Support\Facades\Mail;

class UAEVisaController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'nationality' => 'nullable|string|max:100',
            'residence' => 'nullable|string|max:100',
            'visaDuration' => 'required',
            'price' => 'required',
            'visa_count' => 'required|integer|min:1|max:10',
            'children_count' => 'nullable|integer|min:0|max:10',
            'infants_count' => 'nullable|integer|min:0|max:5',
            'hotel_booking' => 'nullable|boolean',
            'ticket_booking' => 'nullable|boolean',
            'arrival_date' => 'nullable|date',
            'departure_date' => 'nullable|date|after_or_equal:arrival_date',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'passport_valid' => 'nullable|boolean',
            'not_stay_long' => 'nullable|boolean',
            'selected_emirate' => 'nullable|string|max:100',
            'visa_package_id' => 'nullable|integer',
            'entry_type' => 'nullable|string|max:100',
            'applicant_name' => 'nullable|string|max:200',

            // Array Validation
            'passport_copy' => 'required|array',
            'passport_copy.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'passport_photo' => 'required|array',
            'passport_photo.*' => 'required|image|max:4096',
            'supporting_document' => 'nullable|array',
            'supporting_document.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',

            // Sharjah visa OCR and bank details validation
            'first_name' => 'nullable|array',
            'first_name.*' => 'nullable|string|max:100',
            'last_name' => 'nullable|array',
            'last_name.*' => 'nullable|string|max:100',
            'passport_number' => 'nullable|array',
            'passport_number.*' => 'nullable|string|max:100',
            'dob' => 'nullable|array',
            'dob.*' => 'nullable|date',
            'gender' => 'nullable|array',
            'gender.*' => 'nullable|string|max:20',

            'bank_account_holder' => 'nullable|string|max:200',
            'bank_name' => 'nullable|string|max:200',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_swift_code' => 'nullable|string|max:50',
        ]);

        $adultCount = (int) $validated['visa_count'];
        $childrenCount = (int) ($validated['children_count'] ?? 0);
        $visaCount = $adultCount + $childrenCount;

        // Autoritative price lookups (Dynamic Package vs Legacy Fallback)
        $packageId = $request->input('visa_package_id');
        $adultPrice = 0.0;
        $childPrice = 0.0;
        $infantPrice = 0.0;
        $packageName = null;
        $emirateName = $request->input('selected_emirate');

        if ($packageId) {
            $package = UAEVisaPackage::findOrFail($packageId);
            $packageName = $package->name;
            
            $pricingRows = UAEVisaPrice::where('visa_package_id', $package->id)
                ->where('entry_type', $request->input('entry_type'))
                ->where('duration', $request->input('visaDuration'))
                ->where('isActive', true)
                ->get();

            if ($pricingRows->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Invalid Visa Pricing Configuration'], 400);
            }

            // Filter by nationality first, fallback to null (default)
            $nationality = $validated['nationality'] ?? null;
            $natRows = $pricingRows->filter(fn($p) => !empty($p->nationality) && strtolower($p->nationality) === strtolower($nationality));
            
            if ($natRows->isNotEmpty()) {
                $pricingRows = $natRows;
            } else {
                $pricingRows = $pricingRows->filter(fn($p) => empty($p->nationality));
            }

            $adultRow  = $pricingRows->firstWhere('traveller_type', 'Adult');
            $childRow  = $pricingRows->firstWhere('traveller_type', 'Child');
            $infantRow = $pricingRows->firstWhere('traveller_type', 'Infant');

            $adultPrice  = $adultRow ? (float) $adultRow->price : 0.0;
            $childPrice  = $childRow ? (float) $childRow->price : $adultPrice;
            $infantPrice = $infantRow ? (float) $infantRow->price : 0.0;
        } else {
            // Legacy flat duration pricing row lookup
            $master = UAEVisaMaster::where('UAEVisaDuration', $validated['visaDuration'])
                ->where('isActive', true)
                ->first();

            if (!$master) {
                return response()->json(['success' => false, 'message' => 'Invalid Visa Type'], 400);
            }
            $adultPrice = (float) $master->UAEVPrice;
            $childPrice = $adultPrice;
            $infantPrice = $adultPrice;
        }

        // Loop and Create Records
        $firstId = null;
        $createdRecords = [];

        // Determine if Sharjah Visa processing is selected
        $isSharjah = strtolower($emirateName) === 'sharjah';
        $depositAmount = $isSharjah ? 5000.00 : 0.00;
        $refundAmount = $isSharjah ? 5000.00 : 0.00;

        // Resolve every traveller's name up front.
        //
        // Sharjah's simplified form hides the per-applicant name fields and relies
        // on passport OCR (plus one universal "Full Name" for the primary
        // applicant) to supply them. If OCR fails and a name is left blank we must
        // NOT persist a placeholder like "Applicant 2"/"Guest" — reject instead so
        // the customer can enter it. Other emirates keep their long-standing
        // behaviour unchanged (names are required in their form; the historical
        // fallback is preserved so nothing about Dubai/etc. is affected).
        $resolvedNames = [];
        $missingNames = [];
        for ($i = 0; $i < $visaCount; $i++) {
            $isChildRow = $i >= $adultCount;
            $label = $isChildRow ? ('Child ' . ($i - $adultCount + 1)) : ('Applicant ' . ($i + 1));

            $first = trim((string) $request->input("first_name.$i"));
            $last  = trim((string) $request->input("last_name.$i"));

            if ($isSharjah) {
                if ($i === 0 && $first === '' && $last === '' && !empty($request->input('applicant_name'))) {
                    $parts = preg_split('/\s+/', trim($request->input('applicant_name')), 2);
                    $first = $parts[0] ?? '';
                    $last  = $parts[1] ?? '';
                }

                if ($first === '') {
                    $missingNames[] = $label;
                } elseif ($last === '') {
                    // A single-word legal name is valid; mirror it rather than fabricate a surname.
                    $last = $first;
                }
            } else {
                // Historical fallback — unchanged behaviour for non-Sharjah emirates.
                $first = $first !== '' ? $first : $label;
                $last  = $last !== '' ? $last : ($isChildRow ? 'Child' : 'Guest');
            }

            $resolvedNames[$i] = ['first' => $first, 'last' => $last];
        }

        if ($isSharjah && !empty($missingNames)) {
            return response()->json([
                'success' => false,
                'message' => 'We could not read the passport name for: ' . implode(', ', $missingNames)
                    . '. Please enter the traveller name to continue.',
                'missing_names' => $missingNames,
            ], 422);
        }

        for ($i = 0; $i < $visaCount; $i++) {
            // Handle Files
            $passportCopyPath = null;
            if ($request->hasFile("passport_copy.$i")) {
                $passportCopyPath = $request->file("passport_copy.$i")->store('visas/passport_copies', 'public');
            }

            $passportPhotoPath = null;
            if ($request->hasFile("passport_photo.$i")) {
                $passportPhotoPath = $request->file("passport_photo.$i")->store('visas/passport_photos', 'public');
            }

            $supportingDocPath = null;
            if ($request->hasFile("supporting_document.$i")) {
                $supportingDocPath = $request->file("supporting_document.$i")->store('visas/supporting_docs', 'public');
            }

            $isChild = $i >= $adultCount;
            $childNum = $i - $adultCount + 1;
            $applicantLabel = $isChild
                ? 'Child ' . $childNum
                : 'Applicant ' . ($i + 1);

            $travellerType = $isChild ? 'Child' : 'Adult';
            $unitPrice = $isChild ? $childPrice : $adultPrice;

            // Names were resolved and validated before this loop.
            $firstName = $resolvedNames[$i]['first'];
            $lastName  = $resolvedNames[$i]['last'];
            $passportNumber = $request->input("passport_number.$i");
            $dob = $request->input("dob.$i");
            $gender = $request->input("gender.$i");

            // DB Record
            $dbData = [
                'company_id' => current_company_id(),
                'UAEV_nationality' => $validated['nationality'] ?? null,
                'UAEV_residence' => $validated['residence'] ?? null,
                'UAEV_emirate' => $emirateName,
                'UAEV_package_name' => $packageName,
                'UAEV_visa_type' => $request->input('entry_type'),
                'UAEV_traveller_type' => $travellerType,
                'UAEV_first_name' => $firstName,
                'UAEV_last_name' => $lastName,
                'UAEV_passport_number' => $passportNumber,
                'UAEV_passport_valid' => $validated['passport_valid'] ?? null,
                'UAEV_not_stay_long' => $validated['not_stay_long'] ?? null,
                'UAEV_gender' => $gender,
                'UAEV_dob' => $dob,
                'UAEV_arrival_date' => $validated['arrival_date'] ?? null,
                'UAEV_departure_date' => $validated['departure_date'] ?? null,
                'UAEV_phone' => $validated['phone'],
                'UAEV_email' => $validated['email'],
                'UAEV_passport_copy' => $passportCopyPath,
                'UAEV_passport_photo' => $passportPhotoPath,
                'UAEV_addons' => json_encode(array_filter([
                    $request->boolean('hotel_booking') ? 'hotel' : null,
                    $request->boolean('ticket_booking') ? 'flight' : null,
                ])),
                'UAEV_visaDuration' => $validated['visaDuration'],
                'UAEV_price' => $unitPrice,
                'UAEV_deposit_amount' => $depositAmount,
                'UAEV_refund_amount' => $refundAmount,
                'UAEV_bank_account_holder' => $isSharjah ? $validated['bank_account_holder'] : null,
                'UAEV_bank_name' => $isSharjah ? $validated['bank_name'] : null,
                'UAEV_bank_account_number' => $isSharjah ? $validated['bank_account_number'] : null,
                'UAEV_bank_swift_code' => $isSharjah ? $validated['bank_swift_code'] : null,
                'UAEV_Created_by' => 'Guest (Multi-Visa)',
                'UAEV_created_date' => now(),
                'UAEV_isActive' => 1,
                'UAEV_status' => 1,
            ];

            $uaev = UAEVApplication::create($dbData);
            if (!$firstId)
                $firstId = $uaev->id;
            $createdRecords[] = $uaev;
        }

        // Email notifications loop
        $company = current_company();
        $supplierEmail = $company?->getSetting('visa_supplier_email');

        foreach ($createdRecords as $rec) {
            try {
                Mail::to($rec->UAEV_email)->send(new UAEVVisaMail($rec->toArray(), $rec->UAEV_passport_copy, $rec->UAEV_passport_photo));
            } catch (\Exception $e) {
            }

            if ($supplierEmail) {
                try {
                    Mail::to($supplierEmail)->send(new UAEVVisaMail($rec->toArray(), $rec->UAEV_passport_copy, $rec->UAEV_passport_photo));
                } catch (\Exception $e) {
                }
            }
        }

        // Authoritative pricing totals recalculation
        $infantsCount = (int) $request->input('infants_count', 0);
        $persons = $adultCount + $childrenCount + $infantsCount;

        $company    = current_company();
        $ticketRate = (float) ($company?->getSetting('visa_ticket_booking_fee', 25) ?? 25);
        $hotelBase  = (float) ($company?->getSetting('visa_hotel_booking_fee', 25) ?? 25);

        $baseVisaTotal = ($adultPrice * $adultCount) + ($childPrice * $childrenCount) + ($infantPrice * $infantsCount);
        $ticketCost = $request->boolean('ticket_booking') ? $ticketRate * $persons : 0.0;
        $hotelCost  = $request->boolean('hotel_booking')  ? $this->hotelFeeForVisas($persons, $hotelBase) : 0.0;
        $depositTotal = $isSharjah ? (5000.00 * $persons) : 0.0;

        // Nomod Payment
        $totalAmount = round($baseVisaTotal + $ticketCost + $hotelCost + $depositTotal, 2);
        $orderId = 'ORDUAEV-GRP-' . $firstId . '-' . time();

        $nomodService = new NomodService();
        $checkout = $nomodService->createCheckout([
            'amount' => $totalAmount,
            'currency' => 'AED',
            'order_id' => $orderId,
            'description' => 'UAE Visa Application - ' . $validated['visaDuration'],
            'customer' => [
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ],
        ]);

        if (!$checkout['success']) {
            return response()->json([
                'success' => false,
                'message' => $checkout['error'] ?? 'Payment initiation failed.',
            ], 500);
        }

        NomodTransaction::create([
            'checkout_id' => $checkout['checkout_id'],
            'order_id' => $orderId,
            'status' => 'created',
            'amount' => $totalAmount,
            'currency' => 'AED',
            'booking_type' => 'visa',
            'checkout_url' => $checkout['checkout_url'],
            'customer' => ['email' => $validated['email'], 'phone' => $validated['phone']],
            'response_data' => $checkout['data'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Applications submitted successfully.',
            'checkout_url' => $checkout['checkout_url'],
            'orderId' => $orderId,
        ]);
    }

    /**
     * Hotel-booking add-on fee, stepping up with the number of visas (applicants):
     *   1–2 visas → base (default 25), 3–4 → 50, 5–6 → 60,
     *   then +10 AED for every additional pair of visas (7–8 → 70, 9–10 → 80, …).
     */
    private function hotelFeeForVisas(int $visas, float $base = 25.0): float
    {
        if ($visas <= 0) {
            return 0.0;
        }
        $tier = (int) ceil($visas / 2);
        if ($tier <= 1) {
            return $base;            // 1–2 visas
        }
        if ($tier === 2) {
            return 50.0;             // 3–4 visas
        }
        return 60.0 + ($tier - 3) * 10.0; // 5–6 → 60, 7–8 → 70, …
    }
}
