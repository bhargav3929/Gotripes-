<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPaymentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $paymentStatus;
    public $bookingType;

    public function __construct($data, $paymentStatus, $bookingType)
    {
        $this->data = $data;
        $this->paymentStatus = $paymentStatus;
        $this->bookingType = $bookingType;
    }

    public function build()
    {
        $subject = $this->getSubject();

        return $this->subject($subject)
                    ->view('emails.admin-payment-notification')
                    ->with([
                        'data' => $this->data,
                        'paymentStatus' => $this->paymentStatus,
                        'bookingType' => $this->bookingType,
                        'isSuccess' => $this->paymentStatus === 'Success',
                        'isFailed' => $this->paymentStatus === 'Failed',
                        'isCancelled' => $this->paymentStatus === 'Cancelled',
                    ]);
    }

    private function getSubject()
    {
        $type = ucfirst($this->bookingType);
        $orderId = $this->data['order_id'] ?? '';

        switch ($this->paymentStatus) {
            case 'Success':
                return "[PAID] {$type} Booking — {$orderId} — Payment Confirmed";
            case 'Failed':
                return "[FAILED] {$type} Booking — {$orderId} — Payment Failed";
            case 'Cancelled':
                return "[CANCELLED] {$type} Booking — {$orderId} — Payment Cancelled";
            default:
                return "[UPDATE] {$type} Booking — {$orderId}";
        }
    }
}
