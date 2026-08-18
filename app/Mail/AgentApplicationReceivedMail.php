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

    public function __construct(public AgentApplication $application) {}

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
            with: ['application' => $this->application],
        );
    }
}
