<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupplierBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $bookingData;
    public string $supplierName;

    /**
     * Create a new message instance.
     *
     * @param array  $bookingData   Booking details (name, email, date, activity info, etc.)
     * @param string $supplierName  The supplier's display name
     */
    public function __construct(array $bookingData, string $supplierName = 'Supplier')
    {
        $this->bookingData  = $bookingData;
        $this->supplierName = $supplierName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Activity Booking Notification - ' . ($this->bookingData['activityName'] ?? 'Activity'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.supplier_booking',
            with: [
                'booking'      => $this->bookingData,
                'supplierName' => $this->supplierName,
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
