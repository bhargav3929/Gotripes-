<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\ActivityBooking;
use App\Models\NomodTransaction;
use App\Mail\ActivityBookingMail;
use App\Services\NomodService;

class ActivityBookingController extends Controller
{
    // Display the booking form
    public function show(Request $request)
    {
        $activityId = $request->route('activityId') ?? $request->input('id');

        if (!$activityId) {
            abort(404, 'Activity not specified.');
        }

        // Fetch main activity and detailed info
        $activity = DB::table('tbl_uaeactivities')->where('activityID', $activityId)->first();
        $detail = DB::table('tbl_UAEActivityDetails')->where('activityID', $activityId)->first();

        if (!$activity) {
            abort(404, 'Activity not found.');
        }

        // Retrieve selected currency or default AED
        $currency = $request->input('currency', session('selected_currency', 'AED'));
        session(['selected_currency' => $currency]);

        // Determine prices safely
        $adultPrice = ($activity->activityPrice !== null) ? (float) $activity->activityPrice : 0;
        $childPrice = ($activity->activityChildPrice !== null && $activity->activityChildPrice > 0)
            ? (float) $activity->activityChildPrice : $adultPrice;
        $txnCharges = ($activity->activityTransactionCharges !== null)
            ? (float) $activity->activityTransactionCharges : 0;

        // Pass all data to blade
        return view('dubai-global-village', [
            'activity' => $activity,
            'detail' => $detail,
            'activityId' => $activityId,
            'activityAdultPrice' => $adultPrice,
            'activityChildPrice' => $childPrice,
            'activityTxnCharges' => $txnCharges,
            'currency' => $currency,
            'dubaiPrice' => $activity->dubaiPrice ?? 0,
            'abuDhabiPrice' => $activity->abuDhabiPrice ?? 0,
            // NEW: Add the new transport price fields
            'fromAbuDhabiToDubai' => $activity->fromAbuDhabiToDubai ?? 0,
            'emirates' => $activity->emirates ?? 0,
        ]);
    }

    // API endpoint: Return JSON with transport prices for given activity ID
    public function getActivityPrices(Request $request)
    {
        $activityId = $request->route('activityId') ?? $request->query('id');
        // Log::info("Activity prices requested for ID: " . $activityId);

        if (!$activityId) {
            return response()->json(['error' => 'Activity ID is required'], 400);
        }

        $activity = DB::table('tbl_uaeactivities')->where('activityID', $activityId)->first();
        // Log::info("Activity found: " . ($activity ? 'Yes' : 'No'));

        // if ($activity) {
        //     Log::info("Dubai Price: " . ($activity->dubaiPrice ?? 0) . ", Abu Dhabi Price: " . ($activity->abuDhabiPrice ?? 0));
        // }

        if (!$activity) {
            return response()->json(['error' => 'Activity not found'], 404);
        }

        return response()->json([
            'dubaiPrice' => $activity->dubaiPrice ?? 0,
            'abuDhabiPrice' => $activity->abuDhabiPrice ?? 0,
            // NEW: Add the new transport price fields
            'fromAbuDhabiToDubai' => $activity->fromAbuDhabiToDubai ?? 0,
            'emirates' => $activity->emirates ?? 0,
        ]);
    }

    // API endpoint: Return JSON with activity pricing details
    public function getActivityPricing(Request $request)
    {
        $activityId = $request->route('activityId') ?? $request->query('id');
        // Log::info("Activity pricing requested for ID: " . $activityId);

        if (!$activityId) {
            return response()->json(['error' => 'Activity ID is required'], 400);
        }

        $activity = DB::table('tbl_uaeactivities')->where('activityID', $activityId)->first();

        if (!$activity) {
            return response()->json(['error' => 'Activity not found'], 404);
        }

        return response()->json([
            'activityPrice' => $activity->activityPrice ?? 0,
            'activityChildPrice' => $activity->activityChildPrice ?? ($activity->activityPrice ?? 0),
            'activityTransactionCharges' => $activity->activityTransactionCharges ?? 0,
        ]);
    }

