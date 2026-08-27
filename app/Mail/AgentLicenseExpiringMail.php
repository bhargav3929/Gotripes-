<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Agent-facing half of the ~30-day trade license expiry warning, sent by
 * the agents:check-license-expiry command. Mirrors B2bPartnerLicenseExpiringMail.
 */
class AgentLicenseExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $agent) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Action Required: Your Trade License Is Expiring Soon',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.agent_license_expiring',
            with: ['agent' => $this->agent],
        );
    }
}
