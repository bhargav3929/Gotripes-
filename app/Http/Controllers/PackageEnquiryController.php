<?php

namespace App\Http\Controllers;

use App\Mail\BookingNotificationMail;
use App\Models\PackageEnquiry;
use App\Models\TravelPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PackageEnquiryController extends Controller
{
    /**
     * POST /tour-packages/{id}/enquire — capture a customer enquiry for a tour
     * package and email the package's configured recipients (FIFA-style).
     */
    public function submit(Request $request, $id)
    {
        $package = TravelPackage::where('isActive', 1)->findOrFail($id);

        $data = $request->validate([
            'name'        => 'required|string|max:160',
            'email'       => 'required|email|max:160',
            'phone'       => 'nullable|string|max:40',
            'travel_date' => 'nullable|string|max:60',
            'travellers'  => 'nullable|integer|min:1|max:99',
            'message'     => 'nullable|string|max:2000',
        ]);

        $enquiry = PackageEnquiry::create([
            'package_id'    => $package->id,
            'package_title' => $package->title,
            'country'       => $package->country,
            'name'          => $data['name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'] ?? null,
            'travel_date'   => $data['travel_date'] ?? null,
            'travellers'    => $data['travellers'] ?? null,
            'message'       => $data['message'] ?? null,
            'status'        => 'new',
        ]);

        // Notify the package's recipients (falls back to company email). Reply-to
        // the customer so the team can respond directly. Failure must not break
        // the customer's confirmation, so it is logged, not thrown.
        try {
            $recipients = booking_recipients($package->notification_email_list);
            if (!empty($recipients)) {
                Mail::to($recipients)->send(new BookingNotificationMail(
                    heading: 'New tour package enquiry',
                    intro: 'A customer enquired about a tour package.',
                    rows: [
                        'Package'     => $package->title,
                        'Country'     => $package->country,
                        'Name'        => $enquiry->name,
                        'Email'       => $enquiry->email,
                        'Phone'       => $enquiry->phone,
                        'Travel date' => $enquiry->travel_date,
                        'Travellers'  => $enquiry->travellers,
                        'Message'     => $enquiry->message,
                    ],
                    reference: 'PKG-ENQ-' . $enquiry->id,
                    replyToAddress: $enquiry->email,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Package enquiry notification failed', [
                'enquiry_id' => $enquiry->id,
                'error'      => $e->getMessage(),
            ]);
        }

        return back()->with('package_enquiry_success',
            'Thank you! Your enquiry has been sent — our team will contact you shortly.');
    }
}
