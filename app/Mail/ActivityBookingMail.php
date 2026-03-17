<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActivityBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bookingData;
    public $isAdminEmail;

    /**
     * Create a new message instance.
     */
    public function __construct($bookingData, $isAdminEmail = false)
    {
        $this->bookingData = $bookingData;
        $this->isAdminEmail = $isAdminEmail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isAdminEmail
            ? 'New Activity Booking Request - ' . $this->bookingData['name']
            : 'Activity Booking Confirmation - ' . ($this->bookingData['activityName'] ?? 'Activity');

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.activity_booking',
            with: [
                'booking' => $this->bookingData,
                'isAdmin' => $this->isAdminEmail,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
