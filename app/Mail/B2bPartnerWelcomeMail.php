<?php

namespace App\Mail;

use App\Models\B2bPartner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent the moment a B2B partner application is submitted — separate from
 * the later approve/reject decision email.
 */
class B2bPartnerWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public B2bPartner $partner) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your GoTrips B2B Partner Application Has Been Received',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.b2b_partner_welcome',
            with: ['partner' => $this->partner],
        );
    }
}
