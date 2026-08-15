<?php

namespace App\Mail;

use App\Models\B2bPartner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent once a manager approves or rejects a B2B partner application.
 */
class B2bPartnerApplicationDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @param 'approved'|'rejected' $decision */
    public function __construct(
        public B2bPartner $partner,
        public string $decision,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->decision === 'approved'
            ? 'Your GoTrips B2B Partner Application Has Been Approved'
            : 'Update on Your GoTrips B2B Partner Application';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.b2b_partner_decision',
            with: [
                'partner' => $this->partner,
                'decision' => $this->decision,
            ],
        );
    }
}
