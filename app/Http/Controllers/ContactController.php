<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\CustomerMail;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        // Validate input data
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email',
            'phone'          => 'required|string|max:20',
            'booking-city'   => 'required|string|max:100',
            'booking-address' => 'nullable|string',
        ]);
        $fromEmail = config('mail.mailers.smtp.username');

        // Prepare data for the email
        $data = [
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'],
            'booking_city'  => $validated['booking-city'],
            'message'       => $validated['booking-address'] ?? '',
        ];

        // Get primary recipient email from MAIL_USERNAME in .env
        // $toEmail = config('mail.mailers.smtp.username');
        $toEmail = config('mail.mailers.smtp.address');
        // $additionalEmail = 'saideepak.c@vizcheck.com';
        

        // Combine recipients into an array
        $recipients = [$toEmail];

        // Fallback for debugging/testing, remove in production
        if (empty($toEmail)) {
            Log::error('MAIL_USERNAME is not set in config/mail.php or .env');
            $toEmail = 'amer@aynalamirtourism.com'; // fallback email
            $recipients = [$toEmail];
        }

        try {
            // Send email to all recipients in one call
            Mail::to($recipients)->send(new CustomerMail($data,$fromEmail));

            // Log success message with details
            Log::info("Email sent successfully", [
                'to'        => $recipients,
                'from'      => $data['email'],
                'name'      => $data['name'],
                'subject'   => 'Contact form submission',
                'timestamp' => now()->toDateTimeString(),
            ]);

            return redirect()->back()->with('success', 'Your message has been sent!');
        } catch (\Exception $e) {
            // Log error with exception message and input data
            Log::error("Failed to send email", [
                'to'        => $recipients,
                'from'      => $data['email'],
                'error'     => $e->getMessage(),
                'timestamp' => now()->toDateTimeString(),
                'data'      => $data,
            ]);

            return redirect()->back()->with('error', 'Failed to send message. Please try again later.');
        }
    }
}
