<?php

namespace App\Http\Controllers;

use App\Models\SaudiVisaType;
use App\Models\SaudiVisaApplication;
use App\Models\NomodTransaction;
use App\Services\NomodService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SaudiVisaController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'full_name'           => 'required|string|max:255',
            'email'               => 'required|email|max:255',
            'phone'               => 'required|string|max:30',
            'nationality'         => 'required|string|max:100',
            'saudi_visa_type_id'  => 'required|exists:tbl_saudi_visa_types,id',
            'passport_copy'       => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'additional_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $visaType = SaudiVisaType::where('isActive', 1)->findOrFail($validated['saudi_visa_type_id']);
        $price = $visaType->price;

        try {
            // Handle file uploads
            $passportPath = '';
            if ($request->hasFile('passport_copy')) {
                $passportPath = $request->file('passport_copy')->store('visas/saudi/passports', 'public');
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
                'email'               => $validated['email'],
                'phone'               => $validated['phone'],
                'nationality'         => $validated['nationality'],
                'passport_path'       => $passportPath,
                'additional_doc_path' => $additionalDocPath,
                'price'               => $price,
                'payment_status'      => 'pending',
                'order_id'            => $orderId,
            ]);

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
