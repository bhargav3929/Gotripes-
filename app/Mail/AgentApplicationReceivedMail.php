<?php

namespace App\Mail;

use App\Models\AgentApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent the moment an agent application is submitted — separate from the
 * later approve/reject decision email.
 */
class AgentApplicationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string|null $generatedPassword Plaintext, only set when the
     * applicant didn't choose their own password at registration (the
     * homepage "Create Partner Account" modal no longer asks for one) — this
     * is the only place it's ever visible, since the DB only ever stores the
     * hash.
     */
    public function __construct(
        public AgentApplication $application,
        public ?string $generatedPassword = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your GoTrips Agent Application Has Been Received',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.agent_application_received',
            with: [
                'application' => $this->application,
                'generatedPassword' => $this->generatedPassword,
            ],
        );
    }
}
