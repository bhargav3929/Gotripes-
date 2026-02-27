<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerPaymentConfirmationMail extends Mailable
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
                    ->view('emails.customer-payment-confirmation')
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
        $orderId = $this->data['order_id'] ?? '';

        switch ($this->paymentStatus) {
            case 'Success':
                return "Booking Confirmed! Your payment is successful — {$orderId}";
            case 'Failed':
                return "Payment Failed — Please retry your booking {$orderId}";
            case 'Cancelled':
                return "Payment Cancelled — Your booking is saved {$orderId}";
            default:
                return "Booking Payment Update — {$orderId}";
        }
    }
}
