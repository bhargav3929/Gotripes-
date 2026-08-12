<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UAEVApplication;
use App\Models\UAEVisaMaster;
use App\Models\NomodTransaction;
use App\Jobs\BackfillPassportDetails;
use App\Mail\UAEVVisaMail;
use App\Services\NomodService;
use App\Services\PassportOcrService;
use App\Models\UAEVisaPackage;
use App\Models\UAEVisaPrice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class UAEVisaController extends Controller
{
    public function submit(Request $request)
    {
        // Sharjah alone charges a refundable deposit, so bank details are only
        // mandatory there — matches the JS toggle in uaevisa.blade.php, but
        // enforced server-side too since the client-side `required` attribute
        // can be bypassed by posting directly to this endpoint.
        $isSharjahRequest = strtolower((string) $request->input('selected_emirate')) === 'sharjah';

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

            // Passport details are no longer typed by the customer. They arrive
            // from the browser passport scan (hidden fields) and, for anything it
            // could not read, from BackfillPassportDetails after the response.
            // Every rule here is therefore optional — a booking must never be
            // rejected because a passport could not be read.
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

            'bank_account_holder' => [Rule::requiredIf($isSharjahRequest), 'nullable', 'string', 'max:200'],
            'bank_name' => [Rule::requiredIf($isSharjahRequest), 'nullable', 'string', 'max:200'],
            'bank_account_number' => [Rule::requiredIf($isSharjahRequest), 'nullable', 'string', 'max:100'],
            'bank_swift_code' => 'nullable|string|max:50',
        ], [
            'bank_account_holder.required' => 'Bank account holder name is required for Sharjah visas so the security deposit can be refunded.',
            'bank_name.required' => 'Bank name is required for Sharjah visas so the security deposit can be refunded.',
            'bank_account_number.required' => 'Bank account number / IBAN is required for Sharjah visas so the security deposit can be refunded.',
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
        // Hoisted so the deposit and the notification fan-out below can read the
        // package's own supplier/company addresses and deposit amount. Stays null
        // on the legacy flat-price path, where the company settings still apply.
        $package = null;

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

        // Refundable deposit per applicant. Read from the selected package when
        // there is one — a package can set its own amount, or 0 for no deposit —
        // and otherwise from the company-wide setting, which is what the legacy
        // flat-price path has always used. Defaults to 0.
        if ($package) {
            $sharjahDeposit  = $package->depositPerApplicant();
            // Never exceeds the deposit; the model already clamps it.
            $sharjahAdminFee = $package->depositAdminFee();
        } else {
            $sharjahDeposit = current_company()?->getSetting('visa_sharjah_deposit', 0);
            $sharjahDeposit = is_numeric($sharjahDeposit) ? (float) $sharjahDeposit : 0.0;
            // Non-refundable admin/processing fee deducted from the deposit at
            // refund time. Clamped to the deposit so the refundable amount can
            // never go negative even if the setting is inconsistent.
            $sharjahAdminFee = current_company()?->getSetting('visa_sharjah_deposit_admin_fee', 0);
            $sharjahAdminFee = is_numeric($sharjahAdminFee) ? (float) $sharjahAdminFee : 0.0;
            $sharjahAdminFee = max(0.0, min($sharjahAdminFee, $sharjahDeposit));

            // No package selected: the legacy flat-price path only ever charged a
            // deposit for Sharjah, so keep that behaviour for it.
            if (strtolower((string) $emirateName) !== 'sharjah') {
                $sharjahDeposit = 0.0;
                $sharjahAdminFee = 0.0;
            }
        }

        // "Takes a deposit" is now a property of the package, not of the emirate's
        // name. The client asked to be able to introduce a deposit for another
        // emirate without a code change, and matching on the string 'sharjah'
        // was the one thing preventing that. It also kept the two halves of the
        // system in sync: the storefront quotes this same amount off the package,
        // so gating the charge on the emirate would have meant quoting one figure
        // and charging another.
        $isSharjah = $sharjahDeposit > 0;

        // Whether to persist the refund bank account. Broader than $isSharjah on
        // purpose: Sharjah applications are required to supply these details even
        // before a deposit amount has been configured, and details the customer
        // was made to type must never be silently dropped.
        $keepBankDetails = $isSharjah || $isSharjahRequest;

        $depositAmount = $sharjahDeposit;
        // The customer is charged the full deposit; what comes back later is the
        // deposit minus the admin fee.
        $refundAmount = round($sharjahDeposit - $sharjahAdminFee, 2);

        // Resolve every traveller's name up front.
        //
        // The form no longer asks customers to type passport details for any
        // emirate: names arrive from the browser passport scan in hidden fields,
        // and the one name still typed is the lead traveller's, which stands in
        // for applicant #1. Anything the scan could not read is stored blank and
        // filled in by BackfillPassportDetails once the response has been sent —
        // never with a fabricated placeholder like "Applicant 2"/"Guest", which
        // is indistinguishable from a real name in the operations portal.
        [$leadFirst, $leadLast] = PassportOcrService::splitFullName(
            (string) ($validated['applicant_name'] ?? '')
        );

        $resolvedNames = [];
        for ($i = 0; $i < $visaCount; $i++) {
            $first = trim((string) $request->input("first_name.$i"));
            $last  = trim((string) $request->input("last_name.$i"));

            if ($i === 0 && $first === '' && $last === '') {
                $first = $leadFirst;
                $last  = $leadLast;
            }

            if ($first !== '' && $last === '') {
                // A single-word legal name is valid; mirror it rather than fabricate a surname.
                $last = $first;
            }

            $resolvedNames[$i] = ['first' => $first, 'last' => $last];
        }

        // The lead traveller is the one person we must be able to name — the
        // booking confirmation and the payment record are addressed to them.
        if ($resolvedNames[0]['first'] === '') {
            return response()->json([
                'success' => false,
                'message' => "Please enter the lead traveller's full name to continue.",
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
                'UAEV_refund_status' => $refundAmount > 0 ? 'pending' : null,
                // Refund account. Kept whenever the application either carries a
                // deposit or is a Sharjah one — the validation above already makes
                // these mandatory for every Sharjah post regardless of the deposit
                // amount, so gating the save on $isSharjah (deposit > 0) threw away
                // details the customer was forced to type whenever the deposit was
                // still unconfigured. All four are nullable in the rules above, so
                // they are coalesced rather than indexed: an application submitted
                // without them must still save, not 500.
                'UAEV_bank_account_holder' => $keepBankDetails ? ($validated['bank_account_holder'] ?? null) : null,
                'UAEV_bank_name' => $keepBankDetails ? ($validated['bank_name'] ?? null) : null,
                'UAEV_bank_account_number' => $keepBankDetails ? ($validated['bank_account_number'] ?? null) : null,
                'UAEV_bank_swift_code' => $keepBankDetails ? ($validated['bank_swift_code'] ?? null) : null,
                'UAEV_Created_by' => 'Guest (Multi-Visa)',
                'UAEV_created_date' => now(),
                'UAEV_isActive' => 1,
                'UAEV_status' => 1,
            ];

            $uaev = UAEVApplication::create($dbData);
            if (!$firstId)
                $firstId = $uaev->id;
            $createdRecords[] = $uaev;

            // Read anything the browser scan missed off the uploaded copy. Runs
            // after the response so the customer reaches checkout immediately.
            if ($passportCopyPath) {
                BackfillPassportDetails::dispatch(
                    UAEVApplication::class,
                    $uaev->id,
                    $passportCopyPath,
                    [
                        'first_name'      => 'UAEV_first_name',
                        'last_name'       => 'UAEV_last_name',
                        'passport_number' => 'UAEV_passport_number',
                        'dob'             => 'UAEV_dob',
                        'gender'          => 'UAEV_gender',
                    ],
                )->afterResponse();
            }
        }

        // Email notifications loop.
        //
        // Three parties are told about every application: the customer, the
        // supplier who fulfils it, and whoever on our side owns this visa type.
        // The addresses come off the selected package first so different visa
        // types can go to different suppliers and different staff, and fall back
        // to the company-wide settings for the legacy flat-price path.
        $company = current_company();

        if ($package) {
            $supplierEmails = $package->supplierEmails();
            $companyEmails  = $package->companyEmails();
        } else {
            $supplierEmails = parse_emails($company?->getSetting('visa_supplier_email'));
            $companyEmails  = parse_emails($company?->getSetting('visa_company_email') ?: $company?->email);
        }

        // A supplier who is also listed as our own address should not get two
        // identical copies of the same application.
        $companyEmails = array_values(array_diff($companyEmails, $supplierEmails));

        foreach ($createdRecords as $rec) {
            $mailFor = fn() => new UAEVVisaMail($rec->toArray(), $rec->UAEV_passport_copy, $rec->UAEV_passport_photo);

            // Never let a mail failure break the booking — but never swallow it
            // silently either. These were previously empty catch blocks, which is
            // why undelivered confirmations went unnoticed.
            try {
                Mail::to($rec->UAEV_email)->send($mailFor());
            } catch (\Throwable $e) {
                Log::error('UAE visa customer email failed', [
                    'application_id' => $rec->id,
                    'to'             => $rec->UAEV_email,
                    'error'          => $e->getMessage(),
                ]);
            }

            foreach ([['supplier', $supplierEmails], ['company', $companyEmails]] as [$role, $recipients]) {
                foreach ($recipients as $recipient) {
                    try {
                        Mail::to($recipient)->send($mailFor());
                    } catch (\Throwable $e) {
                        Log::error("UAE visa {$role} email failed", [
                            'application_id' => $rec->id,
                            'to'             => $recipient,
                            'error'          => $e->getMessage(),
                        ]);
                    }
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
        $depositTotal = $isSharjah ? ($sharjahDeposit * $persons) : 0.0;

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
