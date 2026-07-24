<?php

namespace App\Http\Controllers;

use App\Jobs\BackfillPassportDetails;
use App\Models\SaudiVisaType;
use App\Models\SaudiVisaApplication;
use App\Models\NomodTransaction;
use App\Services\NomodService;
use App\Services\PassportOcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SaudiVisaController extends Controller
{
    public function index()
    {
        $visaTypes = SaudiVisaType::active()->get();
        return view('saudi-visa', compact('visaTypes'));
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            // Booking and contact details — the only things the customer types.
            'full_name'           => 'required|string|max:255',
            'email'               => 'required|email|max:255',
            'phone'               => 'required|string|max:30',
            'saudi_visa_type_id'  => 'required|exists:tbl_saudi_visa_types,id',

            // Required documents.
            'passport_copy'       => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'passport_photo'      => 'required|file|mimes:jpg,jpeg,png|max:4096',
            'additional_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',

            // Passport details are read from the uploaded passport, not typed:
            // the browser scan posts them in hidden fields and
            // BackfillPassportDetails fills whatever it could not read. All
            // optional — an unreadable passport must never block a booking.
            'first_name'          => 'nullable|string|max:255',
            'last_name'           => 'nullable|string|max:255',
            'nationality'         => 'nullable|string|max:100',
            'passport_number'     => 'nullable|string|max:50',
            'passport_expiry'     => 'nullable|date',
            'dob'                 => 'nullable|date',
            'gender'              => 'nullable|string|max:20',
        ]);

        // Prefer the scanned given/surname split; fall back to splitting the name
        // the customer typed so the applicant is always identifiable.
        $first = trim((string) ($validated['first_name'] ?? ''));
        $last  = trim((string) ($validated['last_name'] ?? ''));
        if ($first === '') {
            [$first, $last] = PassportOcrService::splitFullName($validated['full_name']);
        } elseif ($last === '') {
            $last = $first;
        }

        $visaType = SaudiVisaType::where('isActive', 1)->findOrFail($validated['saudi_visa_type_id']);
        $price = $visaType->price;

        try {
            // Handle file uploads
            $passportPath = '';
            if ($request->hasFile('passport_copy')) {
                $passportPath = $request->file('passport_copy')->store('visas/saudi/passports', 'public');
            }

            $photoPath = '';
            if ($request->hasFile('passport_photo')) {
                $photoPath = $request->file('passport_photo')->store('visas/saudi/photos', 'public');
            }

            $additionalDocPath = null;
            if ($request->hasFile('additional_document')) {
                $additionalDocPath = $request->file('additional_document')->store('visas/saudi/docs', 'public');
            }

            // Generate Order ID (prefix ORDSV to identify Saudi Visa)
            $orderId = 'ORDSV-' . time() . '-' . Str::random(4);

            $application = SaudiVisaApplication::create([
                'company_id'          => current_company()?->id,
                'saudi_visa_type_id'  => $visaType->id,
                'full_name'           => $validated['full_name'],
                'first_name'          => $first,
                'last_name'           => $last,
                'email'               => $validated['email'],
                'phone'               => $validated['phone'],
                // `nationality` is NOT NULL on this table; blank means "not read
                // from the passport yet" and is filled by the backfill job.
                'nationality'         => $validated['nationality'] ?? '',
                'passport_number'     => $validated['passport_number'] ?? null,
                'passport_expiry'     => $validated['passport_expiry'] ?? null,
                'dob'                 => $validated['dob'] ?? null,
                'gender'              => $validated['gender'] ?? null,
                'passport_path'       => $passportPath,
                'photo_path'          => $photoPath,
                'additional_doc_path' => $additionalDocPath,
                'price'               => $price,
                'payment_status'      => 'pending',
                'status'              => 'pending',
                'order_id'            => $orderId,
            ]);

            // Read anything the browser scan missed off the uploaded passport
            // copy, after the response so checkout is not held up.
            if ($passportPath !== '') {
                BackfillPassportDetails::dispatch(
                    SaudiVisaApplication::class,
                    $application->id,
                    $passportPath,
                    [
                        'passport_number' => 'passport_number',
                        'passport_expiry' => 'passport_expiry',
                        'dob'             => 'dob',
                        'gender'          => 'gender',
                        'nationality'     => 'nationality',
                    ],
                )->afterResponse();
            }

            // Call Nomod hosted checkout
            $nomodService = new NomodService();
            $checkout = $nomodService->createCheckout([
                'amount' => $price,
                'currency' => 'AED',
                'order_id' => $orderId,
                'description' => "Saudi Visa Application: {$visaType->name} ({$validated['full_name']})",
                'customer' => [
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                ],
            ]);

            if (!($checkout['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'error' => $checkout['error'] ?? 'Payment gateway checkout initialization failed.',
                ], 500);
            }

            // Save to transaction table
            NomodTransaction::create([
                'company_id'   => current_company()?->id,
                'checkout_id'  => $checkout['checkout_id'],
                'order_id'     => $orderId,
                'status'       => 'created',
                'amount'       => $price,
                'currency'     => 'AED',
                'booking_type' => 'saudi_visa',
                'checkout_url' => $checkout['checkout_url'],
                'metadata'     => [
                    'application_id' => $application->id,
                    'visa_type'      => $visaType->name,
                ],
                'response_data'=> $checkout['data'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'checkout_url' => $checkout['checkout_url'],
                'order_id' => $orderId,
            ]);

        } catch (\Exception $e) {
            Log::error('Saudi Visa submission failed', [
                'error' => $e->getMessage(),
                'visa_type_id' => $visaType->id,
                'price' => $price,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Visa application submission failed. Please try again.',
            ], 500);
        }
    }
}