    // Main booking form submission - EXISTING FUNCTIONALITY MAINTAINED
    public function submit(Request $request)
    {
        // Log::info('Activity booking form submission started', [
        //     'input'     => $request->all(),
        //     'timestamp' => now()->toDateTimeString(),
        //     'ip'        => $request->ip(),
        //     'is_ajax'   => $request->ajax(),
        // ]);

        try {
            // Get currency from form (or session, default AED)
            $currency = $request->input('currency', session('selected_currency', 'AED'));
            session(['selected_currency' => $currency]);

            $validated = $request->validate([
                'name'           => 'required|string|max:255',
                'date'           => 'required|date',
                'address'        => 'nullable|string|max:255',
                'nationality'    => 'nullable|string|max:100',
                'email'          => 'required|email',
                'phone'          => 'required|string|max:20',
                'adults'         => 'required|integer',
                'childrens'      => 'required|integer',
                'payment_option' => 'required|string|max:255',
                'transfer'       => 'required|string|max:255',
                'activityId'     => 'required|integer|exists:tbl_uaeactivities,activityID',
                'final_payment_amount' => 'nullable|numeric',
                'currency'       => 'required|string',
                'remarks'        => 'nullable|string|max:500',
            ]);

            // Log::info('Activity booking input validated', [
            //     'validated' => $validated,
            //     'timestamp' => now()->toDateTimeString(),
            //     'payment_option' => $validated['payment_option']
            // ]);

            $activity = DB::table('tbl_uaeactivities')
                ->where('activityID', $validated['activityId'])
                ->first();
            $activityName  = $activity ? $activity->activityName : 'N/A';
            $adultPrice    = $activity ? (float)$activity->activityPrice : 0;
            $childPrice    = ($activity && $activity->activityChildPrice && $activity->activityChildPrice > 0)
                ? (float)$activity->activityChildPrice : $adultPrice;
            $transCharge   = $activity ? (float)$activity->activityTransactionCharges : 0;
            $taxPercent    = 5;

            // UPDATED: Calculate transport charges with new options
            $transportCharges = 0;
            if ($validated['transfer'] === 'abu_dhabi') {
                $transportCharges = (float) ($activity->abuDhabiPrice ?? 0);
            } elseif ($validated['transfer'] === 'dubai') {
                $transportCharges = (float) ($activity->dubaiPrice ?? 0);
            } elseif ($validated['transfer'] === 'abu_dhabi_to_dubai') {
                // NEW: Handle Abu Dhabi to Dubai transport
                $transportCharges = (float) ($activity->fromAbuDhabiToDubai ?? 0);
            } elseif ($validated['transfer'] === 'any_emirates') {
                // NEW: Handle any Emirates transport
                $transportCharges = (float) ($activity->emirates ?? 0);
            }
            // Keep existing logic for 'abu_bhabi' and 'du_bai' for backward compatibility
            elseif ($validated['transfer'] === 'abu_bhabi') {
                $transportCharges = (float) ($activity->abuDhabiPrice ?? 0);
            } elseif ($validated['transfer'] === 'du_bai') {
                $transportCharges = (float) ($activity->dubaiPrice ?? 0);
            }

            // Calculate totals
            $adultTotal = $validated['adults'] * $adultPrice;
            $childTotal = $validated['childrens'] * $childPrice;
            $subtotal = $adultTotal + $childTotal + $transCharge + $transportCharges;
            $finalAmount = round($subtotal, 2);

            // Save to DB
            $booking = new ActivityBooking();
            $booking->isActive       = 1;
            $booking->createdBy      = $validated['name'];
            $booking->createDate     = now();
            $booking->modifiedBy     = null;
            $booking->modifiedDate   = null;
            $booking->activityId     = $validated['activityId'];
            $booking->name           = $validated['name'];
            $booking->date           = $validated['date'];
            $booking->address        = $validated['address'] ?? '';
            $booking->nationality    = $validated['nationality'] ?? '';
            $booking->email          = $validated['email'];
            $booking->phone          = $validated['phone'];
            $booking->adults         = $validated['adults'];
            $booking->childrens      = $validated['childrens'];
            $booking->paymentOption  = $validated['payment_option'];
            $booking->transfer       = $validated['transfer'];
            $booking->amount         = $finalAmount;
            $booking->currency       = $currency;
            $booking->remarks        = $validated['remarks'] ?? '';
            
            // Handle actionType field - add default value to prevent database error
            if (isset($validated['action_type'])) {
                $booking->actionType = $validated['action_type'];
            } else {
                // Set default based on payment option to maintain existing logic
                $booking->actionType = ($validated['payment_option'] === 'book_with_us') ? 'book_only' : 'book_and_pay';
            }
            
            $booking->save();

            // Log::info('Activity booking saved to database', [
            //     'booking'   => $booking->toArray(),
            //     'timestamp' => now()->toDateTimeString(),
            //     'payment_option' => $validated['payment_option'],
            // ]);

            // Prepare mail data
            $mailData = $booking->toArray();
            $mailData['activityName'] = $activityName;
            $mailData['currency']     = $currency;
            $mailData['transportCharges'] = $transportCharges;
            $mailData['taxAmount'] = $taxAmount;
            $mailData['status'] = 'pending';
            
            // Recipients: Always send to user + admins for BOTH payment options
            $recipients = [
                config('mail.from.address'),
                // 'Saideepak.c@vizcheck.com',
                $mailData['email'], // User's email - THIS IS THE KEY ADDITION
            ];

            // Log::info('About to send activity booking email', [
            //     'recipients' => $recipients,
            //     'payment_option' => $validated['payment_option'],
            //     'timestamp' => now()->toDateTimeString(),
            // ]);

            // Send email for BOTH payment options
            try {
                Mail::to($recipients)
                    ->send((new ActivityBookingMail($mailData))
                        ->from(config('mail.from.address'), env('MAIL_FROM_NAME'))
                        ->replyTo($mailData['email'])
                    );
                    
                // Log::info('Activity booking email sent successfully', [
                //     'recipients' => $recipients,
                //     'payment_option' => $validated['payment_option'],
                //     'timestamp' => now()->toDateTimeString(),
                // ]);
                
            } catch (\Exception $emailEx) {
                // Log::error('Failed to send Activity Booking email', [
                //     'error' => $emailEx->getMessage(),
                //     'trace' => $emailEx->getTraceAsString(),
                //     'data'  => $mailData,
                //     'recipients' => $recipients,
                //     'payment_option' => $validated['payment_option'],
                // ]);
                
                // For AJAX requests, return JSON error
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Booking saved but email sending failed. Please contact support.',
                    ]);
                }
                return redirect()->back()->with('error', 'Failed to send your booking email. Please try again later.');
            }

            // "Book With Us" (classic): Email sent, return success
            if ($validated['payment_option'] === 'book_with_us') {
                // Log::info('Processing book_with_us option', [
                //     'booking_id' => $booking->id,
                //     'is_ajax' => $request->ajax(),
                // ]);
                
                // Handle AJAX request
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Your booking has been submitted successfully! Our agent will contact you shortly.',
                        'booking_id' => $booking->id,
                        'action_type' => 'book_with_us'
                    ]);
                }
                
                return redirect()->back()->with('success', 'Your booking has been submitted and a confirmation sent!');
            }

            // "Book and Pay Yourself": Email sent + Show overlay
            if ($validated['payment_option'] === 'book_and_pay_yourself') {
                // Log::info('Processing book_and_pay_yourself option', [
                //     'booking_id' => $booking->id,
                //     'is_ajax' => $request->ajax(),
                // ]);
                
                $adults   = intval($validated['adults']);
                $children = intval($validated['childrens']);
                $adultsTotal   = $adults * $adultPrice;
                $childrenTotal = $children * $childPrice;
                $subTotal = $adultsTotal + $childrenTotal + $transCharge + $transportCharges;
                $tax = round($subTotal * $taxPercent / 100.0, 2);
                $total = round($subTotal + $tax, 2);

                $overlay_values = [
                    'adultPrice' => $adultPrice . " x " . $adults . " = " . $adultsTotal,
                    'childPrice' => $childPrice . " x " . $children . " = " . $childrenTotal,
                    'transCharges' => $transCharge,
                    'transportCharges' => $transportCharges,
                    'tax' => $tax,
                    'total' => $total,
                    'currency' => $currency
                ];
                
                // Handle AJAX request
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Your booking has been submitted and a confirmation sent!',
                        'booking_id' => $booking->id,
                        'show_overlay' => true,
                        'overlay_values' => $overlay_values,
                        'action_type' => 'book_and_pay_yourself'
                    ]);
                }
                
                return redirect()->back()
                    ->with('show_overlay', true)
                    ->with('overlay_values', $overlay_values)
                    ->with('success', 'Your booking has been submitted and a confirmation sent!');
            }

            // Default response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your booking has been submitted and a confirmation sent!',
                ]);
            }
            
            return redirect()->back()->with('success', 'Your booking has been submitted and a confirmation sent!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log::error('Validation error in booking', ['errors' => $e->errors()]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please check all required fields.',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            // Log::error('Booking or email operation failed', [
            //     'error'     => $e->getMessage(),
            //     'trace'     => $e->getTraceAsString(),
            //     'timestamp' => now()->toDateTimeString(),
            //     'input'     => $request->all(),
            // ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit your booking. Please try again later.',
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to submit your booking. Please try again later.');
        }
    }

    // Nomod checkout for activity payment
    public function initiateActivityPayment(Request $request)
    {
        try {
            $bookingId = $request->input('booking_id');
            $amount = $request->input('amount');

            if (!$bookingId || !$amount) {
                return response()->json(['error' => 'Missing booking_id or amount'], 400);
            }

            $booking = ActivityBooking::find($bookingId);
            if (!$booking) {
                return response()->json(['error' => 'Booking not found'], 404);
            }

            $orderId = 'ORDAB' . $bookingId;

            $nomodService = new NomodService();
            $checkout = $nomodService->createCheckout([
                'amount' => round((float) $amount, 2),
                'currency' => 'AED',
                'order_id' => $orderId,
                'description' => 'Activity Booking #' . $bookingId,
                'customer' => [
                    'name' => $booking->name,
                    'email' => $booking->email,
                    'phone' => $booking->phone,
                ],
            ]);

            if (!$checkout['success']) {
                return response()->json(['error' => $checkout['error'] ?? 'Payment initiation failed'], 500);
            }

            NomodTransaction::create([
                'checkout_id' => $checkout['checkout_id'],
                'order_id' => $orderId,
                'status' => 'created',
                'amount' => round((float) $amount, 2),
                'currency' => 'AED',
                'booking_type' => 'activity',
                'checkout_url' => $checkout['checkout_url'],
                'customer' => ['name' => $booking->name, 'email' => $booking->email, 'phone' => $booking->phone],
                'response_data' => $checkout['data'] ?? null,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Activity booking payment initiated successfully.',
                    'price' => $amount,
                    'checkout_url' => $checkout['checkout_url'],
                    'orderId' => $orderId,
                ]);
            }

            return redirect()->back()->with([
                'success' => 'Activity booking payment initiated successfully.',
                'price' => $amount,
            ]);

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Payment initiation failed'], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Payment initiation failed']);
        }
    }

}
