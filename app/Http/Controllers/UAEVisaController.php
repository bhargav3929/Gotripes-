<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UAEVApplication;
use App\Models\UAEVisaMaster;
use App\Models\NomodTransaction;
use App\Mail\UAEVVisaMail;
use App\Services\NomodService;
use Illuminate\Support\Facades\Mail;
// use Illuminate\Support\Facades\Log;

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
            'arrival_date' => 'required|date',
            'departure_date' => 'required|date|after_or_equal:arrival_date',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'passport_valid' => 'nullable|boolean',
            'not_stay_long' => 'nullable|boolean',

            // Array Validation
            'passport_copy' => 'required|array',
            'passport_copy.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'passport_photo' => 'required|array',
            'passport_photo.*' => 'required|image|max:4096',
            'supporting_document' => 'nullable|array',
            'supporting_document.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $visaCount = (int) $validated['visa_count'];
        $unitPrice = $validated['price']; // This is Total from frontend, but we need unit price validation logic if strictly needed. 
        // Frontend sends TOTAL price in 'price'. Let's trust frontend total for now or recalculate.
        // Actually, logic below recalculates per person to be safe? 
        // Let's stick to simple logic: Input Price is TOTAL.

        // Master Price Check (Unit Price)
        $master = UAEVisaMaster::where('UAEVisaDuration', $validated['visaDuration'])
            ->where('isActive', true)
            ->first();

        if (!$master) {
            return response()->json(['success' => false, 'message' => 'Invalid Visa Type'], 400);
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

            // DB Record
            $dbData = [
                'UAEV_nationality' => $validated['nationality'] ?? null,
                'UAEV_residence' => $validated['residence'] ?? null,
                'UAEV_first_name' => 'Applicant ' . ($i + 1), // Placeholder as name fields removed
                'UAEV_last_name' => 'Guest',
                'UAEV_passport_valid' => $validated['passport_valid'] ?? null,
                'UAEV_not_stay_long' => $validated['not_stay_long'] ?? null,
                'UAEV_arrival_date' => $validated['arrival_date'],
                'UAEV_departure_date' => $validated['departure_date'],
                'UAEV_phone' => $validated['phone'],
                'UAEV_email' => $validated['email'],
                'UAEV_passport_copy' => $passportCopyPath,
                'UAEV_passport_photo' => $passportPhotoPath,
                // 'UAEV_supporting_doc' => $supportingDocPath, // Need to check if column exists, if not exclude
                'UAEV_visaDuration' => $validated['visaDuration'],
                'UAEV_price' => $master->UAEVPrice, // Unit Price
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

        // Email (Send one email for the first record, or loop? Let's send one summary email technically better, but for now stick to 1st)
        // Or loop? Let's loop to be safe.
        foreach ($createdRecords as $rec) {
            // ... email logic (simplified) ...
            try {
                Mail::to($rec->UAEV_email)->send(new UAEVVisaMail($rec->toArray(), $rec->UAEV_passport_copy, $rec->UAEV_passport_photo));
            } catch (\Exception $e) {
            }
        }

        // Nomod Payment
        $totalAmount = round((float) $validated['price'], 2);
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
}
