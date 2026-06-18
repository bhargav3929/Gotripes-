<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Generic internal "you got a new order" notification for the business team.
 * Reused across booking types that have no bespoke email (eSIM, e-Visa, …).
 * Renders a simple branded table of label => value rows.
 */
class BookingNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  string                $heading    e.g. "New eSIM order"
     * @param  string                $intro      one-line context line
     * @param  array<string,?string> $rows       label => value detail rows
     * @param  string|null           $reference  order/booking reference
     * @param  string|null           $replyToAddress  customer email for quick reply
     */
    public function __construct(
        public string $heading,
        public string $intro,
        public array $rows = [],
        public ?string $reference = null,
        public ?string $replyToAddress = null,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->heading . ($this->reference ? ' — ' . $this->reference : '');

        return new Envelope(
            subject: $subject,
            replyTo: $this->replyToAddress ? [$this->replyToAddress] : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking_notification',
            with: [
                'heading'   => $this->heading,
                'intro'     => $this->intro,
                'rows'      => $this->rows,
                'reference' => $this->reference,
            ],
        );
    }
}
