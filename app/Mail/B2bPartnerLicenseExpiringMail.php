<?php

namespace App\Mail;

use App\Models\B2bPartner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Partner-facing half of the ~30-day trade license expiry warning, sent by
 * the partners:check-license-expiry command. Distinct from the internal
 * BookingNotificationMail alert sent to the business at the same time —
 * the audience and tone differ.
 */
class B2bPartnerLicenseExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public B2bPartner $partner) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Action Required: Your Trade License Is Expiring Soon',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.b2b_partner_license_expiring',
            with: ['partner' => $this->partner],
        );
    }
}
