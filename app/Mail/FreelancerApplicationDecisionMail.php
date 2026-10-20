<?php

namespace App\Mail;

use App\Models\ReferralAgent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent once a manager approves or rejects a freelancer application.
 * Mirrors AgentApplicationDecisionMail.
 */
class FreelancerApplicationDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @param 'approved'|'rejected' $decision */
    public function __construct(
        public ReferralAgent $agent,
        public string $decision,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->decision === 'approved'
            ? 'Your GoTrips Freelancer Application Has Been Approved'
            : 'Update on Your GoTrips Freelancer Application';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.freelancer_application_decision',
            with: ['agent' => $this->agent, 'decision' => $this->decision],
        );
    }
}
