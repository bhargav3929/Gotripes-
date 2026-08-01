<?php

namespace App\Mail;

use App\Models\FluxirVisaApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Customer-facing e-Visa lifecycle email.
 *
 * One mailable, three moments ($stage):
 *   'submitted' — payment received, application handed to the visa provider
 *   'approved'  — provider confirmed the application (state ApplicationConfirmed)
 *   'rejected'  — provider rejected the application (state ApplicationRejected)
 */
class EvisaCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FluxirVisaApplication $application,
        public string $stage = 'submitted',
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->stage) {
            'approved' => 'Your e-Visa has been approved',
            'rejected' => 'Update on your e-Visa application',
            default    => 'Your e-Visa application is being processed',
        };

        return new Envelope(subject: $subject . ' — ' . $this->application->order_id);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.evisa-customer',
            with: [
                'application' => $this->application,
                'stage'       => $this->stage,
            ],
        );
    }
}
