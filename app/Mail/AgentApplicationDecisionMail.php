<?php

namespace App\Mail;

use App\Models\AgentApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent once a manager approves or rejects an agent application.
 */
class AgentApplicationDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @param 'approved'|'rejected' $decision */
    public function __construct(
        public AgentApplication $application,
        public string $decision,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->decision === 'approved'
            ? 'Your GoTrips Agent Application Has Been Approved'
            : 'Update on Your GoTrips Agent Application';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.agent_application_decision',
            with: [
                'application' => $this->application,
                'decision' => $this->decision,
            ],
        );
    }
}
