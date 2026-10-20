<?php

namespace App\Mail;

use App\Models\ReferralAgent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent the moment a freelancer application is submitted — separate from the
 * later approve/reject decision email. Mirrors AgentApplicationReceivedMail.
 */
class FreelancerApplicationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ReferralAgent $agent) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your GoTrips Freelancer Application Has Been Received',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.freelancer_application_received',
            with: ['agent' => $this->agent],
        );
    }
}
