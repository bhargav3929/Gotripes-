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
            'arrival_date' => 'required|date',
            'departure_date' => 'required|date|after_or_equal:arrival_date',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'passport_valid' => 'nullable|boolean',
            'not_stay_long' => 'nullable|boolean',
            'selected_emirate' => 'nullable|string|max:100',
            'visa_package_id' => 'nullable|integer',
            'entry_type' => 'nullable|string|max:100',

            // Array Validation
            'passport_copy' => 'required|array',
            'passport_copy.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'passport_photo' => 'required|array',
            'passport_photo.*' => 'required|image|max:4096',
            'supporting_document' => 'nullable|array',
            'supporting_document.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
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

            // DB Record
            $dbData = [
                'UAEV_nationality' => $validated['nationality'] ?? null,
                'UAEV_residence' => $validated['residence'] ?? null,
                'UAEV_first_name' => $applicantLabel,
                'UAEV_last_name' => $isChild ? 'Child' : 'Guest',
                'UAEV_passport_valid' => $validated['passport_valid'] ?? null,
                'UAEV_not_stay_long' => $validated['not_stay_long'] ?? null,
                'UAEV_arrival_date' => $validated['arrival_date'],
                'UAEV_departure_date' => $validated['departure_date'],
                'UAEV_phone' => $validated['phone'],
                'UAEV_email' => $validated['email'],
                'UAEV_passport_copy' => $passportCopyPath,
                'UAEV_passport_photo' => $passportPhotoPath,
                'UAEV_visaDuration' => $validated['visaDuration'],
                'UAEV_price' => $unitPrice,
                'UAEV_Created_by' => 'Guest (Multi-Visa)',
                'UAEV_created_date' => now(),
                'UAEV_isActive' => 1,
                'UAEV_status' => 1,

                // Checkout fields
                'UAEV_emirate' => $emirateName,
                'UAEV_package_name' => $packageName,
                'UAEV_visa_type' => $request->input('entry_type'),
                'UAEV_traveller_type' => $travellerType,
                'UAEV_addons' => json_encode(array_filter([
                    $request->boolean('hotel_booking') ? 'hotel' : null,
                    $request->boolean('ticket_booking') ? 'flight' : null,
                ])),
            ];

            $uaev = UAEVApplication::create($dbData);
            if (!$firstId)
                $firstId = $uaev->id;
            $createdRecords[] = $uaev;
        }

        // Email notifications loop
        foreach ($createdRecords as $rec) {
            try {
                Mail::to($rec->UAEV_email)->send(new UAEVVisaMail($rec->toArray(), $rec->UAEV_passport_copy, $rec->UAEV_passport_photo));
            } catch (\Exception $e) {
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

        // Nomod Payment
        $totalAmount = round($baseVisaTotal + $ticketCost + $hotelCost, 2);
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
